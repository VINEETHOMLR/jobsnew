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
