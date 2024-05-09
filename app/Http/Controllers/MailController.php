<?php

namespace App\Http\Controllers;

use Mail;
use Exception;
use App\Mail\MailNotify;
use Illuminate\Http\Request;


class MailController extends Controller
{
    public function index()
  {
    $data = [
      "subject"=>"Cambo Tutorial Mail",
      "body"=>"Hello friends, Welcome to Cambo Tutorial Mail Delivery!"
      ];
    // MailNotify class that is extend from Mailable class.
    try
    {
      Mail::to('rowen.gonzales@safexpress.com.ph')->send(new MailNotify($data));
      return response()->json(['Great! Successfully send in your mail']);
    }
    catch(Exception $e)
    {
      return response()->json(['Sorry! Please try again latter']);
    }
  }

}
