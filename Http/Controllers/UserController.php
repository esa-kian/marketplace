<?php

namespace App\Http\Controllers;

use App\Models\Accessible;
use App\Models\Company;
use App\Models\Content;
use App\Models\Person;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use \Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            $users = User::latest()->orderBy('id', 'asc')->paginate(15);
            return view('dashboard.users.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
        } else {
            return redirect('/admin/dashboard');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            if (Auth::user()->role == 'super_admin') {
                $roles = ['client' => 'Client', 'content_manager' => 'Content Manager', 'admin' => 'Admin', 'super_admin' => 'Super Admin'];
            } elseif (Auth::user()->role == 'admin') {
                $roles = ['client' => 'Client', 'content_manager' => 'Content Manager', 'admin' => 'Admin'];
            } else {
                return redirect('/admin/dashboard');
            }
            $type = ['person' => 'Person', 'company' => 'Company'];
            $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

            return view('dashboard.users.create', compact('roles', 'type', 'subscription_plan'));
        } else {
            return redirect('/admin/dashboard');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',

            ]);

            if ($validator->fails()) {

                return redirect()->route('users.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = new User();

            if (User::where('email', $request->email)->first()) {
                return redirect()
                    ->route('users.create')
                    ->withErrors("Sorry, someone's already using that email. Please try a different one.")
                    ->withInput();
            }
            $user->email = $request->email;

            if (
                $request->role == "client" || $request->role == "content_manager" ||
                $request->role == "admin" || $request->role == "super_admin"
            ) {
                if (Auth::user()->role != 'super_admin' && $request->role == 'super_admin') {
                    return response()->json('Not Allowed', 405);
                } else {
                    $user->role = $request->role;
                }
            } else {
                return redirect()
                    ->route('users.create')
                    ->withErrors("Sorry, Role is not valid")
                    ->withInput();
            }

            if ($request->active == null) {
                $user->active = 0;
            } else {
                $user->active = 1;
            }

            if ($request->block == null) {
                $user->unblock_at = null;
            } else {
                $user->unblock_at = Carbon::now()->addDays($request->block);
            }
            $user->password = bcrypt($request->password);

            $user->save();

            if ($request->role == "client") {
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
            }

            if (
                $request->subscription_plan == "free" || $request->subscription_plan == "gold" ||
                $request->subscription_plan == "silver" || $request->subscription_plan == "bronze"
            ) {
                $subscription_plan = new SubscriptionPlan();
                $subscription_plan->type = $request->subscription_plan;
                $subscription_plan->expired_at = Carbon::now()->addDays($request->plan);
                $user->subscriptionPlan()->save($subscription_plan);
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } else {
            return redirect('/admin/dashboard');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            $user = User::find($id);

            $roles = ['client' => 'Client', 'content_manager' => 'Content Manager', 'admin' => 'Admin', 'super_admin' => 'Super Admin'];
            $type = ['person' => 'Person', 'company' => 'Company'];
            $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];
            $plan = 0;
            $block = 0;
            $user_plan = null;

            if ($user->role == 'client') {
                $user_plan = $user->subscriptionPlan->type;

                if ($user->subscriptionPlan->expired_at != null) {
                    $p = Carbon::parse($user->subscriptionPlan->expired_at);
                    $plan = $p->diffInDays(Carbon::now());
                }
                if ($user->unblock_at != null) {
                    $b = Carbon::parse($user->unblock_at);
                    $block = $b->diffInDays(Carbon::now());
                }
            }

            return view('dashboard.users.edit', compact(
                'user',
                'roles',
                'subscription_plan',
                'type',
                'block',
                'plan',
                'user_plan'
            ));
        } else {
            return redirect('/admin/dashboard');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            $user = User::find($id);

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {

                return redirect()
                    ->route('users.edit', compact('user'))
                    ->withErrors($validator)
                    ->withInput();
            }

            if (User::where('email', $request->email)->first() && $request->email != $user->email) {
                return redirect()
                    ->route('users.edit', compact('user'))
                    ->withErrors("Sorry, someone's already using that email. Please try a different one.")
                    ->withInput();
            }

            // update company fields
            if ($user->company != null) {
                Company::where('user_id', $id)->update([
                    'first_name' => $request->first_name_company,
                    'last_name' => $request->last_name_company, 'company_name' => $request->company_name,
                    'phone_number' => $request->phone_number_company, 'company_address' => $request->company_address
                ]);
            }

            // update person fields
            if ($user->person != null) {
                Person::where('user_id', $id)->update([
                    'first_name' => $request->first_name, 'last_name' => $request->last_name,
                    'phone_number' => $request->phone_number
                ]);
            }

            // set active user
            if ($request->active == null) {
                $active = 0;
            } else {
                $active = 1;
            }

            // set block user
            if ($request->block == null || $request->block == 0) {
                $unblock_at = null;
            } else {
                $unblock_at = Carbon::now()->addDays($request->block);
            }

            // set role
            if (
                $request->role == "client" || $request->role == "content_manager" ||
                $request->role == "admin" || $request->role == "super_admin"
            ) {
                $role = $request->role;
            } else {
                return redirect()
                    ->route('users.create')
                    ->withErrors("Sorry, Role is not valid")
                    ->withInput();
            }

            if (
                $request->subscription_plan == "free" || $request->subscription_plan == "gold" ||
                $request->subscription_plan == "silver" || $request->subscription_plan == "bronze"
            ) {
                $type = $request->subscription_plan;
                $expired_at = Carbon::now()->addDays($request->plan);
                SubscriptionPlan::where('user_id', $id)->update(['type' => $type, 'expired_at' => $expired_at]);
            }

            // set new password
            if ($request->password != null) {
                $user->password = bcrypt($request->password);
                $user->update();
            }

            User::where('id', $id)->update([
                'email' => $request->email, 'role' => $role, 'active' => $active,
                'unblock_at' => $unblock_at
            ]);

            if ($active == 1) {
                $details = [
                    'title' => 'Hi ' . $request->email,
                    'body' => 'Your account has been verified. You can start using our services. '
                ];
                \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\ActivatedUserMail($details));
            }
            return redirect()->route('users.index')
                ->with('success', 'User updated successfully');
        } else {
            return redirect('/admin/dashboard');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            User::where('id', $id)->delete();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully');
        } else {
            return redirect('/admin/dashboard');
        }
    }

    public function userOnlineStatus()
    {
        $users = DB::table('users')->get();

        return view('dashboard.users.online', compact('users'));
    }

    public function accesses()
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin') {
            $users = User::latest()->orderBy('id', 'asc')->paginate(15);
            return view('dashboard.users.access', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
        } else {
            return redirect('/admin/dashboard');
        }
    }

    public function addContent($id)
    {
        $accessbile_content = Accessible::where('user_id', $id)->get('content_id');

        $accessbile = array();

        foreach ($accessbile_content as $a) {

            array_push($accessbile, $a->content_id);
        }

        $contents = Content::all();

        $user = User::find($id);

        return view('dashboard.users.addContent', compact('accessbile', 'contents', 'user'));
    }

    public function accessible($id, Request $request)
    {
        $accessbile = $request->all();

        $user = User::find($id);

        $user->accessible()->delete();

        foreach ($accessbile as $key => $val) {

            if (is_int($key)) {
                $accessbile = resolve(Accessible::class);

                $accessbile->user_id  = $id;

                $accessbile->content_id = $key;

                $accessbile->save();
            }
        }

        return redirect()->route('accesses')
            ->with('success', 'Accessibles updated successfully');
    }
}
