<?php

namespace Nonoesp\Space\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Subscriber;

class SubscriptionController extends Controller
{
  public function postSubscriber()
  {
    $email = \Input::get('email');
    // $context = \Input::get('context');
    // if($context != '') {
    //   $email .= $context;
    // }

    $subscriber = new Subscriber();
    $subscriber->email = $email;
    $subscriber->save();

      return response()->json([
          'success' => true,
          'email' => $subscriber->email
      ]);
  }
}
