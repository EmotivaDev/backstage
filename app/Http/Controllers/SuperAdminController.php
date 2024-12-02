<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NewCompanyMail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TraccarHelper;

class SuperAdminController extends Controller
{
    protected $traccarHelper;
    protected $engines = [
        'E1' => 'Engine 1',
        'E2' => 'Engine 2',
        'E3' => 'Engine 3',
        'E4' => 'Engine 4',
        'E5' => 'Engine 5',
    ];
    protected $typeDevices = [
        'boat' => 'Boat',
        'car' => 'Car',
        'dredge' => 'Dredge',
    ];


    public function __construct()
    {
        if (Auth::user()->id !== 1) {
            return redirect('/');
        }

        $this->traccarHelper = new TraccarHelper();
    }

    public function index()
    {
        $tcDevices = $this->traccarHelper->getDevicesTraccar();
        $tcUsers = $this->getUsersWithDevices();
        return view('superadmin.index', compact('tcUsers', 'tcDevices'));
    }

    private function getUsersWithDevices()
    {
        return DB::table('companies')
            ->leftJoin('tc_user_device', 'companies.root_id_traccar', '=', 'tc_user_device.userid')
            ->leftJoin('tc_devices', 'tc_user_device.deviceid', '=', 'tc_devices.id')
            ->select('companies.root_id_traccar as id', 'companies.name', 'companies.email', DB::raw('GROUP_CONCAT(tc_devices.name SEPARATOR "|") as devices'))
            ->groupBy('companies.root_id_traccar', 'companies.name', 'companies.email')
            ->get()
            ->map(function ($user) {
                $user->devices = $user->devices !== null ? explode('|', $user->devices) : [];
                return $user;
            });
    }

    public function createCompany()
    {
        $permissions = Permission::all();
        $tcDevices = $this->traccarHelper->getDevicesTraccar();
        return view('superadmin.companies.create-company', compact('tcDevices', 'permissions'));
    }

