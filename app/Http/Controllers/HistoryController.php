<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TraccarHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    protected $traccarHelper;

    public function __construct()
    {
        $this->traccarHelper = new TraccarHelper();
    }

    public function index()
    {
        $tcDevices = $this->traccarHelper->getDevicesTraccar() ?? [];
        return view('general.history', compact('tcDevices'));
    }

    function historyDevice(Request $request)
    {
        $device = $request->device;
        $dateStart = $this->traccarHelper->dateToUTC($request->date_start);
        $dateEnd = $this->traccarHelper->dateToUTC($request->date_end);

        $nameDevice = DB::table('tc_devices')->where('id', $device)->value('name');

        $history = [];
        if (Auth::user()->id == 1) {
            $response = $this->traccarHelper->getPositionsRange($device, $dateStart->format('Y-m-d\TH:i:s\Z'), $dateEnd->format('Y-m-d\TH:i:s\Z'));
            if (is_array($response) && count($response) > 1) {
                $history = $this->writeInFiles(collect($response), $nameDevice);
            }
        }
        return $history;
    }

    private function writeInFiles($response, $nameDevice)
    {
        $avgSpeed = 0;
        $avgCog = 0;
        $stringArr = "";
        $minDate = null;
        $maxDate = null;
        $totalRpm1 = 0;
        $totalRpm2 = 0;
        $totalRpm3 = 0;
        $totalFuel1 = 0;
        $totalFuel2 = 0;
        $totalFuel3 = 0;
        $minEct1 = 0;
        $minEct2 = 0;
        $minEct3 = 0;
        $minEop1 = 0;
        $minEop2 = 0;
        $minEop3 = 0;
        $maxEct1 = 0;
        $maxEct2 = 0;
        $maxEct3 = 0;
        $maxEop1 = 0;
        $maxEop2 = 0;
        $maxEop3 = 0;
        $count = 0;

        $dateNow = $this->traccarHelper->dateToLocal(Carbon::now())->format('Y-m-d-H-i-s');

        $historyStart = $response->first();
        $historyEnd = $response->last();

        $rGeocodeStart = $this->getReverseGeocode($historyStart['latitude'], $historyStart['longitude']);
        $rGeocodeEnd = $this->getReverseGeocode($historyEnd['latitude'], $historyEnd['longitude']);

        $nameFile = Auth::user()->id . "-" . strtoupper($nameDevice) . "-" .
            strtoupper($rGeocodeStart->location) . "-" . strtoupper($rGeocodeStart->milemark) . "-"
            . strtoupper($rGeocodeEnd->location) . "-" . strtoupper($rGeocodeEnd->milemark) . "-" . $dateNow;

        $fileNameCsv =  $nameFile . '.csv';
        $fileCsv = fopen(storage_path('app/private/files_history/csv/' . $fileNameCsv), 'w');
        fwrite($fileCsv, "ID;NAME;EVENT;DATE;SPEED;COURSE;LATITUDE;LONGITUDE;LOCATION;ODOMETER;DEPTH;DEPTH;
        RPM1;RPM2;RPM3;ECT1;ECT2;ECT3;EOT1;EOT2;EOT3;EFP1;EFP2;EFP3;EOP1;EOP2;EOP3;TOP1;TOP2;TOP3;TOT1;TOT2;TOT3
        ;VOL1;VOL2;VOL3;FUELUSED1;FUELUSED2;FUELUSED3;HOURS1,HOURS2;HOURS3" . chr(13));

        $fileNameKml = $nameFile . '.kml';
        $fileKml = fopen(storage_path('app/private/files_history/kml/' . $fileNameKml), 'w');
        fwrite($fileKml, "<?xml version='1.0' standalone='yes'?>" . "\n");
        fwrite($fileKml, "<kml creator='Killer3dfx' xmlns='http://earth.google.com/kml/2.0'>" . "\n");
        fwrite($fileKml,  "<Document>" . "\n");
        fwrite($fileKml,  "<name><![CDATA[rutakml]]></name>" . "\n");
        fwrite($fileKml,  "<Folder>" . "\n");
        fwrite($fileKml,  "<name>Tracks</name>" . "\n");
        fwrite($fileKml,   "<Placemark>" . "\n");
        fwrite($fileKml,  "<name><![CDATA[REALTIME_LOG]]></name>" . "\n");
        fwrite($fileKml,  "<MultiGeometry>" . "\n");
        fwrite($fileKml,  "<LineString>" . "\n");
        fwrite($fileKml, "<altitudeMode>clampedToGround</altitudeMode>" . "\n");
        fwrite($fileKml,  "<coordinates>" . "\n");

        $domGpx = new DOMDocument('1.0', 'UTF-8');
        $domGpx->preserveWhiteSpace = false;
        $domGpx->formatOutput = true;
        $dNodeGpx = $domGpx->createElement('gpx');
        $dNodeGpx = $domGpx->appendChild($dNodeGpx);
        $dNodeGpx->setAttribute('version', "1.2");
        $dNodeGpx->setAttribute('creator', "OpenCPN");
        $dNodeGpx->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $dNodeGpx->setAttribute('xmlns', "http://www.topografix.com/GPX/1/1");
        $dNodeGpx->setAttribute('xmlns:gpxx', "http://www.garmin.com/xmlschemas/GpxExtensions/v3");
        $dNodeGpx->setAttribute('xsi:schemaLocation', "http://www.garmin.com/xmlschemas/GpxExtensions/v3");
        $dNodeGpx->setAttribute('xmlns:gpxx', "http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd");
        $dNodeGpx->setAttribute('xmlns:opencpn', "http://www.opencpn.org");
        $dataNodeGpx = $domGpx->createElement('trk');
        $dNodeGpx->appendChild($dataNodeGpx);
        $linkNode = $domGpx->createElement('link');
        $dataNodeGpx->appendChild($linkNode);
        $linkNode->setAttribute('href', "https://www.rivertech.com.co");
        $textNode = $domGpx->createElement('text', "Rivertech");
        $linkNode->appendChild($textNode);
        $extensionsNode = $domGpx->createElement('extensions');
        $dataNodeGpx->appendChild($extensionsNode);
        $guid = $domGpx->createElement('opencpn:guid');
        $extensionsNode->appendChild($guid);
        $viz = $domGpx->createElement('opencpn:viz', "1");
        $extensionsNode->appendChild($viz);
        $openCpnStyle = $domGpx->createElement('opencpn:style');
        $extensionsNode->appendChild($openCpnStyle);
        $openCpnStyle->setAttribute('width', "3");
        $openCpnStyle->setAttribute('style', "100");
        $trackExtension = $domGpx->createElement('gpxx:TrackExtension');
        $extensionsNode->appendChild($trackExtension);
        $DisplayColor = $domGpx->createElement('gpxx:DisplayColor', "Red");
        $trackExtension->appendChild($DisplayColor);
        $trkSeg = $domGpx->createElement('trkseg');
        $dataNodeGpx->appendChild($trkSeg);


        foreach ($response as $valueResponse) {
            $rGeocode = $this->getReverseGeocode($valueResponse['latitude'], $valueResponse['longitude']);
            $date = $this->traccarHelper->dateToLocal($valueResponse['deviceTime']);

            $attributes = $valueResponse['attributes'];

            $event = $attributes['event'] ?? '';
            $odometer = $attributes['odometer'] ?? 0;
            $depth = $attributes['depth'] ?? 0;

            $result = "";
            // $result = "$rpm1;$rpm2;$rpm3;";
            // $result .= "$ect1;$ect2;$ect3;";
            // $result .= "$eot1;$eot2;$eot3;";
            // $result .= "$efp1;$efp2;$efp3;";
            // $result .= "$eop1;$eop2;$eop3;";
            // $result .= "$top1;$top2;$top3;";
            // $result .= "$tot1;$tot2;$tot3;";
            // $result .= "$vol1;$vol2;$vol3;";
            // $result .= "$fuelused1;$fuelused2;$fuelused3;";
            // $result .= "$hours1;$hours2;$hours3";

            fwrite($fileCsv, "" . $valueResponse['deviceId'] .
                ";" . strtoupper($nameDevice)  .
                ";" . $event  .
                ";" . $date->toDateTimeString() .
                ";" . $valueResponse['speed'] .
                ";" . $valueResponse['course'] .
                ";" . $valueResponse['latitude'] .
                ";" . $valueResponse['longitude'] .
                ";" . $rGeocode->location . "-" . $rGeocode->milemark .
                ";" . $odometer .
                ";" . $depth .
                ";" . $result . chr(13));

            fwrite($fileKml, $valueResponse['longitude'] . ',' . $valueResponse['latitude'] . ',0'  . "\n");

            $avgSpeed += $valueResponse['speed'] * 1.852;
            $avgCog += $valueResponse['course'] * 1.852;

            if ($minDate === null || $date < $minDate) {
                $minDate = $date;
            }
            if ($maxDate === null || $date > $maxDate) {
                $maxDate = $date;
            }

            if (!empty($valueResponse['attributes']['emi'])) {
                $emi = json_decode($valueResponse['attributes']['emi'], true);

                $totalRpm1 += $emi['rpm1'];
                $totalRpm2 += $emi['rpm2'];
                $totalRpm3 += $emi['rpm3'];

                $totalFuel1 += $emi['tfuel1'];
                $totalFuel2 += $emi['tfuel2'];
                $totalFuel3 += $emi['tfuel3'];

                $minEct1 = min($minEct1, $emi['ect1']);
                $minEct2 = min($minEct2, $emi['ect2']);
                $minEct3 = min($minEct3, $emi['ect3']);
                $minEop1 = min($minEop1, $emi['eop1']);
                $minEop2 = min($minEop2, $emi['eop2']);
                $minEop3 = min($minEop3, $emi['eop3']);

                $maxEct1 = max($maxEct1, $emi['ect1']);
                $maxEct2 = max($maxEct2, $emi['ect2']);
                $maxEct3 = max($maxEct3, $emi['ect3']);
                $maxEop1 = max($maxEop1, $emi['eop1']);
                $maxEop2 = max($maxEop2, $emi['eop2']);
                $maxEop3 = max($maxEop3, $emi['eop3']);

                $count++;
            } else {
                $emi = [];
            }

            // NOB
            $lat = $valueResponse['latitude'] < 0 ? abs($valueResponse['latitude']) . ' S' : $valueResponse['latitude'] . ' N';
            $lon = $valueResponse['longitude'] < 0 ? abs($valueResponse['longitude']) . ' W' : $valueResponse['longitude'] . ' E';

            $fe = $date->format('Ymd') . 'T' . $date->format('His') . 'D';
            $feGpx = $date->format('Y-m-d') . 'T' . $date->format('H:i:s') . 'Z';
            $signs = array("+", "-");
            $stringArr .= $fe . " " . str_replace($signs, "", $lat) . " " . str_replace($signs, "", $lon) . ";\n";
            //End NOB

            // Gpx
            $trKpt = $domGpx->createElement('trkpt');
            $trkSeg->appendChild($trKpt);
            $trKpt->setAttribute('lat', $valueResponse['latitude']);
            $trKpt->setAttribute('lon', $valueResponse['longitude']);
            $trkTime = $domGpx->createElement('time', $feGpx);
            $trKpt->appendChild($trkTime);
            //End Gpx

            $history[] = array(
                'deviceid' => $valueResponse['deviceId'],
                'name' => $nameDevice,
                'devicetime' => $date->toDateTimeString(),
                'course' => $valueResponse['course'],
                'longitude' => $valueResponse['longitude'],
                'latitude' => $valueResponse['latitude'],
                'altitude' => $valueResponse['altitude'],
                'speed' => $valueResponse['speed'],
                'trip' => $valueResponse['trip'],
                'attributes' => $attributes,
                'emi' =>  $emi,
                'odometer' => $odometer,
                'location' => $rGeocode->location . "-" . $rGeocode->milemark,
            );
        }

        $travelDistance = (($historyEnd['attributes']['odometer'] ?? 0) - ($historyStart['attributes']['odometer'] ?? 0)) / 1000;
        $avgSpeed = round($avgSpeed /  $response->count(), 2);
        $totalTime = gmdate("H:i:s", strtotime($maxDate) - strtotime($minDate));
        $dateStart = $this->traccarHelper->dateToLocal($historyStart['deviceTime']);
        $dateEnd = $this->traccarHelper->dateToLocal($historyEnd['deviceTime']);
        $dateDifference = $this->get_format(($dateStart)->diff($dateEnd));

        //NOB
        $dom = new DOMDocument('1.0', 'UTF-8');

        $dnode = $dom->createElement('NavObjectCollection');
        $docNode = $dom->appendChild($dnode);

        $data_node = $dom->createElement('Track');
        $docNode->appendChild($data_node);

        $data_node->setAttribute('created', $date->format('Ymd') . 'T' . $date->format('His') . 'D');
        $data_node->setAttribute('id', "{a5e36e9c-4878-4b44-a35d-ffa679db7e31}");
        $TrackingByDistanceInterval = $dom->createElement('TrackingByDistanceInterval', "false");
        $data_node->appendChild($TrackingByDistanceInterval);

        $TrackingDistanceInterval = $dom->createElement('TrackingDistanceInterval', "0.1 NM");
        $data_node->appendChild($TrackingDistanceInterval);

        $TotalLength = $dom->createElement('TotalLength', $travelDistance . " km");
        $data_node->appendChild($TotalLength);

        $TotalTime = $dom->createElement('TotalTime', $dateDifference);
        $data_node->appendChild($TotalTime);

        $AverageSpeed = $dom->createElement('AverageSpeed', $avgSpeed . " km/h");
        $data_node->appendChild($AverageSpeed);

        $StartTime = $dom->createElement('StartTime', $date->format('Ymd') . 'T' . $date->format('His') . 'D');
        $data_node->appendChild($StartTime);

        $Name = $dom->createElement('Name', strtoupper($nameDevice) . "-" . strtoupper($rGeocodeStart->location) . "-" . strtoupper($rGeocodeStart->milemark)
            . "-" . strtoupper($rGeocodeEnd->location) . "-" . strtoupper($rGeocodeEnd->milemark));
        $data_node->appendChild($Name);

        $TrackMarks = $dom->createElement('TrackMarks', $stringArr);
        $data_node->appendChild($TrackMarks);

        $fileNameNob = $nameFile . '.nob';
        $dom->save(storage_path('app/private/files_history/nob/') . $fileNameNob);
        /* End NOB*/

        /* Gpx*/
        $start = $domGpx->createElement('opencpn:start', strtoupper($rGeocodeStart->location) . "-" . strtoupper($rGeocodeStart->milemark));
        $extensionsNode->appendChild($start);
        $end = $domGpx->createElement('opencpn:end', strtoupper($rGeocodeEnd->location) . "-" . strtoupper($rGeocodeEnd->milemark));
        $extensionsNode->appendChild($end);

        $track_node = $domGpx->createElement('name', strtoupper($nameDevice) . "-" . strtoupper($rGeocodeStart->location) . "-" . strtoupper($rGeocodeStart->milemark)
            . "-" . strtoupper($rGeocodeEnd->location) . "-" . strtoupper($rGeocodeEnd->milemark));
        $dataNodeGpx->appendChild($track_node);
        $desc_node = $domGpx->createElement('desc', "Average Speed: " . $avgSpeed . " km/h\nTotal Time: " . $dateDifference . "\nDistancia Recorrida: " . $travelDistance . " KM\n");
        $dataNodeGpx->appendChild($desc_node);
        $fileNameGpx = $nameFile . '.gpx';
        $domGpx->save(storage_path('app/private/files_history/gpx/') . $fileNameGpx);
        /* End Gpx*/

        fwrite($fileKml, "</coordinates>" . "\n");
        fwrite($fileKml, "<tessellate>1</tessellate>" . "\n");
        fwrite($fileKml, "</LineString>" . "\n");
        fwrite($fileKml, "</MultiGeometry>" . "\n");
        fwrite($fileKml,  "<Snippet></Snippet>" . "\n");
        fwrite($fileKml,  "<Style>" . "\n");
        fwrite($fileKml,  "<LineStyle>" . "\n");
        fwrite($fileKml,  "<color>ff0000ff</color>" . "\n");
        fwrite($fileKml,  "<width>4</width>" . "\n");
        fwrite($fileKml,  "</LineStyle>" . "\n");
        fwrite($fileKml,  "</Style>" . "\n");
        fwrite($fileKml,  "<description>&amp;nbsp;</description>" . "\n");
        fwrite($fileKml,  "</Placemark>" . "\n");
        fwrite($fileKml,  "<open>0</open>" . "\n");
        fwrite($fileKml, "<visibility>1</visibility>" . "\n");
        fwrite($fileKml, "</Folder>" . "\n");
        fwrite($fileKml, "<Snippet><![CDATA[created at <A href='http://www.emotiva.io/'>River Tech</A>]]></Snippet>" . "\n");
        fwrite($fileKml, "<open>1</open>" . "\n");
        fwrite($fileKml, "</Document>" . "\n");
        fwrite($fileKml, "</kml>" . "\n");
        fclose($fileKml);

        if ($count > 0) {
            $avgRpm1 = $totalRpm1 / $count;
            $avgRpm2 = $totalRpm2 / $count;
            $avgRpm3 = $totalRpm3 / $count;

            $avgFuelUsed1 = $totalFuel1 / $count;
            $avgFuelUsed2 = $totalFuel2 / $count;
            $avgFuelUsed3 = $totalFuel3 / $count;
        } else {
            $avgRpm1 = 0;
            $avgRpm2 = 0;
            $avgRpm3 = 0;

            $avgFuelUsed1 = 0;
            $avgFuelUsed2 = 0;
            $avgFuelUsed3 = 0;
        }

        $resume = array(
            'location' => strtoupper($rGeocodeStart->location) . "-" . strtoupper($rGeocodeStart->milemark) . '/' .
                strtoupper($rGeocodeEnd->location) . "-" . strtoupper($rGeocodeEnd->milemark),
            'travelDistance' => $travelDistance,
            'name' => $nameDevice,
            'totalTime' => $totalTime,
            'avgCog' => round($avgCog, 1),
            'avgSpeed' => $avgSpeed,
            'avgEngineRpm' =>  array('rpm1' => round($avgRpm1, 1), 'rpm2' => round($avgRpm2, 1), 'rpm3' => round($avgRpm3, 1)),
            'minMaxEngineEct' => array(
                'ect1Min' => round($minEct1, 1),
                'ect1Max' => round($maxEct1, 1),
                'ect2Min' => round($minEct2, 1),
                'ect2Max' => round($maxEct2, 1),
                'ect3Min' => round($minEct3, 1),
                'ect3Max' => round($maxEct3, 1)
            ),
            'minMaxEngineEop' => array(
                'eop1Min' => round($minEop1, 1),
                'eop1Max' => round($maxEop1, 1),
                'eop2Min' => round($minEop2, 1),
                'eop2Max' => round($maxEop2, 1),
                'eop3Min' => round($minEop3, 1),
                'eop3Max' => round($maxEop3, 1)
            ),
            'avgEngineFuelUsed' =>  array('avgFuelUsed1' => round($avgFuelUsed1, 1), 'avgFuelUsed2' => round($avgFuelUsed2, 1), 'avgFuelUsed3' => round($avgFuelUsed3, 1)),
        );

        $responseData =  array(
            'files' => array(
                'kmlTrack' => $fileNameKml,
                'excelCsv' => $fileNameCsv,
                'rosePointTrack' => $fileNameNob,
                'openCpnTrack' => $fileNameGpx
            ),
            'resume' => $resume,
            'data' => $history
        );

        return $responseData;
    }

    function get_format($df)
    {
        $str = '';
        $str .= ($df->invert == 1) ? ' - ' : '';
        if ($df->d > 0) {
            // days
            $str .= ($df->d > 1) ? $df->d . ' Days ' : $df->d . ' Day ';
        }
        if ($df->h > 0) {
            // hours
            $str .= ($df->h > 1) ? $df->h . ' Hours ' : $df->h . ' Hour ';
        }
        if ($df->i > 0) {
            // minutes
            $str .= ($df->i > 1) ? $df->i . ' Minutes ' : $df->i . ' Minute ';
        }
        if ($df->s > 0) {
            // seconds
            $str .= ($df->s > 1) ? $df->s . ' Seconds ' : $df->s . ' Second ';
        }
        return $str;
        //echo $str;
    }

    public function getDownloadFile($url)
    {
        $folder = '';

        if (Str::endsWith($url, '.csv')) {
            $folder =  "app/private/files_history/csv/";
        } elseif (Str::endsWith($url, '.kml')) {
            $folder = "app/private/files_history/kml/";
        } elseif (Str::endsWith($url, '.nob')) {
            $folder =  "app/private/files_history/nob/";
        } elseif (Str::endsWith($url, '.gpx')) {
            $folder =  "app/private/files_history/gpx/";
        }
        $path = storage_path($folder . $url);

        if (!file_exists($path)) {
            abort(404, "File not found");
        }

        return response()->download($path);
    }
}
