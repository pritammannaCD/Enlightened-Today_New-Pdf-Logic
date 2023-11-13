<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Clickfunnel_leads;
use App\Models\Clickfunnel_log;
use App\Models\Filter_cf_log;

class WebhookController extends Controller
{
    //
    public function index(string $id){
        // $users = DB::select('select * from clickfunnel_leads');
        // foreach ($users as $user) {
        //     echo $user->first_name;
        // }
        echo $id;
    }

    public function save_to_table(Request $request, string $id){
        
        $allRequest = $request->all();
        $this->filter_webhook_data($allRequest, $id);
        
    }

    public function filter_webhook_data($webhook_data, $id){
        //print_r ($data);
        $data = array(); 
        $ipResponse = array();
        $klist_id = $id;
        //$ipstack = new IpStack();
        //$db = new Storage(); 
        //$function = new BounceFunction();
        if(isset($webhook_data['event']) == 'created'){
            $data['klaviyo_listid'] = !empty($klist_id) ? $klist_id : 'not_assigned'; 
            $data['funnel_id'] = isset($webhook_data['funnel_id']) ? $webhook_data['funnel_id'] : '';

            $data['funnel_step_id'] = isset($webhook_data['funnel_step_id']) ? $webhook_data['funnel_step_id'] : ($webhook_data['contact']['funnel_step_id']?$webhook_data['contact']['funnel_step_id']:'');

            $data['first_name'] = isset($webhook_data['contact']['contact_profile']['first_name']) ? $webhook_data['contact']['contact_profile']['first_name'] : '';
            $data['last_name'] = isset($webhook_data['contact']['contact_profile']['last_name']) ? $webhook_data['contact']['contact_profile']['last_name'] : '';
            $data['full_name'] = $data['first_name'].' '.$data['last_name'];
            $data['email'] = isset($webhook_data['contact']['contact_profile']['email']) ? $webhook_data['contact']['contact_profile']['email'] : '';
            $data['birthday'] = isset($webhook_data['contact']['birthday']) ? $webhook_data['contact']['birthday'] : 'NA';
            $data['phone_number'] = isset($webhook_data['contact']['contact_profile']['phone']) ? $webhook_data['contact']['contact_profile']['phone'] : '';  
            $data['ip_address'] = isset($webhook_data['ip']) ? $webhook_data['ip'] : ($webhook_data['contact']['ip']?$webhook_data['contact']['ip']:'');
            $data['address'] = isset($webhook_data['address']) ? $webhook_data['address'] : ($webhook_data['contact']['contact_profile']['address']?$webhook_data['contact']['contact_profile']['address']:'');
            if($data['ip_address']!=''){
            $ipResponse = $this->getLocation($data['ip_address']);
            }
            $data['city_name'] = !empty($webhook_data['city']) ? $webhook_data['city'] : (isset($ipResponse['data']['city']) ? $ipResponse['data']['city'] : '');
            $data['region_name'] = !empty($webhook_data['state']) ? $webhook_data['state'] : (isset($ipResponse['data']['region_name']) ? $ipResponse['data']['region_name'] : ''); 
            $data['country_name'] = !empty($webhook_data['country']) ? $webhook_data['country'] : (isset($ipResponse['data']['country_name']) ? $ipResponse['data']['country_name'] : '');   
            $data['phone_code'] = isset($ipResponse['data']['location']['calling_code']) ? '+'.$ipResponse['data']['location']['calling_code'] : ''; 
            if(!empty($data['phone_number'])){
            $data['phone_number'] = $this->phone_number_formatter($data['phone_number'],$ipResponse['data']['location']['calling_code']);
            }     
            $data['cf_create_at'] = isset($webhook_data['created_at']) ? $webhook_data['created_at'] : date('Y-m-d H:M:S');    
            $data['ipstack_response'] = json_encode($ipResponse); 
            $data['webhook_response'] = json_encode($webhook_data);
            $data['status'] = 1;
            $status = $this->save_to_db($data);
            $status_log = $this->save_log_to_db($data);

            //exit(0);
            
            // print_r($data);

            if($status == 'inserted successfully'){
            echo "<br>Email: ".$data['email']." => has been saved to the table!<br>";
            }else{
                
                //$log  = "Table insertion failed.".PHP_EOL;
                //$function->logFile('./logs/table_err_log_',$data['email'],$log);

                
            }
        }
    }

    function save_log_to_db($data){
        $response = array();

        $profile = array(
            'email' => $data['email'],
            'webhook_response' => $data['webhook_response'],
        );
        // $columns = $this->filterColumn($profile);
        // $values = $this->filterValues($profile);
        // $sql = "INSERT INTO `clickfunnel_log` ($columns) VALUES ($values)";

        $objWebhookDataLog = new Clickfunnel_log;
        foreach($profile as $column => $value){
            $objWebhookDataLog->$column = $value;
        }
        $queryResponse = $objWebhookDataLog->save();
        if($queryResponse == 1){
            $response['success'] = 1;
            $response['message'] = "inserted successfully";
        }else{ 
            $response['success'] = 0;
            //$response['message'] = $this->conn->error;
            $response['message'] = "There is an error while inserting data";
        }	
        //$response['query'] =  $sql;
        return $response;

         
    }

