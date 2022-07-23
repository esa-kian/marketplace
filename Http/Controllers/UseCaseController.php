<?php

namespace App\Http\Controllers;

use App\Models\UseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UseCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usecases = UseCase::latest()->orderBy('id', 'asc')->paginate(15);
        return view('dashboard.contents.usecases.index', compact('usecases'))->with('i', (request()->input('page', 1) -1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = ['level1' => 'Level 1 Use Case', 'level2' => 'Level 2 Use Case', 'smcat' => 'SM Category'];

        return view('dashboard.contents.usecases.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('usecases.create')
                            ->withErrors($validator)
                            ->withInput();
        }

        if ($request->type == 'level1' || $request->type == 'level2' || $request->type == 'smcat')
        {
            
            if ($request->type == 'level2')
            {
                $parent_id = $request->level1s;
            }
            else
            {
                $parent_id = null;
            }
        }
        else {
            return redirect()
            ->route('usecases.create')
            ->withErrors('Sorry, Type is not valid')
            ->withInput();
        }
        // dd($parent_id);
        $usecase = UseCase::create([
            'title' => $request->title,
        ]);
        $usecase->type = $request->type;
        $usecase->parent_id = $parent_id;
        $usecase->save();

        return redirect()->route('usecases.index')
            ->with('success','Use case created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usecase = UseCase::findOrFail($id); //db
        return view('dashboard.contents.usecases.show',compact('usecase'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $usecase = UseCase::findOrFail($id); //db

        if ($usecase->parent_id != null)
        {
            $parent = UseCase::where('id', $usecase->parent_id)->get();
        }
        else
        {
            $parent = null;
        }
        $types = ['level1' => 'Level 1 Use Case', 'level2' => 'Level 2 Use Case', 'smcat' => 'SM Category'];

        return view('dashboard.contents.usecases.edit', compact('usecase', 'types', 'parent'));
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) 
        {
          $usecase = UseCase::find($id);
          
          return redirect()->route('usecases.edit', compact('usecase'))
                          ->withErrors($validator)
                          ->withInput();
        }

        if ($request->type == 'level1' || $request->type == 'level2' || $request->type == 'smcat')
        {
            if ($request->type == 'level2')
            {
                $parent_id = $request->level1s;
            }
            else
            {
                $parent_id = null;
            }
        }
        else {
            return redirect()
            ->route('usecases.edit', compact('usecase'))
            ->withErrors('Sorry, Type is not valid')
            ->withInput();
        }

        UseCase::where('id', $id)->update([
            'title' => $request->title, 
            'type' => $request->type, 
            'parent_id' => $parent_id
            ]); //db

        return redirect()->route('usecases.index')
            ->with('success','Use case updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usecase = UseCase::findOrFail($id); //db

        $usecase->delete();

        return redirect()->route('usecases.index')
            ->with('success','Use case deleted successfully');
    }

    public function getLevel1()
    {
        return response()->json(UseCase::where('type', 'level1')->get());
    }

    public function getLevel2(Request $request)
    {
        return response()->json(UseCase::where('type', 'level2')->where('parent_id', $request->title)->get());
    }

    public function getSmcat(Request $request)
    {
        return response()->json(UseCase::where('type', 'smcat')->get());
    }
}
