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
  public function create(Request $request)
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

    if(config('folio.should-add-to-mailchimp') == true) {
      \Newsletter::subscribeOrUpdate(
        $email, [
          // Here we need to reference the merge tags (e.g. 'NAME') and pass a valid string (even if empty) for it to work
          //'NAME'=> $name,
          //'LASTNAME'=> $lastname,
          'IP' => $ip
          ]);
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

  // Soft delete an existing subscriber
  public function delete() {
    $id = \Input::get('id');
    if($subscriber = Subscriber::find($id)) {
      $subscriber->delete();
    } else {
      // does not exist or is already deleted
    }
  }

  // Restore a soft-deleted subscriber
  public function restore() {
    $id = \Input::get('id');
    if($subscriber = Subscriber::onlyTrashed()->find($id)) {
      $subscriber->restore();
    } else {
      // does not exist or is already restored
    }
  }  
}
