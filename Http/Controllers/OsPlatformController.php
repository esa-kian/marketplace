<?php

namespace App\Http\Controllers;

use App\Models\OsPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OsPlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $os_platforms = OsPlatform::latest()->orderBy('id', 'asc')->paginate(15);

        return view('dashboard.contents.osplatforms.index', compact('os_platforms'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.contents.osplatforms.create');
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
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('os_platforms.create')
                ->withErrors($validator)
                ->withInput();
        }

        $os_platform = new OsPlatform();

        $os_platform->title = $request->title;

        $os_platform->save();

        return redirect()->route('os_platforms.index')
            ->with('success', 'OS/Platform created successfully.');
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
        $os_platform = OsPlatform::findOrFail($id);

        return view('dashboard.contents.osplatforms.edit', compact('os_platform'));
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
        $os_platform = OsPlatform::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('os_platforms.edit', compact('os_platform'))
                ->withErrors($validator)
                ->withInput();
        }

        $os_platform->title = $request->title;

        $os_platform->save();

        return redirect()->route('os_platforms.index')
            ->with('success', 'OS/Platform updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $os_platform = OsPlatform::findOrFail($id);

        $os_platform->delete();

        return redirect()->route('os_platforms.index')
            ->with('success', 'OS/Platform deleted successfully.');
    }

    public function getOsPlatforms()
    {
        return response()->json(OsPlatform::all()); // db
    }
}
