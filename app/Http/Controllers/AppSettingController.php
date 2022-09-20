<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function apiAuthenticate($consumer_data)
    {
        $settings = $this->getSetting();

        if ($settings['consumer_key'] == $consumer_data['consumer_key'] &&
            $settings['consumer_secret'] == $consumer_data['consumer_secret']) {

            return 1;

        } else {

            return 0;
        }
    }


    public function getSetting()
    {
       $settings = array();
       $settings['consumer_key'] = 1991;
       $settings['consumer_secret'] = 2000;

       return $settings;
    }

}
