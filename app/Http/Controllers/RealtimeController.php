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

        return view('general.realtime', compact('tcDevices', 'PositionsLog', 'trips', 'reverseGeocodes', 'loadTypes'));
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
    

    /*////////////////////////*/

    public function uploadTrail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimetypes:text/xml,application/octet-stream',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->users_id != null) {
            $users_id = json_encode($request->input('users_id'));
        } else {
            $users_id = null;
        }


        $originalName = $request->file('file')->getClientOriginalName();
        $fileName = strtolower($originalName);

        $fileExtension = $request->file('file')->getClientOriginalExtension();

        $array_waypoints = [];
        $array_routes = [];
        $array_marks = [];
        $array_tracks = [];

        if ($fileExtension === 'gpx') {
            $file = $request->file('file');
            $contents = file_get_contents($file->getRealPath());

            $contents = preg_replace('/xmlns:gpxx="[^"]*"/', '', $contents);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($contents);

            $kml = new SimpleXMLElement('<kml/>');
            $kml->addAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
            $document = $kml->addChild('Document');
            $style = $document->addChild('Style');
            $style->addAttribute('id', 'trackStyle');
            $lineStyle = $style->addChild('LineStyle');
            $lineStyle->addChild('color', 'ff0000ff');
            $lineStyle->addChild('width', '4');

            // waypoints
            foreach ($xml->wpt as $wpt) {
                $placemark = $document->addChild('Placemark');
                $point = $placemark->addChild('Point');
                $point->addChild('coordinates', "{$wpt['lon']},{$wpt['lat']}");
                if ($wpt->name) {
                    $placemark->addChild('name', (string) $wpt->name);
                }
                if ($wpt->desc) {
                    $placemark->addChild('description', (string) $wpt->desc);
                }
                $array_waypoints[] = array('name' => (string) $wpt->name, 'type' => 'waypoints', 'properties' =>  (string) $wpt->desc, 'points' => "{$wpt['lon']},{$wpt['lat']}");
            }

            // routes
            foreach ($xml->rte as $rte) {
                $placemark = $document->addChild('Placemark');
                $linestring = $placemark->addChild('LineString');
                $coordinates = [];
                foreach ($rte->rtept as $rtept) {
                    $coordinates[] = "{$rtept['lon']},{$rtept['lat']}";
                }
                $linestring->addChild('coordinates', implode(' ', $coordinates));
                if ($rte->name) {
                    $placemark->addChild('name', (string) $rte->name);
                }
                if ($rte->desc) {
                    $placemark->addChild('description', (string) $rte->desc);
                }
                $array_routes[] = array('name' => (string) $rte->name, 'type' => 'routes', 'properties' =>  (string) $rte->desc, 'points' => $coordinates);
            }

            // tracks
            foreach ($xml->trk as $trk) {
                $placemark = $document->addChild('Placemark');
                $placemark->addChild('styleUrl', '#trackStyle');
                $linestring = $placemark->addChild('LineString');
                $coordinates = [];
                $coordinatesAux = [];
                foreach ($trk->trkseg as $trkseg) {
                    foreach ($trkseg->trkpt as $trkpt) {
                        $coordinates[] = "{$trkpt['lon']},{$trkpt['lat']}";
                        $coordinatesAux[] = "{$trkpt['lon']} {$trkpt['lat']}";
                    }
                }
                $linestring->addChild('coordinates', implode(' ', $coordinates));
                if ($trk->name) {
                    $placemark->addChild('name', (string) $trk->name);
                }
                if ($trk->desc) {
                    $placemark->addChild('description', (string) $trk->desc);
                }
                $array_tracks = array('name' => (string) $trk->name, 'type' => 'tracks', 'properties' => (string) $trk->desc, 'points' => $coordinatesAux);
            }

            $xmlContent = $kml->asXML();
            $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            $savefileName = 'trails/kml/' . $fileNameWithoutExtension . time() . '.kml';
            Storage::disk('public')->put($savefileName, $xmlContent);
        } elseif ($fileExtension === 'nob') {
            $file = $request->file('file');
            $xml = simplexml_load_file($file->path());
            $kml = new SimpleXMLElement('<kml/>');
            $kml->addAttribute('xmlns', 'http://www.opengis.net/kml/2.2');
            $document = $kml->addChild('Document');

            foreach ($xml->Track as $track) {
                $trackPlacemark = $kml->Document->addChild('Placemark');

                $name = (string)$track->Name;
                $startTime = (string)$track->StartTime;

                $trackPlacemark->addChild('name', $name);
                $trackPlacemark->addChild('description', "Start Time: $startTime");

                $style = $trackPlacemark->addChild('Style');
                $lineStyle = $style->addChild('LineStyle');
                $lineStyle->addChild('color', 'ffff00ff');
                $lineStyle->addChild('width', '4');

                $trackMarks = (string)$track->TrackMarks;
                $trackMarkLines = explode(';', $trackMarks);
                $coordinates = '';
                $coordinatesAux = [];

                if ($trackMarkLines) {
                    foreach ($trackMarkLines as $trackMarkLine) {

                        $trackMarkData = explode(' ', trim($trackMarkLine));
                        if (!$trackMarkData[0]) {
                            break;
                        }

                        $time = $trackMarkData[0];

                        $lat = floatval($trackMarkData[1]);
                        $latDirection = $trackMarkData[2];
                        $lng = floatval($trackMarkData[3]);
                        $lngDirection = $trackMarkData[4];

                        if ($latDirection === 'S') {
                            $lat = -$lat;
                        }

                        if ($lngDirection === 'W') {
                            $lng = -$lng;
                        }

                        $coordinates .= "$lng,$lat,0 ";
                        $coordinatesAux[] = "$lng $lat";
                    }
                }
                $array_tracks = array('name' => $name, 'type' => 'tracks', 'properties' => '', 'points' => $coordinatesAux);
                $lineStringElement = $trackPlacemark->addChild('LineString');
                $lineStringElement->addChild('coordinates', rtrim($coordinates));
            }

            foreach ($xml->Mark as $mark) {

                $placemark = $kml->Document->addChild('Placemark');

                $style = $placemark->addChild('Style');
                $iconStyle = $style->addChild('IconStyle');
                $icon = $iconStyle->addChild('Icon');

                $name1 = (string)$mark->Name;
                $tempName1 = trim($name1);
                $tempName1 = str_replace(" ", "%20", $tempName1);
                $icon->addChild('href', 'https://impala.emotiva.com.co/upgrade/gps/image.php?text=' . $tempName1);

                $name = (string)$mark->Name;
                $description = (string)$mark->Description;
                $position = (string)$mark->Position;

                $positionParts = explode(' ', $position);
                $lat = floatval($positionParts[0]);
                $latDirection = $positionParts[1];
                $lng = floatval($positionParts[2]);
                $lngDirection = $positionParts[3];

                if ($latDirection === 'S') {
                    $lat = -$lat;
                }

                if ($lngDirection === 'W') {
                    $lng = -$lng;
                }

                $extendedData = $placemark->addChild('ExtendedData');
                $nameData = $extendedData->addChild('Data');
                $nameData->addAttribute('name', 'Name');
                $nameValue = $nameData->addChild('value', $name);

                $descData = $extendedData->addChild('Data');
                $descData->addAttribute('name', 'Description');
                $descValue = $descData->addChild('value', $description);

                $pointElement = $placemark->addChild('Point');
                $coordinates = $pointElement->addChild('coordinates', "$lng,$lat,0");

                $array_marks[] = array('name' => $name, 'type' => 'marks', 'properties' =>  $description, 'points' => "$lng $lat,");
            }

            $xmlContent = $kml->asXML();
            $fileNameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME);
            $savefileName = 'trails/kml/' . $fileNameWithoutExtension . time() . '.kml';
            Storage::disk('public')->put($savefileName, $xmlContent);
        } else {
            return back()->withErrors(['file' => 'Formato de archivo no soportado'])->withInput();
        }


        $id = DB::table('trails')->insertGetId([
            'company_id' => auth()->user()->company->id,
            'users_id' => $users_id,
            'name' => $fileNameWithoutExtension,
            'kml' => $savefileName,
            'uploaded_by' => auth()->user()->id,
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($id != null) {
            foreach ($array_waypoints as $key => $value) {
                $coordinates = explode(',', $value['points']);
                $longitude = $coordinates[0];
                $latitude = $coordinates[1];

                DB::table('trails_details')->insert([
                    'trail_id' => $id,
                    'name' =>  $value['name'],
                    'type' =>  $value['type'],
                    'points' => DB::raw("ST_GeomFromText('POINT($longitude $latitude)')"),
                    'properties'  => json_encode($value['properties'])
                ]);
            }
            foreach ($array_routes as $key => $value) {
                $coordinates = explode(',', $value['points']);
                $longitude = $coordinates[0];
                $latitude = $coordinates[1];

                DB::table('trails_details')->insert([
                    'trail_id' => $id,
                    'name' =>  $value['name'],
                    'type' =>  $value['type'],
                    'points' => DB::raw("ST_GeomFromText('POINT($longitude $latitude)')"),
                    'properties'  => json_encode($value['properties'])
                ]);
            }

            foreach ($array_marks as $key => $value) {
                $coordinates = explode(',', $value['points']);
                $longitude = $coordinates[0];
                $latitude = $coordinates[1];

                DB::table('trails_details')->insert([
                    'trail_id' => $id,
                    'name' =>  $value['name'],
                    'type' =>  $value['type'],
                    'points' => DB::raw("ST_GeomFromText('POINT($longitude $latitude)')"),
                    'properties'  => json_encode($value['properties'])
                ]);
            }

            if (!empty($array_tracks)) {
                $pointStrings = implode(', ', $array_tracks['points']);

                DB::table('trails_details')->insert([
                    'trail_id' => $id,
                    'name' =>  $array_tracks['name'],
                    'type' =>   $array_tracks['type'],
                    'points' => DB::raw("ST_GeomFromText('LINESTRING($pointStrings)')"),
                    'properties' => json_encode($array_tracks['properties'])
                ]);
            }
        }

        return back()->with('success', 'Archivo subido y procesado exitosamente!');
    }



    public function destroyTrail($id)
    {
        $path_file =  DB::table('trails')->where('id', $id)->value('kml');

        if ($path_file) {
            Storage::disk('public')->delete($path_file);
            DB::table('trails_details')->where('trail_id', $id)->delete();
            DB::table('trails')->where('id', $id)->delete();
            $file = true;
        } else {
            $file = false;
        }

        return $file;
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

        try {
            $client = new Client();
            $root = auth()->user()->company->email;
            $password = auth()->user()->company->root_pass_traccar;


            $url_traccar = env('URL_TRACCAR');

            $response =  $client->post($url_traccar . '/api/geofences', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth' => [$root, $password],
                'json' => [
                    'name' => $request->name,
                    'description' => $request->description,
                    'area' => $request->area
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            return ($e);
        }
    }

    public function selectGeofence()
    {

        try {
            $client = new Client();
            $root = auth()->user()->company->email;
            $password = auth()->user()->company->root_pass_traccar;


            $url_traccar = env('URL_TRACCAR');

            $response =  $client->get($url_traccar . '/api/geofences', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth' => [$root, $password],
            ]);
            $responseData = json_decode($response->getBody(), true);

            return $responseData;
        } catch (\Exception $e) {
            return ($e);
        }
    }

    public function destroyGeofence($geofence)
    {
        try {
            $client = new Client();
            $root = auth()->user()->company->email;
            $password = auth()->user()->company->root_pass_traccar;

            $url_traccar = env('URL_TRACCAR');

            $client->delete($url_traccar . '/api/geofences/' . $geofence, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth' => [$root, $password],
            ]);
            return true;
        } catch (\Exception $e) {
            dd($e);
        }
    }
    
}
