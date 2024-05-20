<?php

namespace src\auth;

use inc\Raise;
use src\lib\Helper as H;
use src\lib\RRedis;
use src\lib\Database;
use src\models\UserTokenList;
use src\models\User;
//use src\models\Driver_user;

class Auth
{
    // private $userID = '';

    // private $token = '';

    // private $tokenValidty = '+ 30 minutes';

    private $tokenValidty = '+ 60 minutes';
    // private $rememberTokenValidty = '+7 day';
    //for redis depend on tokenValidty
    private $tokenValidtyInSecond = 0;

    public function __construct()
    {
        $tokenExpiryAt = strtotime($this->tokenValidty);
        $this->tokenValidtyInSecond = $tokenExpiryAt - time();
    }

    private function authByDb($redisKey)
    {

        $role = substr($redisKey, 0, 2);
        $token = substr($redisKey, 3); 
        if ($role == 'ut') {



            $user_id = (new UserTokenList)->getTokenUserId($token);




            if ($user_id <= 0) {
                return false;
            }

            $userInfo = (new User)->findByPK($user_id)->convertArray();

           

            if (empty($userInfo)) {
                 return false;
            }

           // $resultLog = Raise::callApi(H::buildParam(MICRO_SERVICES['log'], 'playerLoginLog/getByToken', ['token' => $token]));
        } 
        else {
            return false;
        }

        // if (!isset($resultLog[0]['auth_json'])) {
        //     return false;
        // }
        // $playerVal = $resultLog[0]['auth_json'];
        // $playerArr = json_decode($playerVal, true);

        return $userInfo;
    }

    /**
     * Auth by token (THIS function require refactor to clear up)
     *
     * @param string $token
     * @param boolean $byPass
     * @return mixed $playerVal | false
     */
    public function authApiToken()
    {
        global $requestApiToken;
        $playerArr = [];
        $playerVal = [];
        $requestHeader = getallheaders_new();

        if (!empty($requestHeader)) {
            Raise::initReqHeader($requestHeader);
        }

        if (isset($requestHeader['Language'])) {
            // $playerArr['lang'] = $requestHeader['Language'];
            Raise::initLang($requestHeader['Language']);

        }

        if (isset($requestHeader['Bypass'])) {
            // $playerArr['lang'] = $requestHeader['Language'];
            return $this->byPassAssign($requestHeader);

        }

        if (!isset($requestHeader['Token']) || empty($requestHeader['Token'])) {
            return false;
        }

        $redisKey = $requestHeader['Token']; 
        $redisObj = new RRedis;


        //IF redis down
        if (!$redisObj->checkConn()) { 
            $playerArr = $this->authByDb($redisKey);     
            if(!$playerArr)
                return false;
            $playerArr['lastSeen'] = time(); //
            if (isset($requestHeader['Ip'])) {
                $playerArr['ip'] = $requestHeader['Ip'];
            }
        }

        if ($redisObj->exists($redisKey)) {
            $playerVal = $redisObj->get($redisKey);
            $playerArr = json_decode($playerVal, true);
            $tokenValidtyInSecond = $this->tokenValidtyInSecond;
            $playerArr['lastSeen'] = time();
            if (isset($requestHeader['Ip'])) {
                $playerArr['ip'] = $requestHeader['Ip'];
            }
            $result = $redisObj->set($redisKey, $playerArr, $tokenValidtyInSecond);

            // return $playerVal;
            // here lack of if redis not match but db having record (when user login redis down but then redis up)
        } else {
            if($redisObj->checkConn())
                return false;
        }

        Raise::init($playerArr);
        $requestApiToken = $requestHeader['Token'];

        return $playerVal;

    }

    private function byPassAssign($requestHeader)
    {

        $byPassUser = json_decode($requestHeader['Bypass-User'], true);

        if (!empty($byPassUser) && isset($byPassUser['id']) && isset($byPassUser['ip'])) {
            Raise::init($byPassUser);
            return $byPassUser;

        }

        return false;

    }

}