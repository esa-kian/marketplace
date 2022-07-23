<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function insert(Request $request)
    {
        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->content_id = $request->content_id;
        $comment->user_id = auth()->guard('api')->id();

        if ($request->parent_id != null) {
            $comment->parent_id = $request->parent_id;
        }

        $comment->save(); //db

        return response('Ok', 200);
    }

    public function comments($content_id)
    {
        $comments = Comment::with('user', 'likes', 'replies.likes', 'replies.user', 'replies.replies.user')
        ->withCount(['likes as like_count' => function ($query) {
            $query->select(DB::raw('coalesce(count(comment_id),0)'))->where('like_dislike', 1);
        }])->withCount(['likes as dislike_count' => function ($query) {
            $query->select(DB::raw('coalesce(count(comment_id),0)'))->where('like_dislike', 0);
        }])->where('content_id', $content_id)->get();

        $liked = Like::where('user_id', auth()->guard('api')->id())->get();
        return response(['comments' => $comments, 'liked' => $liked], 200);
    }

    public function index()
    {
        $comments = Comment::latest()->paginate(15);
        return view('dashboard.comments', compact('comments'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function destroy($id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            Comment::where('id', $id)->delete();

            return redirect()->route('comments')
                ->with('success', 'Comment deleted successfully');
        } else {
            return redirect('/admin/comments');
        }
    }
}
