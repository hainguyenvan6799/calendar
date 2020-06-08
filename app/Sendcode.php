<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sendcode extends Model
{
    //
    public static function sendCode($phone)
    {
    	$code = rand(1111,9999);
    	$nexmo = app('Nexmo\Client');
    	$nexmo->message()->send([
    		'to'=>'+84'.(int) $phone,
    		'from'=>'Hai Nguyen Van',
    		'text'=>'Verify code:'.$code,
    	]);
    	return $code;
    }
}
