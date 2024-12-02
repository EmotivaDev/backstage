<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use GuzzleHttp\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Seed the application's database.
         */

        // Company::create([
        //     'name' => 'emotiva',
        //     'email' => 'samir.lissa@emotiva.com.co',
        //     'root_id_traccar' => '1',
        //     'root_pass_traccar' => 'lenovo$1',
        //     'device_ids' => null,
        //     'permissions' => null,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        // Permission::create(['name' => 'read my profile']);
        // Permission::create(['name' => 'read dashboard']);
        // Permission::create(['name' => 'read ais realtime']);
        // Permission::create(['name' => 'read ais historic']);
        // Permission::create(['name' => 'read realtime']);
        // Permission::create(['name' => 'create trip']);
        // Permission::create(['name' => 'update trip']);
        // Permission::create(['name' => 'finish trip']);
        // Permission::create(['name' => 'read historic']);
        // Permission::create(['name' => 'read roles']);
        // Permission::create(['name' => 'create roles']);
        // Permission::create(['name' => 'update roles']);
        // Permission::create(['name' => 'delete roles']);
        // Permission::create(['name' => 'read users']);
        // Permission::create(['name' => 'create users']);
        // Permission::create(['name' => 'update users']);
        // Permission::create(['name' => 'delete users']);
        // Permission::create(['name' => 'restore users']);
        // Permission::create(['name' => 'read devices']);

        // $superAdmin = Role::create(['name' => 'super administrator', 'company_id' => 1]);

        // $superAdmin->givePermissionTo([
        //     'read my profile',
        //     'read dashboard',
        //     'read ais realtime',
        //     'read ais historic',
        //     'read realtime',
        //     'read historic',
        // ]);

        // // $file_csv = 'reverse_geocode.csv';
        // $file_csv = storage_path('app/private/reverse_geocode.csv');

        // $file_csv = fopen($file_csv, 'r');

        // if ($file_csv !== false) {
        //     while (($fila = fgetcsv($file_csv)) !== false) {
        //         list(
        //             $location,
        //             $milemark,
        //             $latitude,
        //             $longitude,
        //             $maxspeed,
        //             $maxrpm,
        //             $description,
        //         ) = $fila;
        //         $maxspeed = $maxspeed !== 'NULL' ? $maxspeed : null;
        //         $maxrpm = $maxrpm !== 'NULL' ? $maxrpm : null;
        //         $description = $description !== 'NULL' ? $description : '';
        //         DB::table('reverse_geocodes')->insert(
        //             [
        //                 'location' => mb_strtolower($location, 'UTF-8'),
        //                 'milemark' => $milemark,
        //                 'latitude' => $latitude,
        //                 'longitude' => $longitude,
        //                 'maxspeed' => $maxspeed,
        //                 'maxrpm' => $maxrpm,
        //                 'description' => $description,
        //                 'created_at' => Carbon::now(),
        //                 'updated_at' => Carbon::now()
        //             ]
        //         );
        //     }
        //     fclose($file_csv);
        // } else {
        //     echo "Error";
        // }

        // $url_traccar = env('URL_TRACCAR');

        // $ids = DB::table('tc_devices')->pluck('id');

        // $tc_users = DB::table('tc_users')->first();
        // $trips = DB::table('trips')->first();

        // if (!$tc_users) {
        //     $client = new Client();
        //     $client->post($url_traccar . '/api/users', [
        //         'headers' => [
        //             'Content-Type' => 'application/json',
        //         ],
        //         'json' => [
        //             'name' => 'administrator',
        //             'email' => 'samir.lissa@emotiva.com.co',
        //             'password' => 'lenovo$1'
        //         ],
        //     ]);
        // }

        // if ($ids->isNotEmpty() && is_null($trips)) {
        //     foreach ($ids as $id) {
        //         DB::table('trips')->insert(['deviceid' => $id]);
        //     }
        // } else {
        //     $records = [
        //         ['id' => 1, 'name' => 'zambrano', 'category' => 'boat', 'unique_id' => '860896050858265'],
        //         ['id' => 2, 'name' => 'barranquilla', 'category' => 'boat', 'unique_id' => '860896050944651'],
        //         ['id' => 3, 'name' => 'mompox', 'category' => 'boat', 'unique_id' => '860896050858034'],
        //         ['id' => 4, 'name' => 'soledad', 'category' => 'boat', 'unique_id' => '860896050857945'],
        //         ['id' => 5, 'name' => 'barrancabermeja', 'category' => 'boat', 'unique_id' => '860896051154896'],
        //         ['id' => 6, 'name' => 'cantagallo', 'category' => 'boat', 'unique_id' => '860896050947324'],
        //         ['id' => 7, 'name' => 'gamarra', 'category' => 'boat', 'unique_id' => '860896050857911'],
        //         ['id' => 8, 'name' => 'capulco', 'category' => 'boat', 'unique_id' => '860896051126738'],
        //         ['id' => 9, 'name' => 'cavalier', 'category' => 'boat', 'unique_id' => '860896052059656'],
        //         ['id' => 10, 'name' => 'puerto_berrio', 'category' => 'boat', 'unique_id' => '860896052850815'],
        //         ['id' => 11, 'name' => 'la_gloria', 'category' => 'boat', 'unique_id' => '860896052808102'],
        //         ['id' => 12, 'name' => 'banco', 'category' => 'boat', 'unique_id' => '860896052850989'],
        //         ['id' => 13, 'name' => 'calamar', 'category' => 'boat', 'unique_id' => '860896051987956'],
        //         ['id' => 14, 'name' => 'rio blanco', 'category' => 'boat', 'unique_id' => '860896052808060'],
        //         ['id' => 15, 'name' => 'magangue', 'category' => 'boat', 'unique_id' => '860896052808045'],
        //         ['id' => 16, 'name' => 'san_pablo', 'category' => 'boat', 'unique_id' => '860896050858018'],
        //         ['id' => 17, 'name' => 'pto_triunfo', 'category' => 'boat', 'unique_id' => '860896052808078'],
        //         ['id' => 18, 'name' => 'draga 1', 'category' => 'dredge', 'unique_id' => '865413054847180'],
        //         ['id' => 19, 'name' => 'draga 2', 'category' => 'dredge', 'unique_id' => '865413055110760'],
        //         ['id' => 20, 'name' => 'dorada', 'category' => 'boat', 'unique_id' => '863719062450342']
        //     ];

        //     foreach ($records as $record) {
        //         $client = new Client();
        //         $root = 'samir.lissa@emotiva.com.co';
        //         $password = 'lenovo$1';
        //         $client->post($url_traccar . '/api/devices', [
        //             'headers' => [
        //                 'Content-Type' => 'application/json',
        //             ],
        //             'auth' => [$root, $password],
        //             'json' => [
        //                 'id' => $record['id'],
        //                 'name' => $record['name'],
        //                 'category' => $record['category'],
        //                 'uniqueId' => $record['unique_id'],
        //             ],
        //         ]);
        //     }
        // }

        // \App\Models\User::factory()->create([
        //     'name' => 'samir lissa',
        //     'email' => 'samir.lissa@emotiva.com.co',
        //     'password' => bcrypt('lenovo$1'),
        //     'verified' => '1',
        //     'time_zone' => 'america/bogota',
        //     'company_id' => 1,
        // ])->assignRole('super administrator');

        // $permissionsForCompanies = [
        //     'read my profile',
        //     'read dashboard',
        //     'read ais realtime',
        //     'read ais historic',
        //     'read realtime',
        //     'create trip',
        //     'update trip',
        //     'finish trip',
        //     'read historic',
        //     'read roles',
        //     'create roles',
        //     'update roles',
        //     'delete roles',
        //     'create users',
        //     'read users',
        //     'update users',
        //     'delete users',
        //     'restore users',
        //     'read devices',
        // ];

        // Company::create([
        //     'name' => 'geodragados',
        //     'email' => 'isabel.torres@gmail.com',
        //     'root_id_traccar' => '2',
        //     'root_pass_traccar' => 'geodragados2024',
        //     'device_ids' => null,
        //     'permissions' => $permissionsForCompanies,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        // $admin_role_2 = Role::create(['name' => '2_administrator', 'company_id' => 2]);

        // $admin_role_2->givePermissionTo($permissionsForCompanies);


        // \App\Models\User::factory()->create([
        //     'name' => 'isabel torres',
        //     'email' => 'isabel.torres@gmail.com',
        //     'password' => bcrypt('geodragados2024'),
        //     'verified' => '1',
        //     'time_zone' => 'america/bogota',
        //     'company_id' => 2,
        // ])->assignRole('2_administrator');


        // Company::create([
        //     'name' => 'uniban',
        //     'email' => 'karedondo@uniban.com.co',
        //     'root_id_traccar' => '3',
        //     'root_pass_traccar' => 'Uniban2024',
        //     'device_ids' => null,
        //     'permissions' => $permissionsForCompanies,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // ]);

        // $admin_role_3 = Role::create(['name' => '3_administrator', 'company_id' => 3]);

        // $admin_role_3->givePermissionTo($permissionsForCompanies);

        // \App\Models\User::factory()->create([
        //     'name' => 'kafir redondo',
        //     'email' => 'karedondo@uniban.com.co',
        //     'password' => bcrypt('Uniban2024'),
        //     'verified' => '1',
        //     'time_zone' => 'america/bogota',
        //     'company_id' => 3,
        // ])->assignRole('3_administrator');
    }
}
