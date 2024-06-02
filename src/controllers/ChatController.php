<?php

namespace src\controllers;

use inc\Controller;
use inc\Raise;
use src\lib\Router;
use src\lib\Helper;
use src\lib\Secure;
use src\lib\RRedis;
use src\lib\ValidatorFactory;
use src\models\User;
use src\models\Jobs;
use src\models\Chat;




class ChatController extends Controller
{
    
    protected $needAuth = true;
    protected $authExclude = [];

    public function __construct()
    {
        parent::__construct();
        $this->usermdl = (new User);
        $this->jobs = (new Jobs);
        $this->chats = (new Chat);
    }

    
    public function actionGetEmployerChatMessages()
    {

        $input        = $_POST;
        $userObj      = Raise::$userObj;
        $role_id      = $userObj['role_id'];
        $jobseeker_id = $_POST['jobseeker_id'];
        $post_id = $_POST['post_id'];
        $params = [];
        $params['employer_id']  = $userObj['id'];
        $params['jobseeker_id'] = $jobseeker_id;
        $params['post_id'] = $post_id;
        $chat_id = $this->chats->CreateChat($params);

        $params = [];
        $params['chat_id'] = $chat_id;
        $chatList = $this->chats->getMessages($params);
        $data         = ['messageList'=>$chatList];
        return $this->renderAPI($data, 'Success', 'false', 'S01', 'true', 200);

    }

    public function actionGetJobseekerChatMessages()
    {

        $input        = $_POST;
        $userObj      = Raise::$userObj;
        $role_id      = $userObj['role_id'];
        $employer_id  = $_POST['employer_id'];
        $post_id = $_POST['post_id'];
        $params = [];
        $params['employer_id']  = $employer_id;
        $params['jobseeker_id'] = $userObj['id'];
        $params['post_id'] = $post_id;
        $chat_id = $this->chats->CreateChat($params);

        $params = [];
        $params['chat_id'] = $chat_id;
        $chatList = $this->chats->getMessages($params);
        $data         = ['messageList'=>$chatList];
        return $this->renderAPI($data, 'Success', 'false', 'S01', 'true', 200);

    }

    public function actionSendMessage()
    {

        $input        = $_POST;
        $userObj      = Raise::$userObj;
        $chat_id      = $input['chat_id'];
        $from_id      = $input['from_id'];
        $to_id        = $input['to_id'];
        $message      = $input['message'];

        if(empty($chat_id)) {
            return $this->renderAPIError('Please pass chat id to proceed','');   
        }

        if(empty($from_id)) {
            return $this->renderAPIError('Please pass from id  to proceed','');   
        }
        if(empty($to_id)) {
            return $this->renderAPIError('Please pass to id  to proceed','');   
        }

        if(empty($message)) {
            return $this->renderAPIError('Please enter message  to proceed','');   
        }

        $params = [];
        $params['chat_id'] = $chat_id;
        $params['to_id']   = $to_id;
        $params['from_id'] = $from_id;
        $params['message'] = $message;

        if($this->chats->sendMessage($params)){

            return $this->renderAPI([], 'Success', 'false', 'S01', 'true', 200);    
        }
        return $this->renderAPIError('Something went wrong','');   


        


    }

    public function actiongetChatList()
    {

        $input          = $_POST;
        $userObj        = Raise::$userObj;
        $type           = issetGet($input,'type',''); //1-employer,2-jobseeker
        $search_keyword = issetGet($input,'search_keyword','');

        $params = [];
        $params['type']           = $type;
        $params['search_keyword'] = $search_keyword;
        $params['user_id'] = $userObj['id'];
        $chatList = $this->chats->getChatList($params);
        $data         = ['chatList'=>$chatList];
        return $this->renderAPI($data, 'Success', 'false', 'S01', 'true', 200);






    }

    



}
