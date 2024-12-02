<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TraccarHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $loadTypes = collect(['dry', 'wet', 'mixed']);

        return view('general.realtime', compact('tcDevices', 'trips', 'reverseGeocodes', 'loadTypes'));
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
