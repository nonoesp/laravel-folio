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
    $email = $request->input('email');
    $name = $request->input('name');
    $source = $request->input('source');
    $medium = $request->input('medium');
    $campaign = $request->input('campaign');
    $newsletter_list = $request->input('newsletter_list');
    $path = $request->input('path');
    $ip = Thinker::clientIp();

    $subscriber = new Subscriber();
    $subscriber->email = $email;
    $subscriber->name = $name;
    $subscriber->source = $source;
    $subscriber->medium = $medium;
    $subscriber->campaign = $campaign;
    $subscriber->newsletter_list = $newsletter_list;
    $subscriber->path = $path;
    $subscriber->ip = $ip;
    $subscriber->host = $request->root();
    $subscriber->save();

    $data = [];
    if($name = $subscriber->name) {
      array_push($data, $name);
    }
    if($source = $subscriber->source) {
      array_push($data, $source);
    }
    if($medium = $subscriber->medium) {
      array_push($data, $medium);
    }
    if($campaign = $subscriber->campaign) {
      array_push($data, $campaign);
    }
    if($newsletter_list = $subscriber->newsletter_list) {
      array_push($data, $newsletter_list);
    }
    if($ip = $subscriber->ip) {
      array_push($data, $ip);
    }

    // Build JSON response
    $response = [
      'success' => true,
      'error' => '',
      'email' => $subscriber->email,
      'name' => $subscriber->name,
      'source' => $subscriber->source,
      'medium' => $subscriber->medium,
      'campaign' => $subscriber->campaign,
      'newsletter_list' => $subscriber->newsletter_list,
      'path' => $subscriber->path,
      'host' => $subscriber->host,
    ];

    // Default email subject
    $email_subject = 'New Subscriber to '.config('folio.title-short');

    // Check email and ip address for spam
    $isSpamEmail = $subscriber->email == '' || \SpamProtector::isSpamEmail($subscriber->email);
    $isSpamIp = \SpamProtector::isSpamIp($subscriber->ip);
    $isSpam = $isSpamEmail || $isSpamIp;

    if ($isSpam) {
      $email_subject = '[SPAM] Subscriber at '.config('folio.title-short');
      $text = 'Filtered SPAM subscriber at '.config('folio.title-short').
      '<br/><br/>'.
      ($subscriber->email ?? '[no email]').($isSpamEmail ? ' › SPAM' : null).
      '<br/>'.
      ($subscriber->ip).($isSpamIp ? ' › SPAM' : null);
      $subscriber->delete();
    }

    $shouldNotifyAdmins = config('folio.subscribers.notify-admins');
    $shouldNotifyAdminsOfSpam = config('folio.subscribers.notify-admins-of-spam');

    if(
      (!$isSpam && $shouldNotifyAdmins) ||
      ( $isSpam && $shouldNotifyAdminsOfSpam)
      ) {

      Mail::send('folio::email.new-subscriber',
      [
        'email' => $email,
        'path' => $path,
        'data' => $data,
        'text' => $text ?? null,
      ],
      function ($email) use ($email_subject) {
        $email->to(config('folio.subscribers.to.email'), config('folio.subscribers.to.name'))
              ->from(config('folio.subscribers.from.email'), config('folio.subscribers.from.name'))
              ->subject($email_subject);
      });

    }

    if(
      config('folio.subscribers.add-to-newsletter') &&
      !$isSpam
      ) {

      $lists = explode(",", str_replace(' ', '', $newsletter_list));
      $valid_lists = [];
      
      // Ensure there are valid lists
      foreach($lists as $list) {
        $list_id = config("newsletter.lists.$list.id");
        if ($list_id) {
          // List id exists
          array_push($valid_lists, $list);
        }
      }

      // Fallback to default list if no valid lists
      if (!count($valid_lists)) {
        $valid_lists = [config('newsletter.defaultListName')];
      }

      $subscribed_lists = [];
      $subscribed_lists_confirmed = [];

      // Subscribe to valid lists
      foreach($valid_lists as $list) {
        // $list_id = config("newsletter.lists.$list.id");

        // Not subscribed yet to this list?
        if (!in_array($list, $subscribed_lists)) {
          
          // Remember we already subscribed
          array_push($subscribed_lists, $list);

          try {
            // Subscribe
            \Newsletter::subscribeOrUpdate(
              $email,
              [
                // Here we need to reference the merge tags (e.g. 'NAME')
                // and pass a valid string (even if empty) for it to work
                //'NAME'=> $name,
                //'LASTNAME'=> $lastname,
                'IP' => $ip
              ],
              $list);

              array_push($subscribed_lists_confirmed, $list);

          }
          catch (\Spatie\Newsletter\Exceptions\InvalidNewsletterList $e) {
              // [ERROR] Invalid newsletter list
              $response['success'] = false;
              $response['error'] = 'InvalidNewsletterList · '.$list;
              return response()->json($response);              
          }
                
        }

      }

      $response["newsletter_list_subscribed"] = join(",", $subscribed_lists_confirmed);
      
    }
    
    return response()->json($response);
  }

  // Soft delete an existing subscriber
  public function delete(Request $request) {
    $id = $request->input('id');
    if($subscriber = Subscriber::find($id)) {
      $subscriber->delete();
    } else {
      // does not exist or is already deleted
    }
  }

  // Restore a soft-deleted subscriber
  public function restore(Request $request) {
    $id = $request->input('id');
    if($subscriber = Subscriber::onlyTrashed()->find($id)) {
      $subscriber->restore();
    } else {
      // does not exist or is already restored
    }
  }  
}