    public function storeCompany(Request $request)
    {
        $verificationUrl  = env('APP_URL') . "/verify";
        $tempPassword = Str::random(10);

        $this->convertToLowercase($request);

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:tc_users,email',
            'password' => 'required|string|max:50',
            'devices' => 'array',
            'time_zone' => 'required|string|max:50',
            'permissions' => 'array',
        ]);

        $deviceIds = $request->devices ? array_map('intval', $request->devices) : [];
        $permissionsArray = $request->permissions ?? [];

        $createCompany = $this->traccarHelper->createUser($request->name, $request->email, $request->password);
        if ($createCompany != null) {
            $companyId = $createCompany['id'];

            foreach ($deviceIds as $key => $deviceId) {
                $this->traccarHelper->assignDeviceToUser($createCompany['id'], $deviceId);
            }

            $this->createRealtimeLogForCompany($companyId);

            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'root_id_traccar' => $companyId,
                'root_pass_traccar' => $request->password,
                'device_ids' => $deviceIds,
                'permissions' => $permissionsArray
            ]);

            $administration_role = Role::create(['name' => $company->id . '_administrator', 'company_id' => $company->id]);

            $administration_role->givePermissionTo($permissionsArray);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($tempPassword),
                'time_zone' => $request->time_zone,
                'company_id' => $company->id,
                'device_ids' => $deviceIds
            ]);
            $user->assignRole($company->id . '_administrator');

            Mail::to($user->email)->send(new NewCompanyMail($verificationUrl, $tempPassword));

            return redirect()->route('super-admin')->with('success', __('messages.create', ['name' => __('labels.texts.company')]));
        } else {
            return redirect()->route('super-admin')->with('error', __('messages.not_create', ['name' => __('labels.texts.company')]));
        }
    }

    private function createRealtimeLogForCompany($companyId)
    {
        $tableNameRealTimeLog = "realtimelog_" . $companyId;
        $tableNameReverseGeocodes = "reverse_geocodes_" . $companyId;
        if (!DB::getSchemaBuilder()->hasTable($tableNameRealTimeLog)) {

            DB::statement("CREATE TABLE $tableNameRealTimeLog (
            `id` int NOT NULL,
            `deviceid` int(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `servertime` timestamp NULL DEFAULT NULL,
            `devicetime` timestamp NULL DEFAULT NULL,
            `fixtime` timestamp NULL DEFAULT NULL,
            `speed` double DEFAULT NULL,
            `cog` double DEFAULT NULL,
            `latitude` double NOT NULL,
            `longitude` double NOT NULL,
            `altitude` float NOT NULL,
            `geofenceids` varchar(128) DEFAULT NULL,
            `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `gpdata` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
            `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `hour` int(11) DEFAULT NULL,
            `event` varchar(45) DEFAULT NULL,
            `trip` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `attributes` JSON DEFAULT NULL,
            `telemetry` JSON DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL
            )");

            DB::statement("CREATE TABLE  $tableNameReverseGeocodes (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `milemark` double NOT NULL,
                `latitude` double NOT NULL,
                `longitude` double NOT NULL,
                `maxspeed` double DEFAULT NULL,
                `maxrpm` double DEFAULT NULL,
                `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            );");

            $fileCsv = storage_path('app/private/reverse_geocode.csv');

            $fileCsv = fopen($fileCsv, 'r');

            if ($fileCsv !== false) {
                while (($fila = fgetcsv($fileCsv)) !== false) {
                    list(
                        $location,
                        $milemark,
                        $latitude,
                        $longitude,
                        $maxspeed,
                        $maxrpm,
                        $description,
                    ) = $fila;
                    $maxspeed = $maxspeed !== 'NULL' ? $maxspeed : null;
                    $maxrpm = $maxrpm !== 'NULL' ? $maxrpm : null;
                    $description = $description !== 'NULL' ? $description : '';
                    DB::table($tableNameReverseGeocodes)->insert(
                        [
                            'location' => mb_strtolower($location, 'UTF-8'),
                            'milemark' => $milemark,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'maxspeed' => null,
                            'maxrpm' => null,
                            'description' => null,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]
                    );
                }
                fclose($fileCsv);
            } else {
                echo "Error";
            }
        }
    }

    public function editCompany($company)
    {
        $permissions = Permission::all();
        $companyData = $this->getCompanyData($company);
        $tcDevices = $this->traccarHelper->getDevicesTraccar();
        return view('superadmin.companies.edit-company', compact('companyData', 'tcDevices', 'permissions'));
    }

    private function getCompanyData($companyId)
    {
        $users = DB::table('tc_users')
            ->leftJoin('tc_user_device', 'tc_users.id', '=', 'tc_user_device.userid')
            ->leftJoin('tc_devices', 'tc_user_device.deviceid', '=', 'tc_devices.id')
            ->leftJoin('companies', 'companies.root_id_traccar', '=', 'tc_users.id')
            ->select('tc_users.id', 'tc_users.name', 'tc_users.email', 'companies.permissions', DB::raw('JSON_ARRAYAGG(tc_devices.id) as devices'))
            ->groupBy('tc_users.id', 'tc_users.name', 'tc_users.email', 'companies.permissions')
            ->where('tc_users.id', $companyId)
            ->get();

        foreach ($users as $user) {
            $user->devices = json_decode($user->devices);
            $user->permissions = json_decode($user->permissions);
        }

        return $users[0];
    }

    public function updateCompany(Request $request, $companyId)
    {
        $this->convertToLowercase($request);

        $request->validate([
            'name' => 'required|string|max:50|unique:tc_users,name,' . $companyId,
            'email' => 'required|string|unique:tc_users,email,' . $companyId,
            'devices' => 'array',
            'time_zone' => 'required|string|max:50',
            'permissions' => 'array',
        ]);

        $deviceIds = $request->devices ? array_map('intval', $request->devices) : [];
        $permissionsArray = $request->permissions ?? [];


        $CompanyEmotivaId =  DB::table('companies')->where('root_id_traccar', $companyId)->value('id');
        $role = Role::where('name', $CompanyEmotivaId . '_administrator')->where('company_id',  $CompanyEmotivaId)->first();

        $role->syncPermissions($permissionsArray);

        $companyTraccar = DB::table('companies')->where('root_id_traccar', $companyId)->get();
        $emailInUser = DB::table('tc_users')->where('id', $companyId)->value('email');

        $deviceIdsDelete = array_diff(is_array($deviceIdsFromDb = json_decode($companyTraccar[0]->device_ids, true)) ? $deviceIdsFromDb : [], $deviceIds);

        $updateCompany = $this->traccarHelper->updateUser($companyId, $request->name, $request->email);

        if ($updateCompany != null) {
            DB::table('tc_user_device')->where('userid', $companyId)->delete();
            foreach ($deviceIdsDelete as $key => $deviceIdDelete) {

                $users = DB::table('users')
                    ->where('company_id', $companyTraccar[0]->id)
                    ->whereJsonContains('device_ids', $deviceIdDelete)
                    ->get();

                foreach ($users as $user) {
                    $deviceIdsResponse = json_decode($user->device_ids, true);

                    $deviceIdsResponse = array_filter($deviceIdsResponse, function ($id) use ($deviceIdDelete) {
                        return $id != $deviceIdDelete;
                    });

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['device_ids' => json_encode(array_values($deviceIdsResponse))]);
                }
            }

            foreach ($deviceIds as $key => $deviceId) {
                $this->traccarHelper->assignDeviceToUser($companyId, $deviceId);
            }

            DB::table('companies')->where('email', $emailInUser)->where('root_id_traccar', $companyId)
                ->update(
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'device_ids' => $deviceIds,
                        'permissions' => $permissionsArray
                    ]
                );

            DB::table('users')->where('email', $emailInUser)->where('company_id', $companyTraccar[0]->id)
                ->update(
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'device_ids' => $deviceIds,
                        'time_zone' => $request->time_zone
                    ]
                );
            return redirect()->route('super-admin')->with('success', __('messages.update', ['name' => __('labels.texts.company')]));
        } else {
            return redirect()->route('super-admin')->with('error', __('messages.not_update', ['name' => __('labels.texts.company')]));
        }
    }

    public function createDevice()
    {
        $typeDevices = $this->typeDevices;
        $engines = $this->engines;
        return view('superadmin.devices.create-device', compact('typeDevices', 'engines'));
    }

    public function storeDevice(Request $request)
    {
        $this->convertToLowercase($request);

        $request->validate([
            'name' => 'required|string|max:50',
            'unique_id' => 'required|string|unique:tc_devices,uniqueid',
            'type_device' => 'required|string|max:50',
            'engines' => 'array',
        ]);

        $engines = $request->engines ?? [];

        $storeDevice = $this->traccarHelper->createDevice($request->name, $request->unique_id, $request->type_device, $engines);

        if ($storeDevice != null) {
            return redirect()->route('super-admin')->with('success', __('messages.create', ['name' => __('labels.texts.device')]));
        } else {
            return redirect()->route('super-admin')->with('error', __('messages.not_create', ['name' => __('labels.texts.device')]));
        }
    }

    public function editDevice($device)
    {
        $tcDevices = DB::table('tc_devices')->where('id', $device)->first();
        $typeDevices = $this->typeDevices;
        $engines = $this->engines;
        return view('superadmin.devices.edit-device', compact('tcDevices', 'typeDevices', 'engines'));
    }

    public function updateDevice(Request $request, $device)
    {
        $this->convertToLowercase($request);

        $request->validate([
            'name' => 'required|string|max:50',
            'unique_id' => 'required|string|unique:tc_devices,uniqueid,' . $device,
            'type_device' => 'required|string|max:50',
            'engines' => 'array',
        ]);

        $engines = $request->engines ?? [];

        $updateDevice = $this->traccarHelper->updateDevice($device, $request->name, $request->unique_id, $request->type_device, $engines);

        if ($updateDevice != null) {
            return redirect()->route('super-admin')->with('success', __('messages.update', ['name' => __('labels.texts.device')]));
        } else {
            return redirect()->route('super-admin')->with('error', __('messages.not_update', ['name' => __('labels.texts.device')]));
        }
    }

    public function destroyDevice($device)
    {
        $destroyDevice = $this->traccarHelper->destroyDevice($device);
        if ($destroyDevice != null) {
            $users = User::get();
            foreach ($users as $user) {
                $device_ids = $user->device_ids;

                if ($device_ids != null && ($key = array_search($device, $device_ids)) !== false) {
                    unset($device_ids[$key]);
                    $device_ids = array_values($device_ids);
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['device_ids' => json_encode($device_ids)]);
                }
            }

            $companies = Company::get();
            foreach ($companies as $company) {
                $device_ids = $company->device_ids;

                if ($device_ids != null && ($key = array_search($device, $device_ids)) !== false) {
                    unset($device_ids[$key]);
                    $device_ids = array_values($device_ids);

                    DB::table('companies')
                        ->where('id', $company->id)
                        ->update(['device_ids' => json_encode($device_ids)]);
                }
            }

            DB::table('trips')->where('deviceid', $device)->delete();

            return redirect()->route('super-admin')->with('success', __('messages.destroy', ['name' => __('labels.texts.device')]));
        } else {
            return redirect()->route('super-admin')->with('error', __('messages.not_destroy', ['name' => __('labels.texts.device')]));
        }
    }
}
