<?php

namespace App\Http\Controllers;

use App\Models\ContactForm;
use App\Models\Content;
use App\Models\RequestClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Comment;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware(['auth', 'verified']);
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $role = $request->user()->role;
        if ($role == 'super_admin' || $role == 'admin' || $role == 'content_manager') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/');
        }
    }

    public function topContent()
    {
        $topContent = Content::with(['application', 'rule', 'views', 'downloads'])->withCount(['votes as average_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(vote),0)'));
        }])->where('enable', 1)->orderByDesc('average_rating')->take(15)->get();
        return response($topContent, 200);
    }

    public function recentContent()
    {
        $recentContent = Content::with(['application', 'rule', 'views', 'downloads'])->withCount(['votes as average_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(vote),0)'));
        }])->where('enable', 1)->latest()->paginate(15);

        return response($recentContent, 200);
    }

    public function search(Request $request)
    {
        $content = Content::with(['application', 'rule', 'views', 'downloads'])->withCount(['votes as average_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(vote),0)'));
        }])->where('title', 'like', '%' . $request->title . '%')->where('enable', 1)->paginate(15); //db

        return response($content, 200);
    }

    public function filter(Request $request)
    {
        $content = Content::with(['application', 'rule', 'views', 'downloads'])->withCount(['votes as average_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(vote),0)'));
        }])->where('enable', 1)->where('type', $request->type)->orWhere('subscription_plan', $request->subscription_plan)->paginate(15); //db

        return response($content, 200);
    }

    public function dashboard(Request $request)
    {
        if ($request->user()->role != 'client') {
            $contents = Content::latest()->paginate(5);
            $users = User::latest()->paginate(5);
            $messages = ContactForm::latest()->paginate(5);
            $requests = RequestClient::latest()->paginate(5);
            $comments = Comment::latest()->paginate(5);
            return view('dashboard.dashboard', compact('contents', 'users', 'messages', 'requests', 'comments'));
        } else {
            return response('Not Allowed', 405);
        }
    }

    public function contactMessage()
    {
        $messages = ContactForm::latest()->paginate(5);
        return view('dashboard.contactMessages', compact('messages'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    // list of sent request from clients
    public function clientRequest()
    {
        $requests = RequestClient::latest()->paginate(5);
        return view('dashboard.clientRequests', compact('requests'))->with('i', (request()->input('page', 1) - 1) * 5);
    }
    // accept request from client method
    public function acceptRequest($id)
    {
        $clientReq = RequestClient::find($id);
        $clientReq->accepted = 1;
        $clientReq->update();
        return redirect()->route('client_requests')
            ->with('success', 'Request accepted successfully.');
    }


    public function adminSearch(Request $request)
    {
        $type = "content";
        if ($request->search_type == "rule") {
            $result = Content::where('title', 'LIKE', '%' . $request->search . '%')->whereNotNull('rule_id')->get();
        } elseif ($request->search_type == "user") {
            $result = User::where('email', 'LIKE', '%' . $request->search . '%')->get();
            $type = "user";
        } elseif ($request->search_type == "application") {
            $result = Content::where('title', 'LIKE', '%' . $request->search . '%')->whereNotNull('application_id')->get();
        }
        return view('dashboard.search', compact('result', 'type'));
    }
}
