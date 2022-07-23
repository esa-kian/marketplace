<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth');
    // }
    
    public function LikeDislike(Request $request)
    {
        $liked = Like::where('user_id', auth()->guard('api')->id())->where('comment_id', $request->comment_id)->get();//db
        $like = new Like;

        $like->comment_id = $request->comment_id;
        $like->user_id = auth()->guard('api')->id();

        if (count($liked) != 0) {
            foreach ($liked as $l) {
                $updateLike = Like::find($l->id);//db
            }
            foreach ($liked as $l) {
                if ($l->like_dislike == 1 && $request->like_dislike == 0) {
                    $updateLike->update($request->all());
                } elseif ($l->like_dislike == 0 && $request->like_dislike == 1) {
                    $updateLike->update($request->all());
                }
            }
        }
        else
        {
            $like->like_dislike = $request->like_dislike;
            $like->save();
        }

        return response('Ok', 200);
    }
}
