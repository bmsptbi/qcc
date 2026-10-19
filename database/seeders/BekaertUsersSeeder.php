<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BekaertUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                'nik' => '971205',
                'name' => 'Abdul Wakhid',
                'email' => 'Abdul.Wakhid@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'Sustainable Construction',
            ],
            [
                'nik' => '980804',
                'name' => 'Achmad Sodikin',
                'email' => 'ahmad.sodikin@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'WWD',
            ],
            [
                'nik' => '70205',
                'name' => 'Antonius Aswin Sidharta',
                'email' => 'anthonius.sidharta@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'TECHNICAL',
            ],
            [
                'nik' => '70904',
                'name' => 'Asnita Rawati S',
                'email' => 'asnita.rawati@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'PROCUREMENT',
            ],
            [
                'nik' => '210501',
                'name' => 'Dedy Rudiyanto',
                'email' => 'dedy.rudiyanto@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'SALES',
            ],
            [
                'nik' => '220502',
                'name' => 'Dodi Yosfa Aulia',
                'email' => 'dodi.yosfaaulia@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'FINANCE',
            ],
            [
                'nik' => '140601',
                'name' => 'Dusti Januarsid',
                'email' => 'Dusti.Januarsid@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'GENERAL',
            ],
            [
                'nik' => '200201',
                'name' => 'Ekky Putra Malonda',
                'email' => 'EkkyPutra.Malonda@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'SALES',
            ],
            [
                'nik' => '980801',
                'name' => 'Heri Wijoyanto',
                'email' => 'Heri.Wijayanto@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'QUALITY',
            ],
            [
                'nik' => '100201',
                'name' => 'Heru Purbo Wiyoto',
                'email' => 'Heru.PurboWiyoto@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'MAINTENANCE',
            ],
            [
                'nik' => '170401',
                'name' => 'Ihda Taftazani',
                'email' => 'Ihda.Taftazani@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'EHS',
            ],
            [
                'nik' => '980803',
                'name' => 'Indra Kurniawan Soehartono',
                'email' => 'Indra.Kurniawan@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'CORD',
            ],
            [
                'nik' => '80808',
                'name' => 'Johan Irawan',
                'email' => 'Johan.Irawan@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'SALES',
            ],
            [
                'nik' => '80625',
                'name' => 'Muhammad Erieq Rizal',
                'email' => 'erieq.rizal@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'SUPPLY CHAIN & LOGISTIC',
            ],
            [
                'nik' => '10501',
                'name' => 'Muhammad Faishol',
                'email' => 'muhammad.faishol@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'TECHNICAL',
            ],
            [
                'nik' => '100601',
                'name' => 'Nina Winengku Lestari',
                'email' => 'Nina.WinengkuLestari@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'SUPPLY CHAIN & LOGISTIC',
            ],
            [
                'nik' => '140335',
                'name' => 'Restu Girik Allo',
                'email' => 'Restu.Girik@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'MAINTENANCE',
            ],
            [
                'nik' => '200703',
                'name' => 'Rizky Febryandy',
                'email' => 'Rizky.Febryandy@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'FINANCE',
            ],
            [
                'nik' => '980802',
                'name' => 'Slamet Hariyono',
                'email' => 'Slamet.Hariyono@bekaert.com',
                'roles' => ['facilitator'],
                'role' => 'facilitator',
                'dept' => 'HP',
            ],
            [
                'nik' => '120405',
                'name' => 'Bahasuan Syafik',
                'email' => 'syafik.bahasuan@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'SALES',
            ],
            [
                'nik' => '170601',
                'name' => 'Toha',
                'email' => 'Filipus.Toha@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'PROCUREMENT',
            ],
            [
                'nik' => '260409',
                'name' => 'Khairi Apriandi',
                'email' => 'khairi.apriandi@bekaert.com',
                'roles' => ['judge', 'facilitator'],
                'role' => 'judge',
                'dept' => 'HUMAN RESOURCES',
            ],
        ];

        $deptMap = [
            'TECHNICAL' => 'TECH',
            'PROCUREMENT' => 'PROC',
            'SALES' => 'SALES',
            'FINANCE' => 'FIN',
            'GENERAL' => 'GEN',
            'QUALITY' => 'QA',
            'MAINTENANCE' => 'MAINT',
            'EHS' => 'EHS',
            'CORD' => 'CORD',
            'SUPPLY CHAIN & LOGISTIC' => 'SCM',
            'HP' => 'HP',
            'HUMAN RESOURCES' => 'HR',
            'WWD' => 'WWD',
            'SUSTAINABLE CONSTRUCTION' => 'SUSCON',
        ];

        $defaultPassword = Hash::make('password');

        foreach ($usersData as $row) {
            $deptName = strtoupper(trim($row['dept']));
            $deptCode = $deptMap[$deptName] ?? $deptName;

            $department = Department::where('code', $deptCode)
                ->orWhere('name', $row['dept'])
                ->first();

            $departmentId = $department?->id;

            // Cari user berdasarkan email atau NIK
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($row['email'])])
                ->orWhere('nik', $row['nik'])
                ->first();

            if ($user) {
                $user->update([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'nik' => $row['nik'],
                    'role' => $row['role'],
                    'roles' => $row['roles'],
                    'department_id' => $departmentId,
                ]);
            } else {
                User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'nik' => $row['nik'],
                    'role' => $row['role'],
                    'roles' => $row['roles'],
                    'department_id' => $departmentId,
                    'password' => $defaultPassword,
                ]);
            }
        }
    }
}
