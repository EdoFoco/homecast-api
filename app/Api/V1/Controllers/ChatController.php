<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Api\V1\Requests\CreateChatRequest;
use App\Api\V1\Requests\CreateMessageRequest;
use App\Services\ChatRepository;
use App\Services\UserRepository;
use Dingo\Api\Routing\Helpers;

class ChatController extends Controller
{
    use Helpers;

    protected $chatRepository;
    protected $userRepository;

    public function __construct(ChatRepository $chatRepository, UserRepository $userRepository)
    {
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
    }

    public function create(CreateChatRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $participantIds = $request->input('participants');

        if(!in_array($user->id, $participantIds)){
            throw new UnauthorizedHttpException("Unauthorized");
        }
        
        $participants = $this->userRepository->findMany($participantIds);
        $foundParticipantIds = $participants->pluck('id')->all();

        $nonExistingIds = array_diff($participantIds, $foundParticipantIds);

        if(count($nonExistingIds) > 0){
            throw new BadRequestHttpException("One or more participants do not exist.");
        }

        $conversation = $this->chatRepository->createConversation($participantIds);
        
        return response()->json($conversation, 201);
   }

   public function getUserChats(JWTAuth $JWTAuth){

        $user = $JWTAuth->toUser();
        if(!$user){
            throw new NotFoundHttpException("User not found");
        }

        return $this->chatRepository->getUserChats($user);
   }

   public function createMessage($conversationId, CreateMessageRequest $message, JWTAuth $JWTAuth){

        $user = $JWTAuth->toUser();
        
        $conversation = $this->chatRepository->getConversation($conversationId);
        if(!$conversation){
            throw new NotFoundHttpException();
        }

        $usersInConversation = $conversation->users->pluck('id')->all();
        if(!in_array($user->id, $usersInConversation)){
            throw new UnauthorizedHttpException("");
        }

        $this->chatRepository->createMessage($user, $conversation, $message->message);

        return $this->response->created();
    }

    public function getMessages($conversationId, JWTAuth $JWTAuth){

        $user = $JWTAuth->toUser();
        
        $conversation = $this->chatRepository->getConversation($conversationId);
        if(!$conversation){
            throw new NotFoundHttpException();
        }

        $usersInConversation = $conversation->users->pluck('id')->all();
        if(!in_array($user->id, $usersInConversation)){
            throw new UnauthorizedHttpException("");
        }

        return $this->chatRepository->getMessages($user, $conversation);
   }
}