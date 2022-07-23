<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth');
    // }

    public function vote(Request $request)
    {
        $voted = Vote::where('user_id', 1)->where('content_id', $request->content_id)->get(); //db

        if (count($voted) == 0) {
            if (1 <= ($request->vote) && ($request->vote) <= 5) {
                $vote = new Vote();
                $vote->vote = $request->vote;
                $vote->user_id = 1;
                $vote->content_id = $request->content_id;
                $vote->save();
            }
            return response('ok', 200);
        } else {
            foreach ($voted as $v) {
                $updateVote = Vote::find($v->id);
                $updateVote->vote = $request->vote;
                $updateVote->content_id = $request->content_id;

                $updateVote->save();
            }
            return response('ok', 200);
        }
    }
}
