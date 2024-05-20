<?php

namespace src\lib;
use inc\Raise;
use src\lib\Database;
use src\models\UserExecutedDealHistory;
use src\models\ApiCallLog;

class TradeApiClass extends Database{

	public function __construct($db = 'db')
    {
        parent::__construct(Raise::params()[$db]);
        //$this->api_token 	 = TRADE_API_TOKEN;
        
        $this->api_user_id 	 = TRADE_API_USER_ID;
        
        if(ENV == 'live'){    
            $this->api_url          = TRADE_API_HTTP_URL;
            $this->api_auth_code    = TRADE_API_AUTH_CODE;
        }else{
            $this->api_url          = 'https://fin.sgcomp-tech.com/api';
            $this->api_auth_code    = 'CgAny7_Dqm';
        }

        $this->deals 		 = new UserExecutedDealHistory;
        $this->api_log		 = new ApiCallLog();
        $this->api_token 	 = $this->serverAuth();
    }


    public function serverAuth(){

    	$cmd 			= 'auth.login';

    	$end_api_url	= 'auth';
    	
    	$request		=  array(
    								'cmd' => $cmd,
    								'user_id'=>$this->api_user_id,
    								'auth_code'=>$this->api_auth_code
    						);  

    	$req_id 		= $this->api_log->insertLog(1, 0, 10, json_encode($request));

    	$response 		= $this->callCurl($request,$end_api_url);

    	$this->api_log->updateLog($req_id, $response, $response);

    	$decoded_response 		= json_decode($response,true);

    	$error			= $decoded_response['error'];

    	if(is_array($error)){ // login token failed 
    		$error_response  = ['status' => false,'message'=> 'API Error'];
            return json_encode($error_response);
    	}	

    	$result			= $decoded_response['result'];

    	return  $result['api_token']; 

    }

    public function createExecutedDeals($start_offset,$market){
    	

    	$cmd 			= 'deals'; 
    	
    	$offset 		= $start_offset;

    	$limit			= 100;

    	$end_api_url	= 'exchange';

    	$request		=  array(	'cmd' => $cmd,
    								'market'=>$market,
    								'offset'=>$offset,
    								'limit'=>$limit,
    								'user_id'=>$this->api_user_id,
    								'api_token'=>$this->api_token
    						); 

    	$req_id 		= $this->api_log->insertLog(1, 0, 21, json_encode($request));

    	$response 		= $this->callCurl($request,$end_api_url);

    	$this->api_log->updateLog($req_id, $response, '');
    	
    	$decoded_response 		= json_decode($response,true);

    	$error			= $decoded_response['error'];

    	if(is_array($error)){ // login token failed 
    		die('API error '); 			
    	}

    	$result			= $decoded_response['result'];

    	$records		= $result['records'];

    	$total_count	= count($records) or 0;
    	
    	$ipArray		= [];

    	foreach ($records as $raw) {
    		
    		$ipArray['deal_order_id']	= $raw['deal_order_id'];
    		$ipArray['user_id']			= $raw['user'];
    		$ipArray['deal_id']			= $raw['id'];
    		$ipArray['side']			= $raw['side'];
    		$ipArray['role']			= $raw['role'];
    		$ipArray['executed_price']	= $raw['price'];
    		$ipArray['amount']			= $raw['amount'];
    		$ipArray['deal_price']		= $raw['deal'];
    		$ipArray['fee']				= $raw['fee'];
    		$ipArray['market']			= $raw['market'];
    		$ipArray['order_time']		= (int)$raw['time'];

    		$deal_id = $this->deals->checkIdExists($raw['id']);

    		if(empty($deal_id)){
    			$this->deals->insert($ipArray);
    		}
    		else{
    			return 0;
    		}

    	}
	    
	    if($total_count < $limit){
    		return 0;
    	}else{
    		$offset 	= $start_offset + $limit;
    		$this->createExecutedDeals($offset,$market);
    	}

    }

    public function getOpenOrders($start_offset,$market,$order_ids = []){

    	$cmd 			= 'open'; 

    	$offset 		= $start_offset;

    	$limit			= 100;

    	$end_api_url	= 'exchange';

    	$request 		= array(	'cmd' => $cmd,
    								'market'=>$market,
    								'user_id'=>$this->api_user_id,
    								'offset'=>$offset,
    								'limit'=>$limit,
    								'api_token'=>$this->api_token
    					  );
    	
    	if(ENV 			!= 'live'){ // development 
    		return $total_open_orders = [8692067,8672513,8590117];
    	}

    	$req_id 		= $this->api_log->insertLog(1, 0, 22, json_encode($request));

    	$response 		= $this->callCurl($request,$end_api_url);

    	$this->api_log->updateLog($req_id, $response, '');
    	
    	$decoded_response 		= json_decode($response,true);

    	$error			= $decoded_response['error'];

    	if(is_array($error)){ // login token failed 
    			
    		die('API Error'); 	

    	}

    	$result			= $decoded_response['result'];

    	$records		= $result['records'];

    	$total_count	= count($records) or 0;

    	foreach ($records as $raw) {
    		
    		$order_ids[] = $raw['id'];
    	}

    	if($total_count < $limit){

    		return $order_ids;
    	}else{

    		$offset 	= $start_offset + $limit;

    		$this->getOpenOrders($offset,$market,$order_ids);
    	}


    }


    public function doTrade($market,$amount,$side){

    	$cmd 			= 'market';
    	$end_api_url	= 'exchange';

    	$request		= ['cmd' => $cmd,'market' => $market,'user_id' => $this->api_user_id,'side' => $side,'amount' => $amount,'api_token'=>$this->api_token];

    	$req_id 		= $this->api_log->insertLog(1, 0, 23, json_encode($request));

    	
    	
    	$response 	    = $this->callCurl($request,$end_api_url);

    	$this->api_log->updateLog($req_id, $response, '');

    	$decoded_response 		= json_decode($response,true);

    	$error			= $decoded_response['error'];

    	if(is_array($error)){ // login token failed 
    		
            $error_response  = ['status' => false,'code'=>'E32','message'=> 'API Error'];	
    		if($error['code'] == 11){
                $error_response  = ['status' => false,'code'=>'E150','message'=> 'API Error'];  
            }
            if($error['code'] == 12){
                $error_response  = ['status' => false,'code'=>'E151','message'=> 'API Error'];  
            }
            return json_encode($error_response);
    	}

    	$result			= $decoded_response['result'];

    	$order_id 		= $result['id'];
    	$order_pirce	= $result['price'];
        $deal_stock     = $result['deal_stock'];
        $deal_money     = $result['deal_money'];
        $deal_fee       = $result['deal_fee'];

    	$return_result 	= ['order_id' => $order_id, 'price' => $order_pirce,'deal_stock'=>$deal_stock,'deal_money'=>$deal_money,'deal_fee'=>$deal_fee];

    	$return_response = ['status' => true,'message' => '','data' =>$return_result];

    	return json_encode($return_response);

    }


    public function callCurl($request,$urlParam)
    {
        $url = $this->api_url .'/'.$urlParam;
		
        //die($url);
        //$this->insertLog($data,$url,$type);
    
        $ch 		= curl_init($url);
	    $request 	= http_build_query($request);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); //Timeout
	    $return = curl_exec($ch);
        //var_dump($return);
        //print_r(json_decode($return,true));
        //$this->editLog($return);
        
        return  $return;
        
    }
}