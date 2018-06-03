<?php

namespace App\Services;

use Chat;
use App\Notifications\MessageNotification;
use Musonza\Chat\Conversations\ConversationUser;

use Musonza\Chat\Conversations\Conversation;
//use Musonza\Chat\Conversations\Chat;
use App\Jobs\PushNotificationJob;
use Illuminate\Support\Facades\DB;

class ChatRepository
{
    public function createConversation($participants){
        return Chat::createConversation($participants); 
    }

    public function getUserChats($user, $page = 1, $limit = 25){
        $rawConversations = DB::table('mc_conversations')
        ->join('mc_conversation_user', 'mc_conversations.id', '=', 'mc_conversation_user.conversation_id')
        ->where('mc_conversation_user.user_id', '=', $user->id)
        ->select('mc_conversations.*', 'mc_conversation_user.wasRead')->get();

        $conversationsData = [];
        foreach($rawConversations as $conversation){
            $participants = DB::table('mc_conversation_user')
            ->join('users', 'mc_conversation_user.user_id', '=', 'users.id')
            ->where('mc_conversation_user.conversation_id', '=', $conversation->id)
            ->select('users.id', 'users.name', 'users.profile_picture')
            ->get();

            $lastMessage = DB::table('mc_messages')
            ->join('users', 'mc_messages.user_id', '=', 'users.id')
            ->where('conversation_id', '=', $conversation->id)
            ->select('mc_messages.id', 'mc_messages.body', 'users.id AS sender_id', 'users.name AS sender_name', 'users.profile_picture AS sender_profile_picture', 'mc_messages.created_at')
            ->orderBy('mc_messages.created_at', 'DESC')
            ->first();
            
            $data = [
                'id' => $conversation->id,
                'wasRead' => $conversation->wasRead ,
                'users' => $participants,
                'last_message' => $lastMessage
            ];

            if(!$lastMessage){
                continue;
            }
            
            array_push($conversationsData, $data);
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

    public function getMessages($user, $conversation, $limit = 25, $page = 1){
       
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