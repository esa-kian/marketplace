<?php

namespace App\Http\Controllers;

use App\Models\Accessible;
use App\Models\Content;
use App\Models\LogData;
use App\Models\LogSource;
use App\Models\Mitre;
use App\Models\OsPlatform;
use App\Models\Rule;
use App\Models\Tag;
use App\Models\UseCase;
use App\Models\User;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Validator;


class RuleController extends Controller
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
        $rules = Content::whereNotNull('rule_id')->orderBy('id', 'asc')->latest()->paginate(15);

        return view('dashboard.contents.rules.index', compact('rules'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = ['siem' => 'SIEM', 'ips_ids' => 'IPS/IDS', 'yara' => 'YARA'];

        $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

        $triggers = [
            'red_blue' => 'Red and blue team-based approach', 'malware' => 'Malware analysis approach',
            'intelligence' => 'Intelligence analysis approach', 'vulnerability' => 'Vulnerability Analysis Approach',
            'exploit' => 'Exploit Analysis Approach', 'platform_based' => 'Platform-based threat approach',
            'log_centric' => 'Log-centric approach', 'human_based' => 'Human-based approach and experiences'
        ];

        $kill_chain_phases = [
            'reconnaissance' => 'Reconnaissance', 'weaponization' => 'Weaponization',
            'delivery' => 'Delivery', 'exploitation' => 'Exploitation', 'installation' => 'Installation',
            'command_and_control' => 'Command and Control', 'actions_objective' => 'Actions on Objective',
        ];

        $levels = ['very_high' => 'Very High', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];

        $status = ['stable' => 'Stable', 'experimental' => 'Experimental'];

        return view(
            'dashboard.contents.rules.create',
            compact('subscription_plan', 'types', 'triggers', 'kill_chain_phases', 'levels', 'status')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'subscription_plan' => 'required',
            'author_name' => 'required',
            'description' => 'required',
            'version' => 'required',
            'false_positive' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('rules.create')
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

        // set type of rule
        if ($request->type == 'siem' || $request->type == 'ips_ids' || $request->type == 'yara') {
            $content->type = $request->type;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select a type!')
                ->withInput();
        }

        // set subscription plan
        if (
            $request->subscription_plan == 'gold' || $request->subscription_plan == 'silver' ||
            $request->subscription_plan == 'bronze' || $request->subscription_plan == 'free'
        ) {
            $content->subscription_plan = $request->subscription_plan;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select a Subscription plan!')
                ->withInput();
        }

        $content->author_name = $request->author_name;

        $content->user_id = Auth::id();

        // end end create content

        // create rule

        $rule = new Rule();

        $rule->author_id = $request->author_id;
        $rule->registrar_pid = $request->registrar_pid;
        $rule->description = $request->description;

        // Rule Type:
        if ($request->hunting == null) {
            $rule->hunting = 0;
        } else {
            $rule->hunting = 1;
        }

        if ($request->detection == null) {
            $rule->detection = 0;
        } else {
            $rule->detection = 1;
        }

        if ($request->correlation == null) {
            $rule->correlation = 0;
        } else {
            $rule->correlation = 1;
        }

        if ($request->monitoring_statistics == null) {
            $rule->monitoring_statistics = 0;
        } else {
            $rule->monitoring_statistics = 1;
        }
        if ($request->intelligence == null) {
            $rule->intelligence = 0;
        } else {
            $rule->intelligence = 1;
        }
        // end Rule Type

        $rule->version = $request->version;
        $rule->references = $request->references;

        // triggers
        if (
            $request->triggers == 'red_blue' || $request->triggers == 'malware' || $request->triggers == 'intelligence'
            || $request->triggers == 'vulnerability' || $request->triggers == 'exploit' || $request->triggers == 'platform_based'
            || $request->triggers == 'log_centric' || $request->triggers == 'human_based'
        ) {
            $rule->triggers = $request->triggers;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select a Trigger!')
                ->withInput();
        }

        // Kill Chain Phase:
        if (
            $request->kill_chain_phases == 'reconnaissance' || $request->kill_chain_phases == 'weaponization'
            || $request->kill_chain_phases == 'delivery' || $request->kill_chain_phases == 'exploitation'
            || $request->kill_chain_phases == 'installation' || $request->kill_chain_phases == 'command_and_control'
            || $request->kill_chain_phases == 'actions_objective'
        ) {
            $rule->kill_chain_phases = $request->kill_chain_phases;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select a Kill Chain Phase!')
                ->withInput();
        }

        $rule->malware_name = $request->malware_name;

        // Security Domain:

        if ($request->endpoint == null) {
            $rule->endpoint = 0;
        } else {
            $rule->endpoint = 1;
        }

        if ($request->network == null) {
            $rule->network = 0;
        } else {
            $rule->network = 1;
        }

        //Priority Level:
        if (
            $request->priority_level == 'very_high' || $request->priority_level == 'high'
            || $request->priority_level == 'medium' || $request->priority_level == 'low'
        ) {
            $rule->priority_level = $request->priority_level;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select Priority Level!')
                ->withInput();
        }

        $rule->detection_logic = $request->detection_logic;

        // status
        if ($request->status == 'stable' || $request->status == 'experimental') {
            $rule->status = $request->status;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select Status!')
                ->withInput();
        }

        $rule->white_black_list = $request->white_black_list;

        // confidence
        if (
            $request->confidence == 'very_high' || $request->confidence == 'high'
            || $request->confidence == 'medium' || $request->confidence == 'low'
        ) {
            $rule->confidence = $request->confidence;
        } else {
            return redirect()->route('rules.create')
                ->withErrors('Please select confidence!')
                ->withInput();
        }

        $rule->false_positive  = $request->false_positive;
        $rule->requirement = $request->requirement;
        $rule->response_solutions = $request->response_solutions;

        // set rule languages

        // SIEM Type:
        if ($request->type == 'siem') {

            if ($request->splunk == null) {
                $rule->splunk = 0;
                $rule->splunk_detection_query = null;
            } else {
                $rule->splunk = 1;
                if ($request->splunk_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Splunk Detection Query!')
                        ->withInput();
                } else {
                    $rule->splunk_detection_query = $request->splunk_detection_query;
                }
            }

            if ($request->elastic == null) {
                $rule->elastic = 0;
                $rule->elastic_detection_query = null;
            } else {
                $rule->elastic = 1;
                if ($request->elastic_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Elastic Detection Query!')
                        ->withInput();
                } else {
                    $rule->elastic_detection_query = $request->elastic_detection_query;
                }
            }

            if ($request->arcsight == null) {
                $rule->arcsight = 0;
                $rule->arcsight_detection_query = null;
            } else {
                $rule->arcsight = 1;
                if ($request->arcsight_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Arcsight Detection Query!')
                        ->withInput();
                } else {
                    $rule->arcsight_detection_query = $request->arcsight_detection_query;
                }
            }

            if ($request->qradar == null) {
                $rule->qradar = 0;
                $rule->qradar_detection_query = null;
            } else {
                $rule->qradar = 1;
                if ($request->qradar_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill QRadar Detection Query!')
                        ->withInput();
                } else {
                    $rule->qradar_detection_query = $request->qradar_detection_query;
                }
            }

            if ($request->python == null) {
                $rule->python = 0;
                $rule->python_detection_query = null;
            } else {
                $rule->python = 1;
                if ($request->python_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Python Detection Query!')
                        ->withInput();
                } else {
                    $rule->python_detection_query = $request->python_detection_query;
                }
            }

            if ($request->eql == null) {
                $rule->eql = 0;
                $rule->eql_detection_query = null;
            } else {
                $rule->eql = 1;
                if ($request->eql_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill EQL Detection Query!')
                        ->withInput();
                } else {
                    $rule->eql_detection_query = $request->eql_detection_query;
                }
            }

            if ($request->sigma == null) {
                $rule->sigma = 0;
                $rule->sigma_detection_query = null;
            } else {
                $rule->sigma = 1;
                if ($request->sigma_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Sigma Detection Query!')
                        ->withInput();
                } else {
                    $rule->sigma_detection_query = $request->sigma_detection_query;
                }
            }
        }

        // IPS/IDS Type:
        elseif ($request->type == 'ips_ids') {

            if ($request->suricata == null) {
                $rule->suricata = 0;
                $rule->suricata_detection_query = null;
            } else {
                $rule->suricata = 1;
                if ($request->suricata_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Suricata Detection Query!')
                        ->withInput();
                } else {
                    $rule->suricata_detection_query = $request->suricata_detection_query;
                }
            }

            if ($request->snort == null) {
                $rule->snort = 0;
                $rule->snort_detection_query = null;
            } else {
                $rule->snort = 1;
                if ($request->snort_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill Snort Detection Query!')
                        ->withInput();
                } else {
                    $rule->snort_detection_query = $request->snort_detection_query;
                }
            }
        }
        // YARA Type:
        elseif ($request->type == 'yara') {

            if ($request->yara == null) {
                $rule->yara = 0;
                $rule->yara_detection_query = null;
            } else {
                $rule->yara = 1;
                if ($request->yara_detection_query == null) {
                    return redirect()->route('rules.create')
                        ->withErrors('Please fill YARA Detection Query!')
                        ->withInput();
                } else {
                    $rule->yara_detection_query = $request->yara_detection_query;
                }
            }
        }


        $rule->save();
        $rule->content()->save($content);

        // set level 1 use case
        if ($request->level1 != null) {
            $level1_usecase = UseCase::where('id', $request->level1)->first();
            $rule->useCases()->attach($level1_usecase);
        }

        // set level 2 use case 
        if ($request->level2 != null) {
            $level2_usecase = UseCase::where('id', $request->level2)->first();
            $rule->useCases()->attach($level2_usecase);
        }

        // set sm category
        if ($request->smcat) {
            $smcat_usecase = UseCase::where('id', $request->smcat)->first();
            $rule->useCases()->attach($smcat_usecase);
        }

        // log source
        if ($request->log_source) {
            $log_source = LogSource::where('id', $request->log_source)->first();
            $rule->logSources()->attach($log_source);
        }

        // log data
        if ($request->log_data) {
            $log_data = LogData::where('id', $request->log_data)->first();
            $rule->logData()->attach($log_data);
        }

        // os/platform/firmware
        if ($request->os_platform_firmware) {
            $os_platform_firmware = OsPlatform::where('id', $request->os_platform_firmware)->first();
            $rule->osPlatforms()->attach($os_platform_firmware);
        }


        if ($request->mitre_tactics != null) {
            $mitre_tactic = Mitre::where('mitre_num', $request->mitre_tactics)->first();
            $rule->mitres()->attach($mitre_tactic);
        }

        if ($request->mitre_techniques != null) {
            $mitre_technique = Mitre::where('mitre_num', $request->mitre_techniques)->first();
            $rule->mitres()->attach($mitre_technique);
        }

        if ($request->sub_techniques != null) {
            $mitre_sub_technique = Mitre::where('mitre_num', $request->sub_techniques)->first();
            $rule->mitres()->attach($mitre_sub_technique);
        }

        // save tags of rule 
        if ($request->tags != null) {
            $tags = explode(" ", $request->tags);
            foreach ($tags as $t) {
                $tag = Tag::where('name', $t)->first();
                if ($tag != null) {
                    $content->tags()->attach($tag);
                } else {
                    $tag = Tag::create(['name' => $t]);
                    $content->tags()->attach($tag);
                }
            }
        }


        return redirect()->route('rules.index')
            ->with('success', 'Rule created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $user = auth()->guard('api')->user();
        $user = User::find(auth()->guard('api')->id());
        $content = Content::with('tags', 'downloads', 'views')->where('enable', 1)
            ->withCount(['votes as rate' => function ($query) {
                $query->select(DB::raw('coalesce(avg(vote),0)'));
            }])->find($id); //db
        if ($content->rule_id != null) {
            $rule = Rule::with('usecases', 'mitres', 'logSources', 'logData', 'osPlatforms')->find($content->rule_id);
        }

        // $comments = Comment::where('content_id', $id)->get();
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

            return response(['content' => $content, 'rule' => $rule,], 200);
        } else {
            return response('Not Allowed', 405);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rule = Rule::find($id);

        $tags = $rule->content->tags;

        $types = ['siem' => 'SIEM', 'ips_ids' => 'IPS/IDS', 'yara' => 'YARA'];

        $subscription_plan = ['free' => 'Free', 'gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

        $triggers = [
            'red_blue' => 'Red and blue team-based approach', 'malware' => 'Malware analysis approach',
            'intelligence' => 'Intelligence analysis approach', 'vulnerability' => 'Vulnerability Analysis Approach',
            'exploit' => 'Exploit Analysis Approach', 'platform_based' => 'Platform-based threat approach',
            'log_centric' => 'Log-centric approach', 'human_based' => 'Human-based approach and experiences'
        ];

        $kill_chain_phases = [
            'reconnaissance' => 'Reconnaissance', 'weaponization' => 'Weaponization',
            'delivery' => 'Delivery', 'exploitation' => 'Exploitation', 'installation' => 'Installation',
            'command_and_control' => 'Command and Control', 'actions_objective' => 'Actions on Objective',
        ];

        $levels = ['very_high' => 'Very High', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];

        $status = ['stable' => 'Stable', 'experimental' => 'Experimental'];

        return view('dashboard.contents.rules.edit', compact(
            'rule',
            'types',
            'subscription_plan',
            'triggers',
            'kill_chain_phases',
            'levels',
            'status',
            'tags',
        ));
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
        // validation
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
            'subscription_plan' => 'required',
            'author_name' => 'required',
            'description' => 'required',
            'version' => 'required',
            'false_positive' => 'required',
        ]);

        if ($validator->fails()) {
            $rule = Rule::find($id);

            return redirect()->route('rules.create', compact('rule'))
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

        $splunk = null;
        $elastic = null;
        $arcsight = null;
        $qradar = null;
        $python = null;
        $eql = null;
        $sigma = null;
        $yara = null;
        $suricata = null;
        $snort = null;
        $splunk_detection_query = null;
        $elastic_detection_query = null;
        $arcsight_detection_query = null;
        $qradar_detection_query = null;
        $python_detection_query = null;
        $eql_detection_query = null;
        $sigma_detection_query = null;
        $suricata_detection_query = null;
        $snort_detection_query = null;
        $yara_detection_query = null;
        $content = Content::where('rule_id', $id)->first();

        // update content fields
        Content::where('rule_id', $id)->update([
            'title' => $request->title, 'enable' => $enable,
            'type' => $request->type, 'subscription_plan' => $request->subscription_plan,
            'author_name' => $request->author_name
        ]);
        // end of update content fields

        // enable/disable content


        // set Rule Type
        if ($request->hunting == null) {
            $hunting = 0;
        } else {
            $hunting = 1;
        }
        if ($request->detection == null) {
            $detection = 0;
        } else {
            $detection = 1;
        }
        if ($request->correlation == null) {
            $correlation = 0;
        } else {
            $correlation = 1;
        }
        if ($request->monitoring_statistics == null) {
            $monitoring_statistics = 0;
        } else {
            $monitoring_statistics = 1;
        }
        if ($request->intelligence == null) {
            $intelligence = 0;
        } else {
            $intelligence = 1;
        }
        // end of set Rule Type

        // set Security Domain
        if ($request->endpoint == null) {
            $endpoint = 0;
        } else {
            $endpoint = 1;
        }
        if ($request->network == null) {
            $network = 0;
        } else {
            $network = 1;
        }
        // end of set Security Domain

        // set rule languages
        // SIEM Type:
        if ($request->type == 'siem') {

            if ($request->splunk == null) {
                $splunk = 0;
                $splunk_detection_query = null;
            } else {
                $splunk = 1;
                if ($request->splunk_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Splunk Detection Query!')
                        ->withInput();
                } else {
                    $splunk_detection_query = $request->splunk_detection_query;
                }
            }

            if ($request->elastic == null) {
                $elastic = 0;
                $elastic_detection_query = null;
            } else {
                $elastic = 1;
                if ($request->elastic_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Elastic Detection Query!')
                        ->withInput();
                } else {
                    $elastic_detection_query = $request->elastic_detection_query;
                }
            }

            if ($request->arcsight == null) {
                $arcsight = 0;
                $arcsight_detection_query = null;
            } else {
                $arcsight = 1;
                if ($request->arcsight_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Arcsight Detection Query!')
                        ->withInput();
                } else {
                    $arcsight_detection_query = $request->arcsight_detection_query;
                }
            }

            if ($request->qradar == null) {
                $qradar = 0;
                $qradar_detection_query = null;
            } else {
                $qradar = 1;
                if ($request->qradar_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill QRadar Detection Query!')
                        ->withInput();
                } else {
                    $qradar_detection_query = $request->qradar_detection_query;
                }
            }

            if ($request->python == null) {
                $python = 0;
                $python_detection_query = null;
            } else {
                $python = 1;
                if ($request->python_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Python Detection Query!')
                        ->withInput();
                } else {
                    $python_detection_query = $request->python_detection_query;
                }
            }

            if ($request->eql == null) {
                $eql = 0;
                $eql_detection_query = null;
            } else {
                $eql = 1;
                if ($request->eql_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill EQL Detection Query!')
                        ->withInput();
                } else {
                    $eql_detection_query = $request->eql_detection_query;
                }
            }

            if ($request->sigma == null) {
                $sigma = 0;
                $sigma_detection_query = null;
            } else {
                $sigma = 1;
                if ($request->sigma_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Sigma Detection Query!')
                        ->withInput();
                } else {
                    $sigma_detection_query = $request->sigma_detection_query;
                }
            }
        }

        // IPS/IDS Type:
        elseif ($request->type == 'ips_ids') {

            if ($request->suricata == null) {
                $suricata = 0;
                $suricata_detection_query = null;
            } else {
                $suricata = 1;
                if ($request->suricata_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Suricata Detection Query!')
                        ->withInput();
                } else {
                    $suricata_detection_query = $request->suricata_detection_query;
                }
            }

            if ($request->snort == null) {
                $snort = 0;
                $snort_detection_query = null;
            } else {
                $snort = 1;
                if ($request->snort_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill Snort Detection Query!')
                        ->withInput();
                } else {
                    $snort_detection_query = $request->snort_detection_query;
                }
            }
        }
        // YARA Type:
        elseif ($request->type == 'yara') {

            if ($request->yara == null) {
                $yara = 0;
                $yara_detection_query = null;
            } else {
                $yara = 1;
                if ($request->yara_detection_query == null) {
                    return redirect()->route('rules.edit')
                        ->withErrors('Please fill YARA Detection Query!')
                        ->withInput();
                } else {
                    $yara_detection_query = $request->yara_detection_query;
                }
            }
        }
        // end of set Rule Languages


        // update rule fields
        Rule::where('id', $id)->update([
            'author_id' => $request->author_id, 'registrar_pid' => $request->registrar_pid,
            'description' => $request->description, 'hunting' => $hunting, 'detection' => $detection,
            'correlation' => $correlation, 'monitoring_statistics' => $monitoring_statistics,
            'intelligence' => $intelligence, 'version' => $request->version,
            'references' => $request->references, 'triggers' => $request->triggers,
            'kill_chain_phases' => $request->kill_chain_phases, 'malware_name' => $request->malware_name,
            'endpoint' => $endpoint, 'network' => $network, 'priority_level' => $request->priority_level,
            'detection_logic' => $request->detection_logic, 'status' => $request->status,
            'white_black_list' => $request->white_black_list, 'confidence' => $request->confidence,
            'false_positive' => $request->false_positive, 'requirement' => $request->requirement,
            'response_solutions' => $request->response_solutions,
            'splunk' => $splunk, 'elastic' => $elastic, 'arcsight' => $arcsight,
            'qradar' => $qradar, 'python' => $python, 'eql' => $eql, 'sigma' => $sigma, 'yara' => $yara,
            'suricata' => $suricata, 'snort' => $snort,
            'splunk_detection_query' => $splunk_detection_query, 'elastic_detection_query' => $elastic_detection_query,
            'arcsight_detection_query' => $arcsight_detection_query, 'qradar_detection_query' => $qradar_detection_query,
            'python_detection_query' => $python_detection_query, 'eql_detection_query' => $eql_detection_query,
            'sigma_detection_query' => $sigma_detection_query,
            'suricata_detection_query' => $suricata_detection_query, 'snort_detection_query' => $snort_detection_query,
            'yara_detection_query' => $yara_detection_query
        ]);
        // end of update rule fields

        // update mitres
        $rule = Rule::find($id);

        if ($request->mitre_tactics != null) {
            $rule->mitres()->detach();
            $tactic = Mitre::where('mitre_num', $request->mitre_tactics)->first();
            $rule->mitres()->attach($tactic);
        }

        if ($request->mitre_techniques != null) {
            $technique = Mitre::where('mitre_num', $request->mitre_techniques)->first();
            $rule->mitres()->attach($technique);
        }

        if ($request->sub_techniques != null) {
            $sub_technique = Mitre::where('mitre_num', $request->sub_techniques)->first();
            $rule->mitres()->attach($sub_technique);
        }
        // ene of update mitres

        // update use cases
        if ($request->level1 != null) {
            $rule->useCases()->detach();
            $level1_usecase = UseCase::where('id', $request->level1)->first();
            $rule->useCases()->attach($level1_usecase);
        }

        if ($request->level2 != null) {
            $level2_usecase = UseCase::where('id', $request->level2)->first();
            $rule->useCases()->attach($level2_usecase);
        }

        if ($request->smcat != null) {
            $smcat_usecase = UseCase::where('id', $request->smcat)->first();
            $rule->useCases()->attach($smcat_usecase);
        }
        // end of update use cases

        // update log source
        if ($request->log_source != null) {
            $rule->logSources()->detach();
            $log_source = LogSource::where('id', $request->log_source)->first();
            $rule->logSources()->attach($log_source);
        }

        // update log data
        if ($request->log_data != null) {
            $rule->logData()->detach();
            $log_data = LogData::where('id', $request->log_data)->first();
            $rule->logData()->attach($log_data);
        }

        // update  os/platform/firmware
        if ($request->os_platform_firmware != null) {
            $rule->osPlatforms()->detach();
            $os_platform_firmware = OsPlatform::where('id', $request->os_platform_firmware)->first();
            $rule->osPlatforms()->attach($os_platform_firmware);
        }

        // save tags of rule 
        if ($request->tags != null) {
            $content->tags()->detach();
            $tags = explode(" ", $request->tags);
            foreach ($tags as $t) {
                $tag = Tag::where('name', $t)->first();
                if ($tag != null) {
                    $content->tags()->attach($tag);
                } else {
                    $tag = Tag::create(['name' => $t]);
                    $content->tags()->attach($tag);
                }
            }
        }

        return redirect()->route('rules.index')
            ->with('success', 'Rule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        $content->delete();

        return redirect()->route('rules.index')
            ->with('success', 'Rule deleted successfully');
    }
}
