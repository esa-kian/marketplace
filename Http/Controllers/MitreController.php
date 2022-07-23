<?php

namespace App\Http\Controllers;

use App\Models\Mitre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MitreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mitres = Mitre::latest()->orderBy('id', 'asc')->paginate(15);

        return view('dashboard.contents.mitres.index', compact('mitres'))->with('i', (request()->input('page', 1) -1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = ['tactic' => 'Tactic', 'technique' => 'Technique', 'sub_technique' => 'Sub-Technique'];

        return view('dashboard.contents.mitres.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation: required items

        $validator = Validator::make($request->all(), [
            'mitre_num' => 'required',
            'name' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                        ->route('mitres.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $mitre = new Mitre();
        
        // validation: duplicate mitre id:
        
        if(count(Mitre::where('mitre_num', $request->mitre_num)->get()) != 0)
        {
            return redirect()
                        ->route('mitres.create')
                        ->withErrors('This Mitre ID is already in use!')
                        ->withInput();
        }
        $mitre->mitre_num = $request->mitre_num;
        $mitre->name = $request->name;
        $mitre->type = $request->type;

        // set tactic
        if ($request->type == "tactic")
        {
            $mitre->parent_id = null;
        }
        // set and validation technique
        elseif ($request->type == "technique")
        {
            if ($request->tactics != null)
            {
                $parent_id = DB::table('mitres')->select('id')->where('mitre_num', $request->tactics)->first(); //db
                $mitre->parent_id = $parent_id->id;
            }
            else {
                return redirect()
                        ->route('mitres.create')
                        ->withErrors('Please select a tactic!')
                        ->withInput();
            }
        }
        // set and validation sub technique
        elseif ($request->type == "sub_technique")
        {
            if ($request->techniques != null)
            {
                $parent_id = DB::table('mitres')->select('id')->where('mitre_num', $request->techniques)->first(); //db
                $mitre->parent_id = $parent_id->id;
            }
            else {
                return redirect()
                        ->route('mitres.create')
                        ->withErrors('Please select a technique!')
                        ->withInput();
            }
        }
        else{
            return redirect()
                        ->route('mitres.create')
                        ->withErrors('Please select a type!')
                        ->withInput();
        }

        $mitre->save(); //db

        return redirect()->route('mitres.index')
            ->with('success','Mitre created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mitre = Mitre::findOrFail($id); //db
        return view('dashboard.contents.mitres.show',compact('mitre'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mitre = Mitre::findOrFail($id); //db
        $types = ['tactic' => 'Tactic', 'technique' => 'Technique', 'sub_technique' => 'Sub-Technique'];

        // fetch parent of mitre
        if ($mitre->parent_id != null)
        {
            $parent = Mitre::where('id', $mitre->parent_id)->get();
            foreach($parent as $pr)
            {
                if ($pr->parent_id != null)
                {
                    $parent_parent = Mitre::where('id', $pr->parent_id)->get();
                }
                else
                {
                    $parent_parent = null;
                }
            }
        }
        else
        {
            $parent = null;
        }

        return view('dashboard.contents.mitres.edit',compact('mitre', 'types', 'parent', 'parent_parent'));
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
        $mitre = Mitre::findOrFail($id); //db

        $validator = Validator::make($request->all(), [
            'mitre_num' => 'required',
            'name' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            
            return redirect()
                        ->route('mitres.edit', compact('mitre'))
                        ->withErrors($validator)
                        ->withInput();
        }

        if(count(Mitre::where('mitre_num', $request->mitre_num)->get()) != 0 && $request->mitre_num != $mitre->mitre_num)
        {
            return redirect()
                        ->route('mitres.edit', compact('mitre'))
                        ->withErrors('This Mitre ID is already in use!')
                        ->withInput();
        }

        if ($request->type == "tactic")
        {
            $parent_id = null;
        }
        elseif ($request->type == "technique")
        {
            $parent_id = DB::table('mitres')->select('id')->where('mitre_num', $request->tactics)->first(); //db
        
            $parent_id = $parent_id->id;
        }
        elseif ($request->type == "sub_technique")
        {
            $parent_id = DB::table('mitres')->select('id')->where('mitre_num', $request->techniques)->first(); //db
            $parent_id = $parent_id->id;
        }
        else{
            return redirect()
                        ->route('mitres.edit', compact('mitre'))
                        ->withErrors('Please select a type!')
                        ->withInput();
        }

        Mitre::where('id', $id)->update([
            'name' => $request->name, 
            'mitre_num' => $request->mitre_num,
            'type' => $request->type, 
            'parent_id' => $parent_id
            ]);

        return redirect()->route('mitres.index')
            ->with('success','Mitre updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mitre = Mitre::findOrFail($id); //db

        $mitre->delete();

        return redirect()->route('mitres.index')
            ->with('success','Mitre deleted successfully');
    }

    public function getTactics()
    {
        return response()->json(Mitre::where('type', 'tactic')->get()); // db
    }

    public function getTechniques(Request $request)
    {
        $tactic = Mitre::select('id')->where('mitre_num', $request->num)->get(); // db
        foreach ($tactic as $t)
        {
            return response()->json(Mitre::where('type', 'technique')->where('parent_id', $t->id)->get());
        }
    }

    public function getSubTechniques(Request $request)
    {
        $tactic = Mitre::select('id')->where('mitre_num', $request->num)->get(); // db
        foreach ($tactic as $t)
        {
            return response()->json(Mitre::where('type', 'sub_technique')->where('parent_id', $t->id)->get());
        }
    }
}
