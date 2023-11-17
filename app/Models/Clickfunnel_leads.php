<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clickfunnel_leads extends Model
{
    use HasFactory;
    protected $table = "clickfunnel_leads";

    static function getallPurchasers(){
        $current_datetime = date('Y-m-d h:m:s');
        // return self::select("id","email")->where([
        //     ["is_process","=","N"],
        //     [date("created_at"),">=",'2023-08-22'],
        //     [date("created_at"),"<=",$current_datetime]
        //     ])->groupBy("email")->orderByDesc("id")->limit(120)->get();

        return self::select("id","email")->where([
            ["is_process","=","N"],
            ["created_at",">=",'2023-08-22'],
            ["created_at","<=",$current_datetime]
            ])->orderByDesc("id")->limit(120)->get();

    }

    static function getallPurchasersPro(){
        $current_datetime = date('Y-m-d h:m:s');

        return self::select("id","email")->where([
            ["is_process","=","A"],
            ["created_at",">=",'2023-08-22'],
            ["created_at","<=",$current_datetime]
            ])->orderByDesc("id")->limit(120)->get();
    }

    static function getallPurchasersfirstTo21Aug(){
        $current_datetime = date('Y-m-d h:m:s');

         return self::select("id","email")->where([
            ["is_process","=","N"]
            ])->whereBetween('created_at',[self::min('created_at'),'2023-08-22'])
            ->orderBy("id","Asc")->limit(100)->get();
    }

    static function updateUsertable($email){
        $current_datetime = date('Y-m-d h:m:s');
        $queryResponse = self::where([["email","=",$email]])->update(["is_process" => "Y","process_at" => $current_datetime]);

        //$sql = "UPDATE `clickfunnel_leads` SET `is_process` = 'Y', `process_at` = '".$this->current_datetime."'  WHERE `email` = '".$email."' ";
        //return $sql;
        if($queryResponse >= 1){
            return "User updated successfully=> ".$email;
        }else{
            return "There is an error while updating";
        }
    }

    static function updateClickFunnelAttend($id){
        $current_datetime = date('Y-m-d h:m:s');
        $queryResponse = self:: where([["id","=",$id]])->update(["is_process" => "A", "process_at" => $current_datetime]);

        //$sql = "UPDATE `clickfunnel_leads` SET `is_process` = 'A', `process_at` = '".$this->current_datetime."'  WHERE `id` = ".$id." ";
        //return $sql;
        if($resource = $this->conn->query($sql)){
            return "User attempted successfully";
        }else{
            return $this->conn->error;;
        }
    }

    static function test(){
        return self::count("id");
    }
}
