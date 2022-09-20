<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

class Constants
{

   //HTTP STATUS
   public static $HTTP_ERROR = 500; //error
   public static $HTTP_CREATED = 201; //creación
   public static $HTTP_OK = 200; //lectura
   public static $HTTP_FORBIDDEN = 403; //Forbidden

   //EXCEPTIONS ERRORS
   public static $UNKNOW_ERROR = ['code' => '1000', 'message' => 'internal error'];
   public static $LOGIN_ERROR = ['code' => '1001', 'message' => 'invalid credentials'];
   public static $JWT_ERROR_CREATING_TOKEN = ['code' => '1002', 'message' => 'could not create token'];
   public static $API_AUTH_ERROR = ['code' => '1003', 'message' => 'invalid api credentials'];
   public static $MISSING_PARAMETERS = ['code' => '1004', 'message' => 'Some paramters are missing'];
   public static $ALREADY_USER_REGISTER = ['code' => '1005', 'message' => 'Email address is already exist'];
   public static $NO_USER_REGISTER = ['code' => '1006', 'message' => 'Record not found.'];
   public static $USER_PASSWORD_DISMATCH = ['code' => '1007', 'message' => 'Current password does not match.'];
   public static $USER_EMAIL_NOEXIST = ['code' => '1008', 'message' => "Email address doesn't exist!"];

    //EXCEPTIONS ERRORS FOR LOGIN GENERIC
    public static $USER_TOKEN_EXPIRED = ['code' => '1010', 'message' => "Your token has expired. Please, login again."];
    public static $USER_TOKEN_INVALID = ['code' => '1011', 'message' => "Your token is invalid. Please, login again."];
    public static $USER_NO_TOKEN= ['code' => '1012', 'message' => "Please, attach a Bearer Token to your request."];
    public static $USER_NO_VALIDATE_ERROR = ['code' => '1013', 'message' => 'user no validate'];
    public static $USER_VALIDATE_ERROR = ['code' => '1014', 'message' => 'user is already validated'];


   //GENERAL MESSAGES
   public static $UPDATE_OK = ['code' => '0', 'message' => "Updated successfully."];
   public static $DELETE_OK = ['code' => '0', 'message' => "Delete successfully."];
   public static $NONE_REGISTER = ['code' => '0', 'message' => "There are no records."];

}
