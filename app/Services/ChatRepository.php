<?php

namespace App\Services;

use Chat;
use App\Notifications\MessageNotification;
use Musonza\Chat\Conversations\ConversationUser;

use Musonza\Chat\Conversations\Conversation;
use App\Jobs\PushNotificationJob;
use Illuminate\Support\Facades\DB;

class ChatRepository
{
    public function createConversation($participants){
        return Chat::createConversation($participants); 
    }

    public function getUserChats($user, $senderNameQuery, $limit = 25){
        //Laravel autodetects the current page through the 'page' query string parameter
        $rawConversationsQuery = DB::table('mc_conversations')
        ->join('mc_conversation_user', 'mc_conversations.id', '=', 'mc_conversation_user.conversation_id')
        ->where('mc_conversation_user.user_id', '=', $user->id)
        ->select('mc_conversations.*', 'mc_conversation_user.wasRead',
            DB::raw('(select body from mc_messages where conversation_id  =   mc_conversations.id order by created_at desc limit 1) as last_message')
        )
        ->orderBy('mc_conversations.updated_at', 'desc');
        
        $rawConversations = $senderNameQuery ? $rawConversationsQuery->paginate() : $rawConversationsQuery->paginate($limit);
        $conversationsData = [];
        $conversationsData['current_page'] = $rawConversations->currentPage();
        $conversationsData['last_page'] = $rawConversations->lastPage();
        $conversationsData['next_page_url'] = $rawConversations->nextPageUrl();
        $conversationsData['data'] = [];
        
        foreach($rawConversations as $conversation){
            $participants = DB::table('mc_conversation_user')
            ->join('users', 'mc_conversation_user.user_id', '=', 'users.id')
            ->where('mc_conversation_user.conversation_id', '=', $conversation->id)
            ->select('users.id', 'users.name', 'users.profile_picture')
            ->get();

            $filteredParticipants = $participants;
            if($senderNameQuery){
                $filteredParticipants = $participants->filter(function($participant) use ($user, $senderNameQuery){
                    return $participant->id != $user->id && strpos($participant->name, $senderNameQuery) !== false;
                })->first();
            }

            $data = [
                'id' => $conversation->id,
                'wasRead' => $conversation->wasRead,
                'last_message' => $conversation->last_message,
                'updated_at' => $conversation->updated_at,
                'users' => $participants
            ];

            if(!$conversation->last_message || count($filteredParticipants) == 0){
                continue;
            }
            
            array_push($conversationsData['data'], $data);
        }
        

        return $conversationsData;
    }

    public function getConversation($id){
        return Chat::conversation($id);
    }

    public function createMessage($user, $conversation, $message){
        
        $message = Chat::message($message)
            ->from($user)
            ->to($conversation)
            ->send(); 
        
        DB::table('mc_conversation_user')
        ->where('conversation_id', '=', $conversation->id)
        ->where('user_id', '!=', $user->id)
        ->update(['wasRead' => false]);

        $participantIds = $conversation->users()
        ->where('id', '!=', $user->id)
        ->whereNotNull('device_token')
        ->get()->pluck('device_token');

        $data = [
            'path' => 'homecastapp://chats/'.$conversation->id
        ];

        PushNotificationJob::dispatch($user->name, $message, $participantIds, $data);

        return $message;
    }

    public function getMessages($user, $conversation, $page = 1, $limit = 25){
       $this->readConversation($user, $conversation);
       return $conversation->getMessages($user, $limit, $page, 'desc');
    }

    private function readConversation($user, $conversation){
        DB::table('mc_conversation_user')
        ->where('user_id', '=', $user->id)
        ->where('conversation_id', '=', $conversation->id)
        ->update(['wasRead' => true]);
    }

}