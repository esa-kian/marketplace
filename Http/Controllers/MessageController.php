<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use \Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


    public function allMessages()
    {
        $messages = Notification::where('sender_id', Auth::id())->get();
        return view('dashboard.messages', compact('messages'))->with('i', (request()->input('page', 1) - 1) * 5);
    }


    /**
     * Create a new message to users
     *
     */
    public function createWithUser($email)
    {
        return view('dashboard.newMessage', compact('email'));
    }

    public function create()
    {
        $email = null;
        return view('dashboard.newMessage', compact('email'));
    }
    /**
     * Send a new message to users
     *
     */
    public function send(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->route('new_message')
                ->withErrors($validator)
                ->withInput();
        }

        $message = new Notification();
        $message->sender_id = Auth::id();
        $receiver_id = DB::table('users')->select('id')->where('email', $request->email)->first(); //db
        $message->receiver_id = $receiver_id->id;
        $message->subject = $request->subject;
        $message->body = $request->body;

        $message->save();

        return redirect()->route('messages')
            ->with('success', 'Message sent successfully.');
    }

    public function edit($id)
    {
        $message = Notification::find($id);

        return view('dashboard.editMessage', compact('message'));
    }

    public function destroy($id)
    {
        Notification::where('id', $id)->delete();
        return redirect()->route('messages')
            ->with('success', 'Message deleted successfully');
    }
}
