<?php

namespace App\Http\Controllers;

use App\Models\Accessible;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Mitre;
use App\Models\Picture;
use App\Models\User;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;

class ApplicationController extends Controller
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

    public function index()
    {
        $applications = Content::whereNotNull('application_id')->orderBy('id', 'asc')->latest()->paginate(15);
        return view('dashboard.contents.applications.index', compact('applications'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = ['app' => 'App', 'document' => 'Document'];
        $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

        return view('dashboard.contents.applications.create', compact('subscription_plan', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
        if ($request->type == 'app') {
            $validator = Validator::make($request->all(), [
                'number_of_rules' => 'required',
                'number_of_dashboards' => 'required',
                'number_of_alerts' => 'required',
                'data_sources' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->route('applications.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        } elseif ($request->type == 'document') {
            $validator = Validator::make($request->all(), [
                'author_name' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->route('applications.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'subscription_plan' => 'required',
            'overview' => 'required',
            'details' => 'required',
            'contents' => 'required',
            'requirements' => 'required',
            'version' => 'required',
            'built_by' => 'required',
            'compatibility' => 'required',
            'licensing' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('applications.create')
                ->withErrors($validator)
                ->withInput();
        }
        // end of validation



        // create content
        $content = new Content();
        $content->title = $request->title;

        // enable/disable content
        if ($request->enable == null) {
            $content->enable = 0;
        } else {
            $content->enable = 1;
        }

        // set type
        if ($request->type == 'app' || $request->type == 'document') {
            $content->type = $request->type;
        } else {
            return redirect()->route('applications.create')
                ->withErrors('Please select type of application!')
                ->withInput();
        }

        if (
            $request->subscription_plan == 'gold' || $request->subscription_plan == 'silver' ||
            $request->subscription_plan == 'bronze' || $request->subscription_plan == 'free'
        ) {
            $content->subscription_plan = $request->subscription_plan;
        } else {
            return redirect()->route('applications.create')
                ->withErrors('Please select subscription plan of application!')
                ->withInput();
        }

        $content->author_name = $request->author_name;

        $content->user_id = Auth::id();


        // end end create content

        // create application
        $application = new Application();

        $application->overview = $request->overview;
        $application->details = $request->details;
        $application->release_notes = $request->release_notes;
        $application->contents = $request->contents;
        $application->number_of_rules = $request->number_of_rules;
        $application->number_of_dashboards = $request->number_of_dashboards;
        $application->number_of_alerts = $request->number_of_alerts;

        // set Supported Platforms:
        if ($request->splunk == null) {
            $application->splunk = 0;
        } else {
            $application->splunk = 1;
        }
        if ($request->arcsight == null) {
            $application->arcsight = 0;
        } else {
            $application->arcsight = 1;
        }
        if ($request->qradar == null) {
            $application->qradar = 0;
        } else {
            $application->qradar = 1;
        }
        if ($request->other == null) {
            $application->other = 0;
        } else {
            $application->other = 1;
        }

        $application->data_sources = $request->data_sources;
        $application->requirements = $request->requirements;

        // set Kill Chain Phases:
        if ($request->reconnaissance == null) {
            $application->reconnaissance = 0;
        } else {
            $application->reconnaissance = 1;
        }
        if ($request->weaponization == null) {
            $application->weaponization = 0;
        } else {
            $application->weaponization = 1;
        }
        if ($request->delivery == null) {
            $application->delivery = 0;
        } else {
            $application->delivery = 1;
        }
        if ($request->exploitation == null) {
            $application->exploitation = 0;
        } else {
            $application->exploitation = 1;
        }
        if ($request->installation == null) {
            $application->installation = 0;
        } else {
            $application->installation = 1;
        }
        if ($request->command == null) {
            $application->command_and_control = 0;
        } else {
            $application->command_and_control = 1;
        }
        if ($request->actions == null) {
            $application->actions_on_objective = 0;
        } else {
            $application->actions_on_objective = 1;
        }

        $application->version = $request->version;
        $application->built_by = $request->built_by;
        $application->compatibility = $request->compatibility;
        $application->licensing = $request->licensing;

        $application->save();

        $application->content()->save($content);

        // upload picture

        if ($request->file('image') != null) {
            $image = $request->file('image');
            $imageName = time() . $image->getClientOriginalName();
            $image->move(public_path('pictures'), $imageName);
            $picture = new Picture();
            $picture->url = '/pictures/' . $imageName;
            $picture->title = $imageName;
            $application->pictures()->save($picture);
        }

        // upload doc
        if ($request->file('doc') != null) {
            $doc = $request->file('doc');
            $docName = time() . $doc->getClientOriginalName();
            $doc->move(public_path('attachments'), $docName);
            $document = new Attachment();
            $document->url = '/attachments/' . $docName;
            $document->title = $docName;
            $application->attachments()->save($document);
        }

        // upload app
        if ($request->file('app') != null) {
            $app = $request->file('app');
            $appName = time() . $app->getClientOriginalName();
            $app->move(public_path('attachments'), $appName);
            $applic = new Attachment();
            $applic->url = '/attachments/' . $appName;
            $applic->title = $appName;
            $application->attachments()->save($applic);
        }

        // set tactic
        if ($request->mitre_tactics != null) {
            $mitre_tactic = Mitre::where('mitre_num', $request->mitre_tactics)->first();
            $application->mitres()->attach($mitre_tactic);
        }

        // set technique
        if ($request->mitre_techniques != null) {
            $mitre_technique = Mitre::where('mitre_num', $request->mitre_techniques)->first();
            $application->mitres()->attach($mitre_technique);
        }

        // set sub-technique
        if ($request->sub_techniques != null) {
            $mitre_sub_technique = Mitre::where('mitre_num', $request->sub_techniques)->first();
            $application->mitres()->attach($mitre_sub_technique);
        }

        return redirect()->route('applications.index')
            ->with('success', 'Application created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find(auth()->guard('api')->id());

        $content = Content::with('tags', 'downloads', 'views')->where('enable', 1)
            ->withCount(['votes as rate' => function ($query) {
                $query->select(DB::raw('coalesce(avg(vote),0)'));
            }])->find($id); //db

        if ($content->application_id != null) {
            $application = Application::with('mitres', 'pictures', 'attachments')->find($content->application_id);
        }

        $access = Accessible::where('user_id', auth()->guard('api')->id())->where('content_id', $id)->count();

        if (
            $access == 1 || ($user->role == "admin" || $user->role == "super_admin" || $user->role == "content_manager") ||
            ($content->subscription_plan == 'gold' && $user->subscriptionPlan->type == 'gold') ||
            ($content->subscription_plan == 'silver' && ($user->subscriptionPlan->type == 'gold' || $user->subscriptionPlan->type == 'sliver')) ||
            ($content->subscription_plan == 'bronze' && ($user->subscriptionPlan->type == 'gold' || $user->subscriptionPlan->type == 'sliver' || $user->subscriptionPlan->type == 'bronze') ||
                ($content->subscription_plan == 'free'))
        ) {
            //view part must be separate
            $viewed = DB::table('views')->select('id')->where('content_id', $id)->where('user_id', auth()->guard('api')->id())->get();

            if (count($viewed) == 0) {
                $view = new View();
                $view->content_id = $id;
                $view->user_id =  auth()->guard('api')->id();
                $view->save();
            }
            // end view part

            return response(['content' => $content, 'application' => $application,], 200);
        } else {
            return response('Not Allowed', 405);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $application = Application::find($id);
        $types = ['app' => 'App', 'document' => 'Document'];
        $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

        return view('dashboard.contents.applications.edit', compact(
            'application',
            'types',
            'subscription_plan'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validation
        if ($request->type == 'app') {
            $validator = Validator::make($request->all(), [
                'number_of_rules' => 'required',
                'number_of_dashboards' => 'required',
                'number_of_alerts' => 'required',
                'data_sources' => 'required',
            ]);

            if ($validator->fails()) {
                $application = Application::find($id);

                return redirect()->route('applications.edit', compact('application'))
                    ->withErrors($validator)
                    ->withInput();
            }
        } elseif ($request->type == 'document') {
            $validator = Validator::make($request->all(), [
                'author_name' => 'required',
            ]);
            if ($validator->fails()) {
                $application = Application::find($id);

                return redirect()->route('applications.edit', compact('application'))
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'subscription_plan' => 'required',
            'overview' => 'required',
            'details' => 'required',
            'contents' => 'required',
            'requirements' => 'required',
            'version' => 'required',
            'built_by' => 'required',
            'compatibility' => 'required',
            'licensing' => 'required',
        ]);

        if ($validator->fails()) {
            $application = Application::find($id);

            return redirect()->route('applications.edit', compact('application'))
                ->withErrors($validator)
                ->withInput();
        }
        // end of validation

        // enable/disable content
        if ($request->enable == null) {
            $enable = 0;
        } else {
            $enable = 1;
        }

        // update content fields
        Content::where('application_id', $id)->update([
            'title' => $request->title, 'enable' => $enable,
            'type' => $request->type, 'subscription_plan' => $request->subscription_plan,
            'author_name' => $request->author_name
        ]);
        // end of update content fields

        // set Supported Platforms
        if ($request->splunk == null) {
            $splunk = 0;
        } else {
            $splunk = 1;
        }
        if ($request->arcsight == null) {
            $arcsight = 0;
        } else {
            $arcsight = 1;
        }
        if ($request->qradar == null) {
            $qradar = 0;
        } else {
            $qradar = 1;
        }
        if ($request->other == null) {
            $other = 0;
        } else {
            $other = 1;
        }
        // end of set Supported Platforms

        // set of Kill Chain Phases
        if ($request->reconnaissance == null) {
            $reconnaissance = 0;
        } else {
            $reconnaissance = 1;
        }
        if ($request->weaponization == null) {
            $weaponization = 0;
        } else {
            $weaponization = 1;
        }
        if ($request->delivery == null) {
            $delivery = 0;
        } else {
            $delivery = 1;
        }
        if ($request->exploitation == null) {
            $exploitation = 0;
        } else {
            $exploitation = 1;
        }
        if ($request->installation == null) {
            $installation = 0;
        } else {
            $installation = 1;
        }
        if ($request->command == null) {
            $command_and_control = 0;
        } else {
            $command_and_control = 1;
        }
        if ($request->actions == null) {
            $actions_on_objective = 0;
        } else {
            $actions_on_objective = 1;
        }
        // end of set of Kill Chain Phases

        // update application fields
        Application::where('id', $id)->update([
            'overview' => $request->overview, 'details' => $request->details,
            'release_notes' => $request->release_notes, 'contents' => $request->contents,
            'number_of_rules' => $request->number_of_rules, 'number_of_dashboards' => $request->number_of_dashboards,
            'number_of_alerts' => $request->number_of_alerts, 'splunk' => $splunk, 'arcsight' => $arcsight,
            'qradar' => $qradar, 'other' => $other, 'data_sources' => $request->data_sources,
            'requirements' => $request->requirements, 'reconnaissance' => $reconnaissance,
            'weaponization' => $weaponization, 'delivery' => $delivery, 'exploitation' => $exploitation,
            'installation' => $installation, 'command_and_control' => $command_and_control,
            'actions_on_objective' => $actions_on_objective, 'version' => $request->version,
            'built_by' => $request->built_by, 'compatibility' => $request->compatibility,
            'licensing' => $request->licensing
        ]);
        // end of update application fields

        // update mitres
        $application = Application::find($id);

        if ($request->mitre_tactics != null) {
            $application->mitres()->detach();
            $tactic = Mitre::where('mitre_num', $request->mitre_tactics)->first();
            $application->mitres()->attach($tactic);
        }

        if ($request->mitre_techniques != null) {
            $technique = Mitre::where('mitre_num', $request->mitre_techniques)->first();
            $application->mitres()->attach($technique);
        }

        if ($request->sub_techniques != null) {
            $sub_technique = Mitre::where('mitre_num', $request->sub_techniques)->first();
            $application->mitres()->attach($sub_technique);
        }
        // ene of update mitres

        // upload picture

        if ($request->file('image') != null) {
            $image = $request->file('image');
            $imageName = time() . $image->getClientOriginalName();
            $image->move(public_path('pictures'), $imageName);
            $picture = new Picture();
            $picture->url = '/pictures/' . $imageName;
            $picture->title = $imageName;

            $application->pictures()->delete();
            $application->pictures()->save($picture);
        }
        // upload doc
        if ($request->file('doc') != null) {
            $doc = $request->file('doc');
            $docName = time() . $doc->getClientOriginalName();
            $doc->move(public_path('attachments'), $docName);
            $document = new Attachment();
            $document->url = '/attachments/' . $docName;
            $document->title = $docName;

            $application->attachments()->delete();
            $application->attachments()->save($document);
        }

        // upload app
        if ($request->file('app') != null) {
            $app = $request->file('app');
            $appName = time() . $app->getClientOriginalName();
            $app->move(public_path('attachments'), $appName);
            $applic = new Attachment();
            $applic->url = '/attachments/' . $appName;
            $applic->title = $appName;

            $application->attachments()->delete();
            $application->attachments()->save($applic);
        }
        return redirect()->route('applications.index')
            ->with('success', 'Application updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        $content->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Application deleted successfully');
    }
}
