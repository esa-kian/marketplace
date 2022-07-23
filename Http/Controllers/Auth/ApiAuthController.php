<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Company;
use App\Models\PasswordReset;
use App\Models\Person;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Notifications\VerificationEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use \Validator;
use Illuminate\Support\Str;

class ApiAuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);




        try {
            $user = User::create($input);
        } catch (\Throwable $th) {
            return $this->sendError('Unauthorised.', ['error' => 'Another account is using ' . $request->email]);
        }

        $token =  $user->createToken('MyApp')->accessToken;

        $success['token'] = $token;

        $user->remember_token = Str::random(60);

        $user->save();

        $success['email'] =  $user->email;

        if ($request->type == "company") {
            $company = new Company();
            $company->first_name = $request->first_name_company;
            $company->last_name = $request->last_name_company;
            $company->company_name = $request->company_name;
            $company->phone_number = $request->phone_number_company;
            $company->company_address = $request->company_address;
            $user->company()->save($company);
        } elseif ($request->type == "person") {
            $person = new Person();
            $person->first_name = $request->first_name;
            $person->last_name = $request->last_name;
            $person->phone_number = $request->phone_number;
            $user->person()->save($person);
        }

        $subscription_plan = new SubscriptionPlan();
        $subscription_plan->type = "free";
        $subscription_plan->expired_at = Carbon::now()->addDays(0);
        $user->subscriptionPlan()->save($subscription_plan);
        // $user->sendEmailVerificationNotification();

        $this->sendEmailVerification($user);

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            if ($user->person != null) {
                $success['name'] =  $user->person->first_name;
            } else if ($user->company != null) {
                $success['name'] =  $user->company->first_name;
            }
            $success['active'] =  $user->active;
            $success['subscription_plan'] =  $user->subscriptionPlan->type;
            $expire_remain = Carbon::parse($user->subscriptionPlan->expired_at);
            $success['expire_remained'] = $expire_remain->diffInDays(Carbon::now());

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->withErrors('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function validatePasswordRequest(Request $request)
    {
        $user = DB::table('users')->where('email', '=', $request->email)
            ->first(); //Check if the user exists
        if (count($user) < 1) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }

        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => str_random(60),
            'created_at' => Carbon::now()
        ]); //Get the token just created above
        $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();

        if ($this->sendResetEmail($request->email, $tokenData->token)) {
            return redirect()->back()->with('status', trans('A reset link has been sent to your email address.'));
        } else {
            return redirect()->back()->withErrors(['error' => trans('A Network Error occurred. Please try again.')]);
        }
    }

    private function sendResetEmail($email, $token)
    { //Retrieve the user from the database
        $user = DB::table('users')->where('email', $email)->select('firstname', 'email')->first(); //Generate, the password reset link. The token generated is embedded in the link$link = config('base_url') . 'password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            //Here send the link with CURL with an external email API         return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function createTokenResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response([
                'message' => 'We can\'t find a user with that email address.'
            ], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );
        if ($user && $passwordReset) {

            $user->notify(
                new PasswordResetRequest($passwordReset)
            );
        }

        return response([
            'message' => 'We have emailed your password reset link!'
        ]);
    }

    public function findTokenResetPassword($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {

            return response([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {

            $passwordReset->delete();

            return response([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return redirect(env('CLIENT_URL') . '/reset_password?token=' . $passwordReset->token .  '&email=' . $passwordReset->email);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();


        if (!$passwordReset) {

            return response([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {

            return response([
                'message' => 'We can\'t find a user with that email address.'
            ], 404);
        }

        $user->password = bcrypt($request->password);

        $user->save();

        $user->notify(new PasswordResetSuccess($passwordReset));

        $passwordReset->delete();

        return response($user);
    }

    public function sendEmailVerification($user)
    {
        $user->notify(
            new VerificationEmail($user->email, $user->remember_token)
        );
    }

    public function verifyAccount($token)
    {
        Log::info($token);
        $user = User::where('remember_token', $token)->first();

        if (!$user) {

            return response([
                'message' => 'This user token is invalid.'
            ], 404);
        }

        if (Carbon::parse($user->updated_at)->addMinutes(720)->isPast()) {

            $user->delete();

            return response([
                'message' => 'This token is invalid.'
            ], 404);
        }

        $user->email_verified_at = Carbon::now();

        $user->save();

        return redirect(env('CLIENT_URL') . '/registration');
    }
}
