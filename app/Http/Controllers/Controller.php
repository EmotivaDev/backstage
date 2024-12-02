<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function convertToLowercase(Request $request)
    {
        $requestData = array_map(function ($value) {
            return is_string($value) ? Str::lower($value) : $value;
        }, $request->all());

        $request->replace($requestData);
    }

    protected function getReverseGeocode($latitude, $longitude)
    {
        $companyId = Auth::user()->company_id;
        $table = ($companyId == 1) ? 'reverse_geocodes' : 'reverse_geocodes_' . $companyId;

        $response = DB::table($table)
            ->select('location', 'milemark', 'description', DB::raw("
                        (6371 * acos(
                            cos(radians({$latitude})) 
                            * cos(radians(latitude)) 
                            * cos(radians(longitude) - radians({$longitude})) 
                            + sin(radians({$latitude})) 
                            * sin(radians(latitude))
                        )) AS distance
                    "))
            ->orderBy('distance', 'asc')
            ->first();
        return $response;
    }

    protected function getAllReverseGeocode()
    {
        $companyId = Auth::user()->company_id;
        $table = ($companyId == 1) ? 'reverse_geocodes' : 'reverse_geocodes_' . $companyId;
        $response = DB::table($table)->orderBy('location')->orderBy('milemark')->get();
        return $response;
    }

    protected function getPositionsLogTable(): string
    {
        if (Auth::user()->id == 1) {
            return 'tc_positions';
        }

        return 'realtimelog_' . Auth::user()->company->root_id_traccar;
    }
}
