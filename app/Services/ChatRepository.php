<?php

namespace App\Services;

use Chat;
use Musonza\Chat\Conversations\ConversationUser;

class ChatRepository
{
    public function createConversation($participants){
        return Chat::createConversation($participants); 
    }

    public function getUserChats($userId){
        return ConversationUser::with('conversation.last_message')->where('user_id', '=', $userId)->get()->pluck('conversation'); 
    }

    public function getConversation($id){
        return Chat::conversation($id);
    }

    public function createMessage($user, $conversation, $message){
        Chat::message($message)
            ->from($user)
            ->to($conversation)
            ->send(); 
    }

    public function getMessages($user, $conversation, $limit = 25, $page = 1){
       Chat::conversations($conversation)->for($user)->readAll();
       return $conversation->getMessages($user, $limit, $page, 'desc');
    }

}