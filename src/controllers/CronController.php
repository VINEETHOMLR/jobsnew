<?php

namespace src\controllers;

use inc\Raise;
use inc\Controller;
use src\lib\Router;
use src\models\User;


class CronController extends Controller {

    protected $needAuth = false;
    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');

        $this->usermdl = (new User);
    }
    public function actionUnblockUsers(){
        
        $time = time();

        $blocked_users = $this->usermdl->callSql("SELECT * FROM user WHERE ($time - blocked_time >= 86400) AND status =0","rows");
        $this->usermdl->query("UPDATE user SET status =1,fail_login_count ='',blocked_time='' WHERE ($time - blocked_time >= '86400') AND status ='0'");
        $this->usermdl->execute();
       
    }

    

}
?>
