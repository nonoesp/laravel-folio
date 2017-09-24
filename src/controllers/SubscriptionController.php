<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Subscriber;
use Thinker;
use Mail;

class SubscriptionController extends Controller
{
  public function postSubscriber(Request $request)
  {
    $email = \Input::get('email');
    $name = \Input::get('name');
    $source = \Input::get('source');
    $campaign = \Input::get('campaign');
    $path = \Input::get('path');
    $ip = Thinker::clientIp();

    $subscriber = new Subscriber();
    $subscriber->email = $email;
    $subscriber->name = $name;
    $subscriber->source = $source;
    $subscriber->campaign = $campaign;
    $subscriber->path = $path;
    $subscriber->ip = $ip;
    $subscriber->save();
    
    if(config('folio.subscribers.should-notify') == true) {
      Mail::send('folio::email.new-subscriber', ['email' => $email, 'path' => $path], function ($m) use ($email) {
        $m->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'));
        $m->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))->
            subject('New Subscriber to '.config('folio.title-short'));
      });
    }
    
    return response()->json([
        'success' => true,
        'email' => $subscriber->email,
        'source' => $subscriber->source,
        'name' => $subscriber->name,
        'campaign' => $subscriber->campaign,
        'path' => $subscriber->path
    ]);
  }
}
