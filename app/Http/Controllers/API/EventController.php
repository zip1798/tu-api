<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;

use App\Repository\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class EventController extends Controller
{

    public function __construct()
    {
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

        $user = Auth::user()->events()->create($request->all());

        return response()->json(['success' => $user], ApiHelper::SUCCESS_STATUS);
    }

    /**
     * Display the specified resource. (GET)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage. (DELETE)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
