<?php

namespace App\Http\Controllers\API;

use App\Event;
use App\Repository\EventRepository;
use Illuminate\Support\Facades\Auth;

use App\Repository\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class EventController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
        $this->middleware('role:moderator', ['only' => ['store', 'update']]);
        $this->middleware('role:admin',   ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource. (GET)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage. (POST)
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
            return response()->json(['error' => $validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $event = Auth::user()->events()->create($request->all());

        return response()->json(['success' => $event], ApiHelper::SUCCESS_STATUS);
    }

    public function show($id)
    {
        $event = Event::with('media')->findOrFail($id)->setAppends(['is_interested', 'is_registered']);

        return response()->json(['success' => $event], ApiHelper::SUCCESS_STATUS);
    }

    /**
     * Update the specified resource in storage. (PUT|PATCH)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $event = Event::findOrFail($id);
        $event->update($request->all());

        return $this->show($id);
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['success' => 'OK'], ApiHelper::SUCCESS_STATUS);
    }

    public function interested($id) {
        $repository = new EventRepository();

        return response()->json(['success' => $repository->toogleInterested($id)], ApiHelper::SUCCESS_STATUS);
    }

    public function test()
    {
        return response()->json(['success' => auth('api')->user()], ApiHelper::SUCCESS_STATUS);
    }



}
