<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TraccarHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GeofenceController extends Controller
{
    protected $traccarHelper;

    public function __construct()
    {
        $this->traccarHelper = new TraccarHelper();
    }

    public function createGeofence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'area' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            $fieldName = key($errors->messages());

            $response = [
                'success' => false,
                'message' => $firstError,
                'field' => $fieldName
            ];
            return response()->json($response, 200);
        }

        $this->traccarHelper->createGeofence($request->name, $request->description, $request->area);

        return  response()->json(['success' => true, __('messages.create', ['name' => __('labels.texts.geofence')])]);
    }

    public function updateGeofence(Request $request, $geofenceId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $area = DB::table('tc_geofences')->where('id', $geofenceId)->value('area');

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            $fieldName = key($errors->messages());

            $response = [
                'success' => false,
                'message' => $firstError,
                'field' => $fieldName
            ];
            return response()->json($response, 200);
        }

        $this->traccarHelper->updateGeofence($geofenceId, $request->name, $request->description, $area);

        return  response()->json(['success' => true, __('messages.update', ['name' => __('labels.texts.geofence')])]);
    }

    public function updateAreaGeofence(Request $request, $geofenceId)
    {
        $validator = Validator::make($request->all(), [
            'area' => 'required|string',
        ]);

        $name = DB::table('tc_geofences')->where('id', $geofenceId)->value('name');

        if ($validator->fails()) {
            $errors = $validator->errors();
            $firstError = $errors->first();
            $fieldName = key($errors->messages());

            $response = [
                'success' => false,
                'message' => $firstError,
                'field' => $fieldName
            ];
            return response()->json($response, 200);
        }
       
        $this->traccarHelper->updateAreaGeofence($geofenceId, $name, $request->area);

        return  response()->json(['success' => true, __('messages.update', ['name' => __('labels.texts.geofence')])]);
    }

    public function getGeofences()
    {
        $geofence = $this->traccarHelper->getGeofencesTraccar();
        return response()->json($geofence, 200);
    }

    public function destroyGeofence($geofence)
    {
        $geofence = $this->traccarHelper->destroyGeofence($geofence);
        return  response()->json(['success' => true, __('messages.delete', ['name' => __('labels.texts.geofence')])]);
    }
}