    public function save_to_db($data){
        $profile = array();
        $webhook_data = @json_decode($data['webhook_response'],true);
        date_default_timezone_set("US/Eastern");
		$current_datetime = date('Y-m-d h:m:s');
        
        if($webhook_data['status']=='paid'){
            $log=0;
            if($data['funnel_id']=='11840645' && ($data['funnel_step_id']=='76470389')){
                $klaviyo_listid='SZ8mJR'; 
            }else if($data['funnel_id']=='12512604' && ($data['funnel_step_id']=='82924961')){
                $klaviyo_listid='SZ8mJR'; 
            }else if($data['funnel_id']=='13069899' && ($data['funnel_step_id']=='88461322')){
                $klaviyo_listid='SZ8mJR'; 
            }else if($data['funnel_id']=='12205531' && ($data['funnel_step_id']=='80034909')){
                $klaviyo_listid='XhigNd'; 
            }else if($data['funnel_id']=='12953847' && ($data['funnel_step_id']=='87203725')){
                $klaviyo_listid=$data['klaviyo_listid'];
            }else if($data['funnel_id']=='12562249' && ($data['funnel_step_id']=='83359026')){
                $klaviyo_listid=$data['klaviyo_listid'];
            }else if($data['funnel_id']!='11840645' && $data['funnel_id']!='12512604' && $data['funnel_id']!='13069899' && $data['funnel_id']!='12205531' && $data['funnel_id']!='12953847' && $data['funnel_id']!='12562249'){
                $klaviyo_listid=$data['klaviyo_listid']; // All steps will be captured under this except the funnels 11840645 and 13069899 and 12512604 and 12205531 and 12953847 and 12562249
            }else{
                $klaviyo_listid=$data['klaviyo_listid']; // will store in seperate log table.
                $log=1;
            }
            $profile = array(
                'klaviyo_listid' => $klaviyo_listid,
                'funnel_id' => $data['funnel_id'],
                'funnel_step_id' => $data['funnel_step_id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'birthday' => $data['birthday'],
                'phone_number' => $data['phone_number'],
                'ip_address' => $data['ip_address'],
                'address' => $data['address'],
                'city_name' => $data['city_name'],
                'region_name' => $data['region_name'],
                'country_name' => $data['country_name'],
                'phone_code' =>  $data['phone_code'],
                'cf_create_at' => $data['cf_create_at'],
                'ipstack_response' => $data['ipstack_response'],
                'webhook_response' => $data['webhook_response'],
                'status' => $data['status'],
                'updated_at' => $current_datetime,
                'created_at' => $current_datetime,
            );
            
            $objWebhookData = new Clickfunnel_leads;

            foreach($profile as $column => $value){
                $objWebhookData->$column = $value;
            }

            
            $queryResponse = 0;
            if($log==0){
                //$sql = "INSERT INTO `clickfunnel_leads` ($columns) VALUES ($values)";
                $queryResponse = $objWebhookData->save();
            }else{
                //$sql = "INSERT INTO `filter_cf_log` ($columns) VALUES ($values)";
                $objWebhookDataCFLog = new Filter_cf_log;
                $queryResponse = $objWebhookDataCFLog->save();
            }
            if($queryResponse == 1){
                return "inserted successfully";
            }else{ 
                return "There is an error while inserting data";
            }	
        }else{
            return 0;
        }
    }

    

    /*Internal function for Identifying the location*/
    public function getLocation($ip){
        $apikey = '665393a76cb200c752413216c0256fbf';
	    $endpoint = 'http://api.ipstack.com';
		$response_data = array();
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $endpoint.'/'.$ip.'?access_key='.$apikey); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$response = curl_exec($ch); 
		if(curl_errno($ch)){
			$response_data['success'] = 0;
			$response_data['message'] = curl_error($ch);
			$response_data['data'] = array();
		}else{
			$response_data['success'] = 1;
			$response_data['message'] = 'Details have been fetched based on Ip address!';
			$response_data['data'] = json_decode($response, true); 
		}
		curl_close($ch); 
		return $response_data;
	}

    /*Internal function for Phone number format*/
    public function phone_number_formatter(){
	    $number = preg_replace("/[^0-9]+/", "", $phone);
	    $phoneNumber = $phone;
	    if(strlen($number) == 10){
	      //$phoneNumber = '+'.$phonecode.substr($number,0,1).'-'.substr($number,1,3).'-'.substr($number,4,3).'-'.substr($number,7,3);
	    	$phoneNumber = '+'.$phonecode.substr($number,0,1).substr($number,1,3).substr($number,4,3).substr($number,7,3);
	    }elseif(strlen($number) == 11){
	    	//$phoneNumber = '+'.substr($number,0,2).'-'.substr($number,2,3).'-'.substr($number,5,3).'-'.substr($number,8,3);
	    	$phoneNumber = '+'.substr($number,0,2).substr($number,2,3).substr($number,5,3).substr($number,8,3);
	    }elseif(strlen($number) == 12){
	    	//$phoneNumber = '+'.substr($number,0,3).'-'.substr($number,3,3).'-'.substr($number,6,3).'-'.substr($number,9,3);
	    	$phoneNumber = '+'.substr($number,0,3).substr($number,3,3).substr($number,6,3).substr($number,9,3);
	    }else{
	    	//$phoneNumber = $this->formatter(substr($number,-10),$phonecode);
	    	if(strlen($phone)>=10){
	    	  $phoneNumber = $this->phone_number_formatter(substr($number,-10),$phonecode);	
	    	}else{
	    		$phoneNumber = $phone;
	    	}
	    }
	    return $phoneNumber;
	}
}
