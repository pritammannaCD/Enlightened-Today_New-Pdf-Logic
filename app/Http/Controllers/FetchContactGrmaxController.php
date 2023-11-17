<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Clickfunnel_leads;
use App\Models\purchase_history;

class FetchContactGrmaxController extends Controller
{
    //
    public function index(){
        //echo "This is index";
        //$obj = new Clickfunnel_leads;
        //echo $obj->getallPurchasers();
        //echo $obj->getallPurchasersfirstTo21Aug();
        //echo $obj->test();
        //$obj1 = new Purchase_history;
        //echo $obj1->checkPurchaserowExist("abhishek.chatterjee@codeclouds.com1","1");

        //echo $this-> updatePurchaseHistory("7","test","test","test");

        //echo $obj->updateUsertable("test@gmail.com");

        // $Clickfunnel_leads_obj = new Clickfunnel_leads;
        // $data = $obj->getallPurchasers();

        // $usertype=array();
        // foreach ($data as $contact) {   
        //     //echo $contact['email'];
        //     $check_grlist=checkFromGrlist($contact['email'],$contact['id'],$Clickfunnel_leads_obj);
        //     echo $check_grlist;
        // }

        $this->oneForAll('index');
        echo "index";
    }

    public function reverse(){
        $this->oneForAll('reverse');
        echo "This is reverse";
    }
    public function pro(){
        $this->oneForAll('pro');
        echo "This is pro";
    } 

    public function oneForAll($calledFrom){
        $Clickfunnel_leads_obj = new Clickfunnel_leads;

        if($calledFrom == 'index'){
            $data = $Clickfunnel_leads_obj->getallPurchasers();
        }elseif($calledFrom == 'reverse'){
            $data = $Clickfunnel_leads_obj->getallPurchasers();
        }elseif($calledFrom == 'pro'){
            $data = $Clickfunnel_leads_obj->getallPurchasersfirstTo21Aug();
        }
        echo $data;
        exit(0);
        $usertype=array();
        foreach ($data as $contact) {   
            //echo $contact['email'];
            $check_grlist=checkFromGrlist($contact['email'],$contact['id'],$Clickfunnel_leads_obj);
            echo $check_grlist;
        }
    }

    public function checkFromGrlist($email,$id,$Clickfunnel_leads_obj){
        //return $email;
        // get contact list from getresponse
        $token='tiai7ou1u7zto8vl3v84fx5tf7jb8v46';
        //$token='tiai7ou1u7zto8vl3v84fx5tf7jb8v46';
        $host = 'https://api.getresponse.com/v3/contacts?query[email]='.$email.'';
        
        $headers = array(
            'Content-Type: application/json',
            'X-Auth-Token: api-key '. $token
        );
        
        $process = curl_init($host);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($process, CURLOPT_USERPWD, "$username:$password");
        $output = curl_exec($process);
        
        $response = json_decode($output, true);
        
        $err = curl_error($process);
        curl_close($process);
        //return $response;
        
        //$merged_numbers=array();
        if(!empty($response)){
            $usertype=array();
            foreach($response as $gr){
                if($gr['campaign']['name']){
                    if($gr['campaign']['name']=='tarot-report-purchaser'){
                        array_push($usertype, 1, $gr['contactId']);
                    }
                    if($gr['campaign']['name']=='SS_second_purchaser'){
                        array_push($usertype, 2, $gr['contactId']);
                    }
                    if($gr['campaign']['name']=='SS_third_purchaser'){
                        array_push($usertype, 3, $gr['contactId']);
                    }
                }
                
            }
        }
        if(!empty($usertype)){
            return insertIntoHistory($usertype,$id,$Clickfunnel_leads_obj,$email);
        }else{
            $update_clickFunnel=$Clickfunnel_leads_obj->updateClickFunnelAttend($id);
        }
    }


    function insertIntoHistory($usertype,$id,$Clickfunnel_leads_obj,$email){
        //return $usertype;
        if(count($usertype)>0){
            for ($i=0;$i<count($usertype);$i=$i+2){
                $update_history=$Clickfunnel_leads_obj->updatePurchaseHistory($id,$usertype[$i],$usertype[$i+1],$email);
                if($update_history=='Y'){
                    return $update_user=$Clickfunnel_leads_obj->updateUsertable($email);
                }
            }
        }
    }


    public function updatePurchaseHistory($id,$insert_single,$contactId,$email){
        $obj1 = new Purchase_history;
        $checkrowExist=$obj1->checkPurchaserowExist($email,$insert_single);
        $status='N';
        if($checkrowExist=='N'){
            $obj1->userId= $id;
            $obj1->purchase_type= $insert_single;
            $obj1->contactId= $contactId;
            $obj1->email= $email;
            $queryResponse = $obj1->save();
            //$sql = "INSERT INTO `purchase_history` (userId,purchase_type,contactId,email) values ($id, $insert_single, '".$contactId."', '".$email."') ";
            if($queryResponse == 1){
                //return "History inserted successfully";
                $status='Y';
            }else{
                //return $this->conn->error;;
            }
        }
        return $status;
    }
}
