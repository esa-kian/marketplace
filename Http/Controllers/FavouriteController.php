<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function fav(Request $request)
    {
        $faved = Favourite::where('content_id', $request->content_id)
            ->where('user_id', auth()->guard('api')->id())->get();

        if (count($faved) == 0) {
            $fav = new Favourite();
            $fav->user_id = auth()->guard('api')->id();
            $fav->content_id = $request->content_id;
            $fav->save();

            return response('Ok', 200);
        }
    }

    public function unfav($id)
    {
        $faved = Favourite::where('content_id', $id)
            ->where('user_id', auth()->guard('api')->id())->get();

        if (count($faved) != 0) {
            Favourite::where('content_id', $id)
                ->where('user_id', auth()->guard('api')->id())->delete();

            return response('Ok', 200);
        }
    }


    public function favs()
    {
        $favs = Favourite::where('user_id', auth()->guard('api')->id())->with('content')->get();

        return response(['favourites' => $favs], 200);
    }
}
