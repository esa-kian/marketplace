<?php

namespace App\Http\Controllers;

use App\Models\LogSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LogSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $log_sources = LogSource::latest()->orderBy('id', 'asc')->paginate(15);

        return view('dashboard.contents.logsources.index', compact('log_sources'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.contents.logsources.create');
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
                ->route('log_sources.create')
                ->withErrors($validator)
                ->withInput();
        }

        $log_source = new LogSource();

        $log_source->title = $request->title;

        $log_source->save();

        return redirect()->route('log_sources.index')
            ->with('success', 'Log Source created successfully.');
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
        $log_source = LogSource::findOrFail($id);

        return view('dashboard.contents.logsources.edit', compact('log_source'));
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
        $log_source = LogSource::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('log_sources.edit', compact('log_source'))
                ->withErrors($validator)
                ->withInput();
        }

        $log_source->title = $request->title;

        $log_source->save();

        return redirect()->route('log_sources.index')
            ->with('success', 'Log Source updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $log_source = LogSource::findOrFail($id);

        $log_source->delete();

        return redirect()->route('log_sources.index')
            ->with('success', 'Log Source deleted successfully.');
    }

    public function getLogSources()
    {
        return response()->json(LogSource::all()); // db
    }
}
