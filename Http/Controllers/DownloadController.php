<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function download(Request $request)
    {
        $downloaded = Download::where('user_id', auth()->guard('api')->id())->where('content_id', $request->content_id)->get(); //db

        if (count($downloaded) == 0) {
            $download = new Download();

            $download->content_id = $request->content_id;
            $download->user_id = auth()->guard('api')->id();
            $download->save();
            return response('Ok', 200);
        }
        else {
            return response('You downloaded this file before');
        }

    }
}
