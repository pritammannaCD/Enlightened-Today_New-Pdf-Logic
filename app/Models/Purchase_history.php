<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase_history extends Model
{
    use HasFactory;

    protected $table = "purchase_history";

    static function checkPurchaserowExist($email,$purchaseType){
        $status='N';
        $total_templates_chosen = self::where([['email',"=","$email"],['purchase_type',"=",$purchaseType]])->count('id');

        if($total_templates_chosen>0){
            $status='Y';
        }
        return $status;
    }
}
