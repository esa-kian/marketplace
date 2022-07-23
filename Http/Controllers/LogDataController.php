<?php

namespace App\Http\Controllers;

use App\Models\LogData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $log_data = LogData::latest()->orderBy('id', 'asc')->paginate(15);

        return view('dashboard.contents.logdata.index', compact('log_data'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.contents.logdata.create');
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
                ->route('log_data.create')
                ->withErrors($validator)
                ->withInput();
        }

        $log_data = new LogData();

        $log_data->title = $request->title;

        $log_data->save();

        return redirect()->route('log_data.index')
            ->with('success', 'Log Data created successfully.');
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
        $log_data = LogData::findOrFail($id);

        return view('dashboard.contents.logdata.edit', compact('log_data'));
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
        $log_data = LogData::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('log_data.edit', compact('log_data'))
                ->withErrors($validator)
                ->withInput();
        }

        $log_data->title = $request->title;

        $log_data->save();

        return redirect()->route('log_data.index')
            ->with('success', 'Log Data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $log_data = LogData::findOrFail($id);

        $log_data->delete();

        return redirect()->route('log_data.index')
            ->with('success', 'Log Data deleted successfully.');
    }

    public function getLogData()
    {
        return response()->json(LogData::all()); // db
    }
}
