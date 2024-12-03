<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TraccarHelper
{
    protected $client;
    protected $url;
    protected $root;
    protected $password;
    protected $traccarHelper;

    public function __construct()
    {
        $this->url = env('URL_TRACCAR');
        $this->client = new Client();
        $this->root = Auth::check() ? Auth::user()->company->email : null;
        $this->password = Auth::check() ? Auth::user()->company->root_pass_traccar : null;
    }

    public function getTokenTraccar($user)
    {
        $token = null;
        try {
            $response = Http::withBasicAuth($user->company->email, $user->company->root_pass_traccar)
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->timeout(1)
                ->post($this->url . '/api/session/token');
            $token = $response->getBody();
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $token;
    }

    public function getDevicesTraccar()
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->get($this->url . '/api/devices');
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function getLastRecordForEachDevice()
    {
        $result = null;
        try {
            $devices = $this->getDevicesTraccar();
            $dateFrom = Carbon::now()->subMinutes(60)->format('Y-m-d\TH:i:s\Z');
            $dateTo = Carbon::now()->format('Y-m-d\TH:i:s\Z');
            $dataPositions = [];

            foreach ($devices as $device) {
                $positions = [];
                $url = $this->url  . '/api/positions?deviceId=' . $device['id'] . '&from=' . $dateFrom . '&to=' . $dateTo;

                $response = Http::withBasicAuth($this->root, $this->password)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(30)
                    ->get($url);
                $result = $this->handleResponse($response);
                foreach ($result as $data) {
                    $positions[] = [
                        'attributes' => $data['attributes'],
                        'date' => $data['deviceTime'],
                        'lat' => $data['latitude'],
                        'lng' => $data['longitude'],
                        'speed' => round($data['speed'] * 1.852, 2),
                    ];
                }
                $dataPositions[$device['id']] = $positions;
            }
            $result = $dataPositions;
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }


    public function createUser($name, $email, $password)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->post($this->url . '/api/users', [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);

            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function updateUser($userId, $name, $email)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->put($this->url . '/api/users/' . $userId, [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                ]);
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }

        return $result;
    }

    public function assignDeviceToUser($userId, $deviceId)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->post($this->url . '/api/permissions', [
                    'userId' => $userId,
                    'deviceId' => $deviceId,
                ]);
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function createDevice($name, $uniqueId, $typeDevice, $engines)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->post($this->url . '/api/devices', [
                    'name' => $name,
                    'uniqueId' => $uniqueId,
                    'category' => $typeDevice,
                    'contact' => json_encode($engines),
                ]);
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function updateDevice($deviceId, $name, $uniqueId, $typeDevice, $engines)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->put($this->url . '/api/devices/' . $deviceId, [
                    'id' => $deviceId,
                    'name' => $name,
                    'uniqueId' => $uniqueId,
                    'category' => $typeDevice,
                    'contact' => json_encode($engines),
                ]);
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function destroyDevice($deviceId)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->delete($this->url . '/api/devices/' . $deviceId);

            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                $result = 'ok';
            };
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    public function getPositionsRange($device, $dateStart, $dateEnd)
    {
        $result = null;
        try {
            $response = Http::withBasicAuth($this->root, $this->password)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(1)
                ->get($this->url . '/api/positions?deviceId=' . $device . '&from=' . $dateStart . '&to=' . $dateEnd);
            $result = $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Traccar Error ', ['exception' => $e->getMessage()]);
        }
        return $result;
    }

    private function handleResponse($response)
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        }
        return null;
    }

    public function dateToUTC($date)
    {
        return Carbon::parse($date, Auth::user()->time_zone)->setTimezone('UTC');
    }

    public function dateToLocal($date)
    {
        return Carbon::parse($date, 'UTC')->setTimezone(Auth::user()->time_zone);
    }
}
