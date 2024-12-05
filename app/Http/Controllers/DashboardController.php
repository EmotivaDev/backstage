<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TraccarHelper;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $traccarHelper;

    public function __construct()
    {
        $this->traccarHelper = new TraccarHelper();
    }

    public function index()
    {
        return view('general.dashboard');
    }

    public function fleetPosition($date)
    {
      
        $namePositionsLogs = $this->getPositionsLogTable();
        $tcDevices = $this->traccarHelper->getDevicesTraccar() ?? [];
     
        if (empty($tcDevices)) {
            return [
                'devices' => [],
                'statistics_fleet_position' => [
                    'avg_speed' => 0,
                    'totalStops' => 0,
                    'total_active' => 0,
                ],
            ];
        }

        $inputDate = $this->traccarHelper->dateToUTC($date);

        $closestRecords = [];
        $totalSpeed = 0;
        $totalStops = 0;
        $totalActive = 0;
       
        foreach ($tcDevices as $deviceId) {
            $closestRecord = DB::table($namePositionsLogs)
                ->join('tc_devices', "{$namePositionsLogs}.deviceid", '=', 'tc_devices.id')
                ->where("{$namePositionsLogs}.deviceid", $deviceId)
                ->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, devicetime, ?))', [$inputDate])
                ->select("{$namePositionsLogs}.*", 'tc_devices.name as name')
                ->first();

            if ($closestRecord) {
                if (property_exists($closestRecord, 'cog')) {
                    $closestRecord->cog = $closestRecord->course;
                }
                $closestRecord->devicetime = $this->traccarHelper->dateToLocal($closestRecord->devicetime)->toDateTimeString();
                $closestRecord->location = $this->getReverseGeocode($closestRecord->longitude, $closestRecord->latitude);
                $closestRecords[] = $closestRecord;

                if ($closestRecord->speed > 0) {
                    $totalActive++;
                    $totalSpeed += $closestRecord->speed;
                } else {
                    $totalStops++;
                }
            }
        }

        $totalRecords = $totalStops + $totalActive;
        $avgSpeed = $totalRecords > 0 ? round($totalSpeed / $totalRecords, 2) : 0;
      
        return [
            'devices' => $closestRecords,
            'statistics_fleet_position' => [
                'avgSpeed' => $avgSpeed,
                'totalStops' => $totalStops,
                'totalActive' => $totalActive,
            ],
        ];
    }
}
