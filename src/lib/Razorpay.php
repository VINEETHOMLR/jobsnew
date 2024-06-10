<?php

namespace src\lib;

use inc\Raise;
use src\lib\RRedis;
use src\lib\Database;
use src\models\User;

class Razorpay extends Database
{
    public function __construct($db = 'db')
    {
        parent::__construct(Raise::params()[$db]);
        $this->rds = new RRedis();
        $this->logId = '';
        $this->url = ['1'=>'contacts','2'=>'fund_accounts','3'=>'fund_accounts/','4'=>'orders/','5'=>'payments/'];
        $this->user = new User();
        
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
       $response = $this->callcurl($request_params,RAZORPAY_URL.$this->url['1'],'POST');

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
        $this->user->query($sql);
        $this->user->execute();

    }

    public function createBankAccount($params)
    {

        $request_params = [];
        $request_params['contact_id']        = $params['contact_id'];
        $request_params['account_number']    = $params['account_number'];
        $request_params['ifsc']              = $params['ifsc'];
        $request_params['beneficiary_name']  = $params['beneficiary_name'];
        //$request_params['account_type']      = $params['account_type'];



        $log_params = [];
        $log_params['contact_id']        = $params['contact_id'];
        $log_params['account_number']    = $params['account_number'];
        $log_params['ifsc']              = $params['ifsc'];
        $log_params['beneficiary_name']  = $params['beneficiary_name'];
       // $log_params['account_type']      = $params['account_type'];
        $log_params['log_type']          = '2'; //1-create bankaccount
        $log_params['user_id']           = $params['user_id'];

        $this->insertLog($log_params);
        $response = $this->callcurl($request_params,RAZORPAY_URL.$this->url['2'],'POST');

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
            $this->user->query($sql);
            $this->user->execute();

            $sql = "INSERT INTO user_bank SET user_id='$params[user_id]',account_id='$account_id',status='1',created_at='$time',updated_at='$time'";
            $this->user->query($sql);
            $this->user->execute();


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
        $sql = "SELECT id,account_id FROM user_bank WHERE user_id='$user_id' AND status='1' ORDER BY id DESC LIMIT 1";
        $details = $this->user->callsql($sql,'row');
        $account_id = $details['account_id'];
        $id         = $details['id'];

        $callurl = RAZORPAY_URL.$this->url['3'].$account_id;
        $response = $this->getCallCurl($callurl);
        $response  = json_decode($response,true);
        $status = false;
        $bankDetails = [];
        if(array_key_exists('id', $response)) {

            $status = true;  
            $bankDetails['id'] = $id;
            $bankDetails['account_number'] = $response['account_number'];
            $bankDetails['ifsc'] = $response['ifsc'];
            $bankDetails['account_holder'] = $response['name'];

        }

        return ['status'=>$status,'bankDetails'=>$bankDetails];



        


    }

    public function createOrder($params)
    {

       $request_params = [];
       $request_params['amount']            = $params['amount'];
       $request_params['receipt']           = $params['receipt'];
       $request_params['currency']          = "INR";
       $request_params['payment_capture']   = "1";
       

       $log_params = [];
       $log_params['amount']            = $params['amount'];
       $log_params['receipt']           = $params['receipt'];
       $log_params['currency']          = "INR";
       $log_params['payment_capture']   = "1";
       $log_params['log_type']  = '3'; //1-create account
       $log_params['user_id']   = $params['user_id'];

       $this->insertLog($log_params);


       
       $responseapi = $this->callcurl($request_params,RAZORPAY_URL.$this->url['4'],'POST');
        if(ENV == 'dev'){

           $responseapi = '{
              "id": "order_EJh5rkJBRh1u1B",
              "entity": "order",
              "amount": 50000,
              "amount_paid": 0,
              "amount_due": 50000,
              "currency": "INR",
              "receipt": "rcptid_11",
              "status": "created",
              "attempts": 0,
              "notes": [],
              "created_at": 1595677116
            }';

        }

       
       
       $this->updateLog($responseapi);
       $response = json_decode($responseapi,true);

       $status     = false;
       $order_id = '';

       if(array_key_exists('id',$response)) {

           $status = true;
           $order_id = $response['id'];
           $message = 'Successfully created order created';

       }else{

          $status  = false; 
          $message = '';

       }

       $return = [];
       $return['status']        = $status;
       $return['order_id']      = $order_id;
       $return['message']       = $message;
       $return['response']      = $responseapi;

       return $return;

    }

    public function verifyPayment($params)
    {

       $request_params = [];
       $razorpay_order_id            = $params['razorpay_order_id'];
       $razorpay_payment_id          = $params['razorpay_payment_id'];
       $razorpay_signature           = $params['razorpay_signature'];

       $log_params = [];
       $log_params['razorpay_order_id']            = $params['razorpay_order_id'];
       $log_params['razorpay_payment_id']          = $params['razorpay_payment_id'];
       $log_params['razorpay_signature']           = $params['razorpay_signature'];
       
       $log_params['log_type']  = '4'; //1-verify signature
       $log_params['user_id']   = $params['user_id'];

       $this->insertLog($log_params);

       $url = RAZORPAY_URL.$this->url['5'].$razorpay_payment_id;

       $response = $this->getCallCurl($url);

       $this->updateLog($response);
        

       $payment_details = json_decode($response, true);

       //$generated_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, RAZORPAY_SECRET);
    
       if (isset($payment_details['status']) && $payment_details['status'] == 'captured') {

           $status = true;
           $message = 'Successfully verified payment';

       }else{

          $status  = false; 
          $message = $payment_details['status'];

       }

       $return = [];
       $return['status']        = $status;
       $return['message']       = $message;

       return $return;

    }

    public function getCallCurl($url)
    {

        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY . ':' . RAZORPAY_SECRET);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for simplicity

        // Execute cURL request
        return $response = curl_exec($ch);

       

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
        $type    = $log_params['log_type'];
        $time    = time();

        $sql  =  "INSERT INTO pg_log SET request='$request',response='',user_id='$user_id',type='$type',created_at='$time',updated_at='$time'";
        $this->user->query($sql);
        $this->user->execute();
        $this->logId = $this->user->lastInsertId();



    }

    public function updateLog($log_params)
    {

        $response = $log_params;
        $time    = time();

        $sql  =  "UPDATE pg_log SET response='$response',updated_at='$time' WHERE id='$this->logId'";
        $this->user->query($sql);
        $this->user->execute();
       



    }



   
}
