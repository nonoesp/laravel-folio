<?php

namespace Nonoesp\Folio\Controllers;

use Illuminate\Http\Request;
use Form;
use Image;
use File;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class UploadController extends Controller
{

    public function getUploadForm(Request $request)
	{
        $errors = [];
        $messages = [];

        $uploaderPublicFolder = config('folio.uploader.public-folder');
        $uploaderDisk = config('folio.uploader.disk');
        $uploaderUploadsFolder = config('folio.uploader.uploads-folder');
        $uploaderAllowedFileTypes = config('folio.uploader.allowed-file-types');

        $formFilename = 'photo';

        $imgExists = false;
        $imgUploaded = false;
        $imgURL = null;
        $filename = null;
        $fileType = null;        

        if(!\Storage::disk($uploaderDisk)
                    ->exists($uploaderUploadsFolder)) {
			// path does not exist
			return view('folio::admin.notification', [
				'title' => 'Upload',
				'message' => "Uploads folder not found at <code>$uploaderUploadsFolder</code> in <code>$uploaderDisk</code> disk."
			]);
		}

            if($request->isMethod('post')) {
                
                $input_filename = $request->input('name');
                $input_filename = str_replace(['ñ'], ['n'], $input_filename);
                $extension = Arr::last(explode('.', $input_filename));
                $filename_without_extension = substr($input_filename, 0, strlen($input_filename) - strlen($extension));
                $filename = Str::slug($filename_without_extension).'.'.Str::lower($extension);
            
            $imgURL = $uploaderPublicFolder.'/'.$filename;

            // Validate extension is allowed
            $fileType = $request->file($formFilename)->extension();
            if (!in_array($fileType, $uploaderAllowedFileTypes)) {
                array_push($errors, "The <strong>$fileType</strong> extension is not allowed.<br/>You might enable this in <code>folio.uploader.allowed-file-types</code>.");
                return view('folio::admin.upload.form', [
                    'errors' => $errors,
                    'filename' => $filename,
                ]);
            } else {
                // array_push($messages, "File type is <strong>$fileType</strong>");
            }

            // Validate image does not exist (or replace is active)
            $shouldReplace = $request->input('shouldReplace');
            $imgExists = \Storage::disk($uploaderDisk)
                                 ->exists($uploaderUploadsFolder.'/'.$filename);
            if ($imgExists && !$shouldReplace) {
                array_push($errors, "A upload named <strong>$filename</strong> already exists. Choose Overwrite if that's what you want.");
                return view('folio::admin.upload.form', [
                    'errors' => $errors,
                    'filename' => $filename,
                ]);
            }

            if($filename == '') {
                $filename = $request->file($formFilename)->getClientOriginalName();
            }

            // Confirm we're overwriting
            if ($imgExists) {
                array_push($messages, 'The file has been replaced.');

                // Purge from imgix if possible
                $imgix_api_key = env('IMGIX_API_KEY');
                $imgix_purge_on_replace = env('IMGIX_PURGE_ON_REPLACE');
                if ($imgix_api_key && $imgix_purge_on_replace) {
                    $imgix_url = imgix($uploaderUploadsFolder.'/'.$filename);
                    $purge_response = \Folio::purgeImgix($imgix_url);
                    if ($purge_response) {
                        array_push($messages, 'The file has been purged from imgix.');
                    } else {
                        array_push($messages, 'Failed to purge the file from imgix.');
                    }
                }
            }

            // Store file
            if($request->hasFile($formFilename)) {
                $request->file($formFilename)
                        ->storeAs(
                            $uploaderUploadsFolder,
                            $filename,
                            $uploaderDisk
                        );
                \Storage::disk($uploaderDisk)->setVisibility($uploaderUploadsFolder.'/'.$filename, 'public');
            } else {
                array_push($errors, 'Invalid image provided.</br>It was either empty of bigger than the limit configured in your server.');
                return view('folio::admin.upload.form', [
                    'errors' => $errors,
                    'filename' => $filename,
                ]);
            }

            if($request->hasFile($formFilename)) {
                $imgUploaded = true;
            }
        }

        return view('folio::admin.upload.form')->with([
            'errors' => $errors,
            'messages' => $messages,
            'imgExists' => $imgExists,
            'imgUploaded' => $imgUploaded,
            'imgURL' => $imgURL,
            'filename' => $filename,
            'fileType' => $fileType,
        ]);
	}

	public function getMediaList()
	{
		return view('folio::admin.upload.list');
	}

	public function postDeleteMedia($filename)
	{
        // Get uploads disk and folder name
        $uploaderDisk = config('folio.uploader.disk');
        $uploaderUploadsFolder = config('folio.uploader.uploads-folder');
        
        // Delete file
        \Storage::disk($uploaderDisk)
               ->delete("$uploaderUploadsFolder/$filename");

        // Return to uploads list
  		return redirect()->to(\Folio::adminPath('upload/list'));
	}

}