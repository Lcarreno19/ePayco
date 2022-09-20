<?php

namespace App\Traits;

use DB;
use App\Http\Controllers\AppSettingController;

class Util
{

    public static function initAuth($request) {

        $consumer_data = array();
        $consumer_data['consumer_key'] = $request->header('consumer-key');
        $consumer_data['consumer_secret'] = $request->header('consumer-secret');
        $authController = new AppSettingController();
        return $authController->apiAuthenticate($consumer_data);
    }
}
