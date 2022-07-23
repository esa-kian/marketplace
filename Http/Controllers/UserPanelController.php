<?php

namespace App\Http\Controllers;

use App\Models\Accessible;
use App\Models\Company;
use App\Models\Notification;
use App\Models\Person;
use App\Models\View;
use Illuminate\Http\Request;
use App\Models\RequestClient;
use App\Models\User;
use Mockery\Matcher\Not;

class UserPanelController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function fixInfo(Request $request)
    {
        $user = User::with('company', 'person')->find(auth()->guard('api')->id());
        return response($user, 200);
    }

    public function settings(Request $request)
    {
        $user = User::find(auth()->guard('api')->id());

        if (User::where('email', $request->email)->first() && $request->email != $user->email) {
            return response("Sorry, someone's already using that email. Please try a different one.", 403);
        }

        // update company fields
        if ($user->company != null) {

            if ($request->phone_number != null) {
                Company::where('user_id', auth()->guard('api')->id())->update([
                    'phone_number' => $request->phone_number,
                ]);
            }
        }

        // update person fields
        if ($user->person != null) {

            if ($request->phone_number != null) {
                Person::where('user_id', auth()->guard('api')->id())->update([
                    'phone_number' => $request->phone_number,
                ]);
            }
        }

        // set new password
        if ($request->newpassword != null) {
            if ($user->password == bcrypt($request->password)) {
                $user->password = bcrypt($request->newpassword);
                $user->update();
            } else {
                return response('Password is wrong', 403);
            }
        }

        return response('updated', 200);
    }

    public function submitRequest(Request $request)
    {
        $request_client = new RequestClient();
        $request_client->accepted = 0;
        $request_client->user_id = auth()->guard('api')->id();
        $request_client->subject = $request->subject;
        $request_client->description = $request->message;

        $type = array("application", "rule", "question", "support", "upgrade_plan", "other");

        if (in_array($request->request_type, $type)) {
            $request_client->type = $request->request_type;
        } else {
            return response()->json("Request type is not valid!", 403);
        }
        $request_client->save();

        return response("Your request sent successfully!", 200);
    }

    public function history()
    {
        $client_requests = RequestClient::where('user_id', auth()->guard('api')->id())->get();

        return response($client_requests, 200);
    }

    public function accessible()
    {
        $contents = Accessible::with('content')->where('user_id', auth()->guard('api')->id())->get(); //db

        return response(['contents' => $contents], 200);
    }

    public function notifications()
    {
        $notifications = Notification::where('receiver_id', auth()->guard('api')->id())->latest()->paginate(10);

        return response($notifications, 200);
    }

    public function seen(Request $request)
    {
        $data = Notification::where('id', $request->id)->where('receiver_id', auth()->guard('api')->id())->update(['seen' => 1]);

        return response($data, 200);
    }

    public function hasUnreadNotif()
    {
        $notifications = Notification::where('receiver_id', auth()->guard('api')->id())->where('seen', 0)->get();

        if (count($notifications) == 0) {
            return response(0);
        } else {
            return response(1);
        }
    }
}
