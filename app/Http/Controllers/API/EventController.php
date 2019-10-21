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

    public function index()
    {
        $repository = new EventRepository();
        return response()->json(['success' => $repository->getFuturePublicEventsList()], ApiHelper::SUCCESS_STATUS);
    }

    public function user_index()
    {
        $repository = new EventRepository();
        $user = Auth::user();
        if ($user->hasRole('moderator')) {
            $result = $repository->getFullEventList();
        } else {
            $result = $repository->getUserEventList();
        }

        return response()->json(['success' => $result], ApiHelper::SUCCESS_STATUS);
    }


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

    public function register(Request $request)
    {
        $repository = new EventRepository();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email',
            'event_id' => 'required', //todo add validation allow open registration for event
        ]);
        try {
            if ($validator->fails()) {
                throw new \Exception($validator->errors(), ApiHelper::ERROR_VALIDATE_STATUS);
            }
            return response()->json(
                ['success' => $repository->eventRegistration($request->only(['event_id', 'name', 'email', 'city', 'phone', 'comments']))],
                ApiHelper::SUCCESS_STATUS);

        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function test()
    {
        return response()->json(['success' => auth('api')->user()], ApiHelper::SUCCESS_STATUS);
    }



}
