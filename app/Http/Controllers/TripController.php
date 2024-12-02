<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'deviceid' => 'required|integer',
            'number' => 'required|integer',
            'origin' => 'nullable|string',
            'destination' => 'nullable|string',
            'draft' => 'nullable|string',
            'load_type' => 'nullable|string',
            'tonnes' => 'nullable|string',
            'bargues' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $existTrip = DB::table('trips')->where('deviceid', $request->deviceid)->value('number');
        if ($existTrip != null) {
            $response = [
                'success' => false,
                'message' => __('messages.error_exist_trip'),
            ];
            return response()->json($response, 200);
        }

        $update = DB::table('trips')
            ->where('deviceid', $request->deviceid)
            ->update([
                'number' => $request->number,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'draft' => $request->draft,
                'loadtype' => $request->load_type,
                'tonnes' => $request->tonnes,
                'bargues' => $request->bargues,
                'description' => $request->description,
            ]);

        if ($update > 0) {
            return  response()->json(['success' => true, __('messages.create', ['name' => __('labels.texts.trip')])]);
        } else {
            return  response()->json(['success' => false, __('messages.create', ['name' => __('labels.texts.not_create')])]);
        }
    }

    public function show($deviceId)
    {
        $trip = DB::table('trips')->where('deviceid', $deviceId)->get();
        return  response()->json(['success' => true, 'trip' => $trip[0]]);
    }

    public function update(Request $request, $deviceId)
    {
        $request->validate([
            'number' => 'required|integer',
            'origin' => 'nullable|string',
            'destination' => 'nullable|string',
            'draft' => 'nullable|string',
            'load_type' => 'nullable|string',
            'tonnes' => 'nullable|string',
            'bargues' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $existTrip = DB::table('trips')->where('deviceid', $deviceId)->value('number');
        if ($existTrip == null) {
            $response = [
                'success' => false,
                'message' => __('messages.error_not_exist_trip'),
            ];
            return response()->json($response, 200);
        }

        $update = DB::table('trips')
            ->where('deviceid', $deviceId)
            ->update([
                'origin' => $request->origin,
                'destination' => $request->destination,
                'draft' => $request->draft,
                'loadtype' => $request->load_type,
                'tonnes' => $request->tonnes,
                'bargues' => $request->bargues,
                'description' => $request->description,
            ]);

        return  response()->json(['success' => true, __('messages.update', ['name' => __('labels.texts.trip')])]);
    }

    public function finish($deviceId)
    {
        $existTrip = DB::table('trips')->where('deviceid', $deviceId)->value('number');
        if ($existTrip == null) {
            $response = [
                'success' => false,
                'message' => __('messages.error_not_exist_trip'),
            ];
            return response()->json($response, 200);
        }

        $update = DB::table('trips')
            ->where('deviceid', $deviceId)
            ->update([
                'number' => null,
                'origin' => null,
                'destination' => null,
                'draft' => null,
                'loadtype' => null,
                'tonnes' => null,
                'bargues' => null,
                'description' => null,
            ]);

        if ($update > 0) {
            return  response()->json(['success' => true, __('messages.finish', ['name' => __('labels.texts.trip')])]);
        } else {
            return  response()->json(['success' => false, __('messages.finish', ['name' => __('labels.texts.not_finish')])]);
        }
    }
}
