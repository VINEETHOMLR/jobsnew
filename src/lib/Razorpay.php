<?php

namespace src\lib;

use inc\Raise;
use src\lib\RRedis;
use src\lib\Database;

class Razorpay extends Database
{
    public function __construct($db = 'db')
    {
        parent::__construct(Raise::params()[$db]);
        $this->rds = new RRedis();
        $this->logId = '';
        $this->url = ['1'=>'https://api.razorpay.com/v1/contacts','2'=>'https://api.razorpay.com/v1/fund_accounts'];
        
    }

    public function createAccount($params)
    {

       $request_params = [];
       $request_params['name']  = $params['name'];
       $request_params['email'] = $params['email'];
       $request_params['type']  = $params['type'];
       

       $log_params = [];
       $log_params['name']      = $params['name'];
       $log_params['email']     = $params['email'];
       $log_params['type']      = $params['type'];
       $log_params['log_type']  = '1'; //1-create account
       $log_params['user_id']   = $params['user_id'];

       $this->insertLog($log_params);
       $response = $this->callcurl($request_params,$this->url['1'],'POST');
       $this->updateLog($response);
       $response = json_decode($response,true);

       $status     = false;
       $contact_id = '';
       if(array_key_exists('id',$response)) {

           $status = true;
           $contact_id = $response['id'];
           $contactParams = [];
           $contactParams['contact_id'] = $contact_id;
           $contactParams['user_id']    = $params['user_id'];
           $this->insertContact($contactParams);
           $message = 'Successfully created connect';


       }else{

          $status  = false; 
          $message = '';

       }

       $return = [];
       $return['status']     = $status;
       $return['connect_id'] = $contact_id;
       $return['message']    = $message;
       return $return;



    }

    public function insertContact($params)
    {

        $contact_id = $params['contact_id'];
        $user_id    = $params['user_id'];
        $updated_at = time();

        $sql = "UPDATE user SET connect_id='$contact_id',updated_at='$updated_at' WHERE id='$user_id'";
        $this->query($sql);
        $this->execute();

    }

    public function createBankAccount($params)
    {

        $request_params = [];
        $request_params['contact_id']        = $params['contact_id'];
        $request_params['account_number']    = $params['account_number'];
        $request_params['ifsc']              = $params['ifsc'];
        $request_params['beneficiary_name']  = $params['beneficiary_name'];
        $request_params['account_type']      = $params['account_type'];



        $log_params = [];
        $log_params['contact_id']        = $params['contact_id'];
        $log_params['account_number']    = $params['account_number'];
        $log_params['ifsc']              = $params['ifsc'];
        $log_params['beneficiary_name']  = $params['beneficiary_name'];
        $log_params['account_type']      = $params['account_type'];
        $log_params['log_type']          = '2'; //1-create bankaccount
        $log_params['user_id']           = $params['user_id'];

        $this->insertLog($log_params);
        $response = $this->callcurl($request_params,$this->url['2'],'POST');
        $this->updateLog($response);
        $response = json_decode($response,true);

        $status     = false;
        $account_id = '';
        if(array_key_exists('id',$response)) {

            $status  = true;
            $message = "Successfully added bank";
            $account_id = $response['id'];

            $time = time();

            $sql = "UPDATE user_bank SET status='2',updated_at='$time' WHERE user_id='$params[user_id]' AND status='1'";
            $this->query($sql);
            $this->execute();

            $sql = "INSERT INTO user_bank SET user_id='$params[user_id]',account_id='$account_id',status='1',created_at='$time',updated_at='$time'";
            $this->query($sql);
            $this->execute();


        }
        if(array_key_exists('error',$response)) {

            $status  = false;
            $message = $response['error']['description'];

        }

        $return = [];
        $return['status'] = $status;
        $return['message'] = $message;
        return $return;


    }

    public function getBank($params)
    {

        $user_id = $params['user_id'];
        $sql = "SELECT account_id FROM user_bank WHERE user_id='$user_id' AND status='1' ORDER BY id DESC LIMIT 1";
        $account_id = $this->callsql($sql,'value');
        


    }



    public function callcurl($request,$url,$method)
    {


        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url, // Razorpay API endpoint for creating a contact
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => json_encode($request), // Convert data to JSON format
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("".RAZORPAY_KEY.":".RAZORPAY_SECRET.""), // Authorization header with API key and secret
        ),
        ));

        return $response = curl_exec($curl);

    }



    public function insertLog($log_params)
    {

        $request = json_encode($log_params);
        $user_id = $log_params['user_id'];
        $type    = $log_params['type'];
        $time    = time();

        $sql  =  "INSERT INTO pg_log SET request='$request',response='',user_id='$user_id',type='$type',created_at='$time',updated_at='$time'";
        $this->query($sql);
        $this->execute();
        $this->logId = $this->lastInsertId();



    }

    public function updateLog($log_params)
    {

        $response = $log_params;
        $time    = time();

        $sql  =  "UPDATE pg_log SET response='$response',updated_at='$time' WHERE user_id='$this->logId'";
        $this->query($sql);
        $this->execute();
       



    }



   
}
