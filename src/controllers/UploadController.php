<?php

namespace Nonoesp\Folio\Controllers;

use Request;
use Form;
use Input;
use Image;

class UploadController extends Controller
{

    public function getUploadForm()
	{

		  $img_exists = false;
		  $img_uploaded = false;
		  $img_URL = "";

		  if(Request::isMethod('post')) {

		    $name = Input::get('name');
		    if($name == '') {
		      $name = 'Untitled.jpg';
		    }
		    $img_URL = config('folio.media-upload-path').$name;
		    $shouldReplace = Input::get('shouldReplace');

		    if(file_exists(public_path($img_URL))) {
		      $img_exists = true;
		    }

		    if(!$img_exists || $img_exists && $shouldReplace) {
		      if(Input::hasFile('photo')) {
		        $max_width = Input::get('max_width');
		        $img = Image::make(Input::file('photo'));      
		        // Downsize image if wider than $max_width
		        if($img->width() > $max_width) {
		          $img->resize($max_width, null, function ($constraint) {
		            $constraint->aspectRatio();
		            $constraint->upsize();
		          });
		        }
		        $img->save(public_path($img_URL));
		      }
    
		      if(Input::hasFile('photo')) {
		      	$img_uploaded = true;
		      }      
		    } else {
		      // Image was not uploaded because it existed.
		    }
		  }

		return view('folio::admin.upload.form')->with([
			'img_exists' => $img_exists,
			'img_uploaded' => $img_uploaded,
			'img_URL' => $img_URL
			]);
	}

	public function getMediaList()
	{
		return view('folio::admin.upload.list');
	}

	public function postDeleteMedia($name)
	{
		unlink(public_path(config('folio.media-upload-path').$name));
  		return redirect()->to('/admin/upload/list');
	}

}