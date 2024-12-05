<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TraccarHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use SimpleXMLElement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class RealtimeController extends Controller
{
    protected $traccarHelper;

    public function __construct()
    {
        $this->traccarHelper = new TraccarHelper();
    }

    public function index()
    {
        $tcDevices = $this->traccarHelper->getDevicesTraccar() ?? [];
        $reverseGeocodes = $this->getAllReverseGeocode();
        $trips = $this->getTripDevices();
        $PositionsLog = $this->traccarHelper->getLastRecordForEachDevice();
        $loadTypes = collect(['dry', 'wet', 'mixed']);
        $geofenceTypes = collect(['POLYLINE', 'POLYGON']);
        return view('general.realtime', compact('tcDevices', 'PositionsLog', 'trips', 'reverseGeocodes', 'loadTypes', 'geofenceTypes'));
    }

    public function getTraccarToken(Request $request)
    {
        if (!$request->header('X-Requested-With') || $request->header('X-Requested-With') !== 'XMLHttpRequest') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $tokenTraccar = Auth::user()->token_traccar;

        return response()->json([
            'token' => $tokenTraccar,
        ]);
    }

    public function getTripDevices()
    {
        $tcDevices = $this->traccarHelper->getDevicesTraccar() ?? [];

        $trips = collect($tcDevices)->flatMap(function ($value) {
            return DB::table('trips')->where('deviceid', $value['id'])->get();
           
        })->toArray();

        return $trips;
    }

}
