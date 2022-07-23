<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $user_downloads = User::join('downloads', 'downloads.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.email')
            ->get(['users.id', 'users.email', DB::raw('count(downloads.id) as downloads')]);

        $user_veiws = User::join('views', 'views.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.email')
            ->get(['users.id', 'users.email', DB::raw('count(views.id) as views')]);

        $content_downloads = Content::join('downloads', 'downloads.content_id', '=', 'contents.id')
            ->groupBy('contents.id', 'contents.title')
            ->get(['contents.id', 'contents.title', DB::raw('count(downloads.id) as downloads')]);

        $content_veiws = Content::join('views', 'views.content_id', '=', 'contents.id')
            ->groupBy('contents.id', 'contents.title')
            ->get(['contents.id', 'contents.title', DB::raw('count(views.id) as views')]);

        return view('dashboard.statistics.index', compact('user_downloads', 'user_veiws', 'content_downloads', 'content_veiws'));
    }

    public function userDownload($id)
    {
        $user = User::find($id);
        $content = Content::join('downloads', 'downloads.content_id', '=', 'contents.id')
            ->where('downloads.user_id', $id)->get();

        return view('dashboard.statistics.user', compact('user', 'content'));
    }

    public function userView($id)
    {
        $user = User::find($id);
        $content = Content::join('views', 'views.content_id', '=', 'contents.id')
            ->where('views.user_id', $id)->get();

        return view('dashboard.statistics.user', compact('user', 'content'));
    }

    public function contentDownload($id)
    {
        $content = Content::find($id);
        $user = User::join('downloads', 'downloads.user_id', '=', 'users.id')
            ->where('downloads.content_id', $id)->get();

        return view('dashboard.statistics.content', compact('user', 'content'));
    }

    public function contentView($id)
    {
        $content = Content::find($id);
        $user = User::join('views', 'views.user_id', '=', 'users.id')
            ->where('views.content_id', $id)->get();

        return view('dashboard.statistics.content', compact('user', 'content'));
    }
}
