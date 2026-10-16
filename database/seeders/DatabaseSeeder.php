<?php

namespace Database\Seeders;

use App\Models\AssessmentCriterion;
use App\Models\AssessmentPeriod;
use App\Models\Circle;
use App\Models\CircleCategory;
use App\Models\CircleMember;
use App\Models\CriteriaCategory;
use App\Models\Department;
use App\Models\JudgeAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Departments
        $deptProd1 = Department::firstOrCreate(['code' => 'PROD-W1'], [
            'name' => 'Production Wire Drawing 1',
            'description' => 'Area produksi kawat primer dan drawing halus',
            'is_active' => true,
        ]);

        $deptMaint = Department::firstOrCreate(['code' => 'MAINT-ENG'], [
            'name' => 'Maintenance & Engineering',
            'description' => 'Departemen pemeliharaan mesin, utilitas, dan otomasi',
            'is_active' => true,
        ]);

        $deptQA = Department::firstOrCreate(['code' => 'QA-QC'], [
            'name' => 'Quality Assurance & Lab',
            'description' => 'Pengujian mutu, sertifikasi produk, dan kalibrasi alat uji',
            'is_active' => true,
        ]);

        $deptLog = Department::firstOrCreate(['code' => 'LOG-WH'], [
            'name' => 'Supply Chain & Warehouse',
            'description' => 'Manajemen bahan baku, suku cadang, dan pengiriman barang jadi',
            'is_active' => true,
        ]);

        // 2. Users (Admin, Judges, Facilitator, Circle Leader)
        $password = Hash::make('password');

        $admin = User::firstOrCreate(['email' => 'admin@bekaert.com'], [
            'name' => 'Administrator QCC',
            'password' => $password,
            'role' => 'admin',
            'nik' => 'EMP-001',
            'department_id' => $deptQA->id,
            'phone' => '081234567890',
        ]);

        $judge1 = User::firstOrCreate(['email' => 'juri1@bekaert.com'], [
            'name' => 'Ir. Bambang Wijaya (Lead Judge)',
            'password' => $password,
            'role' => 'judge',
            'nik' => 'EMP-010',
            'department_id' => $deptQA->id,
            'phone' => '081234567891',
        ]);

        $judge2 = User::firstOrCreate(['email' => 'juri2@bekaert.com'], [
            'name' => 'Dr. Hendra Saputra, M.T.',
            'password' => $password,
            'role' => 'judge',
            'nik' => 'EMP-011',
            'department_id' => $deptMaint->id,
            'phone' => '081234567892',
        ]);

        $judge3 = User::firstOrCreate(['email' => 'juri3@bekaert.com'], [
            'name' => 'Siti Rahmawati, S.T.',
            'password' => $password,
            'role' => 'judge',
            'nik' => 'EMP-012',
            'department_id' => $deptProd1->id,
            'phone' => '081234567893',
        ]);

        $facilitator = User::firstOrCreate(['email' => 'fasilitator1@bekaert.com'], [
            'name' => 'Agus Gunawan (Facilitator)',
            'password' => $password,
            'role' => 'facilitator',
            'nik' => 'EMP-020',
            'department_id' => $deptProd1->id,
            'phone' => '081234567894',
        ]);

        $leader1 = User::firstOrCreate(['email' => 'leader1@bekaert.com'], [
            'name' => 'Rian Pratama',
            'password' => $password,
            'role' => 'circle_leader',
            'nik' => 'EMP-101',
            'department_id' => $deptProd1->id,
            'phone' => '081234567895',
        ]);

        // 3. Assessment Period
        $period = AssessmentPeriod::firstOrCreate(['name' => 'Konvensi Mutu & QCC 2026'], [
            'year' => 2026,
            'theme' => 'Driving Innovation, Reliability & Operational Excellence',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'registration_deadline' => '2026-11-30',
            'status' => 'active',
            'notes' => 'Konvensi gugus kendali mutu tahunan seluruh plant',
        ]);

        // 4. Circle Categories
        $catMfg = CircleCategory::firstOrCreate(['code' => 'MFG'], [
            'name' => 'Manufacturing & Operations',
            'description' => 'Circle perbaikan di lini produksi dan proses manufaktur langsung',
            'is_active' => true,
        ]);

        $catOffice = CircleCategory::firstOrCreate(['code' => 'OFFICE'], [
            'name' => 'Office & Support Services',
            'description' => 'Circle di bidang administrasi, logistik, finance, dan pelayanan',
            'is_active' => true,
        ]);

        $catSga = CircleCategory::firstOrCreate(['code' => 'SGA'], [
            'name' => 'Small Group Activity (SGA)',
            'description' => 'Aktivitas kelompok kecil fokus 5S, Safety, dan pemeliharaan dasar',
            'is_active' => true,
        ]);

        $catQcp = CircleCategory::firstOrCreate(['code' => 'QCP'], [
            'name' => 'Quality Control Project (QCP)',
            'description' => 'Proyek lintas divisi dengan kompleksitas teknik tinggi',
            'is_active' => true,
        ]);

        // 5. Criteria Categories & Criteria (Standard 8 Langkah PDCA + Presentasi)
        // Kategori 1: Plan (Bobot 35%)
        $catPlan = CriteriaCategory::firstOrCreate(['name' => '1. Plan (Langkah 1 - 4)'], [
            'stage' => 'plan',
            'weight_percentage' => 35.00,
            'display_order' => 1,
            'description' => 'Perencanaan perbaikan: Penentuan tema, analisis situasi, sebab akibat, dan rencana penanggulangan',
        ]);

        $criteriaDataPlan = [
            [
                'code' => 'P1',
                'title' => 'Penentuan Tema & Sasaran (SMART)',
                'guidelines' => 'Latar belakang masalah jelas didukung data historis QCDSMPE, kesesuaian dengan target departemen/perusahaan, dan penetapan target terukur (SMART).',
                'weight' => 10.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 1,
            ],
            [
                'code' => 'P2',
                'title' => 'Analisis Kondisi yang Ada / Pemetaan Fakta (Gemba)',
                'guidelines' => 'Pengumpulan data primer di lapangan, stratifikasi data, penggunaan 7 QC Tools (Pareto, Histogram, Flowchart) yang relevan dan mendalam.',
                'weight' => 10.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 2,
            ],
            [
                'code' => 'P3',
                'title' => 'Analisis Sebab Akibat (Fishbone & 5-Why)',
                'guidelines' => 'Penguraian faktor 4M+1E (Man, Machine, Material, Method, Environment), logika 5-Why yang kuat hingga menemukan akar penyebab (root cause) yang terbukti.',
                'weight' => 8.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 3,
            ],
            [
                'code' => 'P4',
                'title' => 'Rencana Penanggulangan (5W + 1H)',
                'guidelines' => 'Kelayakan alternatif solusi, efektivitas biaya, analisis risiko, serta kejelasan PIC, timeline pelaksanaan, dan target antara.',
                'weight' => 7.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 4,
            ],
        ];

        foreach ($criteriaDataPlan as $item) {
            AssessmentCriterion::firstOrCreate(
                ['criteria_category_id' => $catPlan->id, 'code' => $item['code']],
                $item
            );
        }

        // Kategori 2: Do (Bobot 20%)
        $catDo = CriteriaCategory::firstOrCreate(['name' => '2. Do (Langkah 5)'], [
            'stage' => 'do',
            'weight_percentage' => 20.00,
            'display_order' => 2,
            'description' => 'Pelaksanaan tindakan perbaikan dan penanggulangan masalah',
        ]);

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catDo->id, 'code' => 'D1'],
            [
                'title' => 'Pelaksanaan Tindakan & Inovasi Perbaikan',
                'guidelines' => 'Kesesuaian implementasi dengan rencana 5W+1H, partisipasi aktif anggota gugus, kreativitas inovasi, dan monitoring selama masa uji coba.',
                'weight' => 20.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 1,
            ]
        );

        // Kategori 3: Check (Bobot 25%)
        $catCheck = CriteriaCategory::firstOrCreate(['name' => '3. Check (Langkah 6)'], [
            'stage' => 'check',
            'weight_percentage' => 25.00,
            'display_order' => 3,
            'description' => 'Evaluasi hasil perbaikan (Tangible & Intangible benefit)',
        ]);

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catCheck->id, 'code' => 'C1'],
            [
                'title' => 'Evaluasi Hasil Nyata / Tangible Benefit (Cost, Quality, Delivery)',
                'guidelines' => 'Pencapaian target KPI sebelum vs sesudah, validitas perhitungan penghematan biaya (cost saving / ROI), peningkatan OEE/produktivitas.',
                'weight' => 15.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 1,
            ]
        );

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catCheck->id, 'code' => 'C2'],
            [
                'title' => 'Dampak Tidak Langsung / Intangible (Safety, Morale, 5S)',
                'guidelines' => 'Dampak terhadap keselamatan kerja (Safety zero accident), motivasi kerja tim, kerapian 5S, serta penurunan potensi keluhan pelanggan.',
                'weight' => 10.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 2,
            ]
        );

        // Kategori 4: Action (Bobot 10%)
        $catAction = CriteriaCategory::firstOrCreate(['name' => '4. Action (Langkah 7 & 8)'], [
            'stage' => 'action',
            'weight_percentage' => 10.00,
            'display_order' => 4,
            'description' => 'Standarisasi SOP dan penetapan rencana perbaikan berikutnya',
        ]);

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catAction->id, 'code' => 'A1'],
            [
                'title' => 'Standarisasi & Pencegahan Terulang',
                'guidelines' => 'Pembuatan/revisi Standar Operasional Prosedur (SOP/WI), One Point Lesson (OPL), mekanisme poka-yoke, dan sosialisasi kepada seluruh shift.',
                'weight' => 6.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 1,
            ]
        );

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catAction->id, 'code' => 'A2'],
            [
                'title' => 'Penetapan Tema Rencana Berikutnya',
                'guidelines' => 'Identifikasi sisa permasalahan, pemilihan tema perbaikan putaran berikutnya berdasarkan matriks prioritas, serta komitmen tim.',
                'weight' => 4.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 2,
            ]
        );

        // Kategori 5: Presentasi (Bobot 10%)
        $catPres = CriteriaCategory::firstOrCreate(['name' => '5. Presentasi & Dinamika Tim'], [
            'stage' => 'presentation',
            'weight_percentage' => 10.00,
            'display_order' => 5,
            'description' => 'Kualitas penyajian materi konvensi, penguasaan tanya jawab, dan kekompakan tim',
        ]);

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catPres->id, 'code' => 'PR1'],
            [
                'title' => 'Kekompakan Tim & Penguasaan Tanya Jawab',
                'guidelines' => 'Seluruh anggota aktif berpartisipasi, jawaban tegas berbasis fakta Gemba dan data, antusiasme dan rasa memiliki terhadap proyek.',
                'weight' => 6.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 1,
            ]
        );

        AssessmentCriterion::firstOrCreate(
            ['criteria_category_id' => $catPres->id, 'code' => 'PR2'],
            [
                'title' => 'Sistematika Presentasi & Kepatuhan Waktu',
                'guidelines' => 'Alur slide visual jelas, bahasa lugas dan percaya diri, kepatuhan tepat waktu (15 menit presentasi + 10 menit Q&A).',
                'weight' => 4.00,
                'min_score' => 0,
                'max_score' => 100,
                'display_order' => 2,
            ]
        );

        // 6. Judge Assignments
        JudgeAssignment::firstOrCreate([
            'assessment_period_id' => $period->id,
            'judge_id' => $judge1->id,
        ], [
            'circle_category_id' => $catMfg->id,
            'is_lead_judge' => true,
            'notes' => 'Koordinator juri kategori Manufaktur',
        ]);

        JudgeAssignment::firstOrCreate([
            'assessment_period_id' => $period->id,
            'judge_id' => $judge2->id,
        ], [
            'circle_category_id' => $catMfg->id,
            'is_lead_judge' => false,
            'notes' => 'Juri penilai teknis dan engineering',
        ]);

        JudgeAssignment::firstOrCreate([
            'assessment_period_id' => $period->id,
            'judge_id' => $judge3->id,
        ], [
            'circle_category_id' => $catMfg->id,
            'is_lead_judge' => false,
            'notes' => 'Juri penilai metodologi mutu & PDCA',
        ]);

        // 7. Sample Circles & Members
        $circle1 = Circle::firstOrCreate(['circle_code' => 'QCC-2026-001'], [
            'assessment_period_id' => $period->id,
            'circle_category_id' => $catMfg->id,
            'department_id' => $deptProd1->id,
            'facilitator_id' => $facilitator->id,
            'leader_id' => $leader1->id,
            'circle_name' => 'QCC SPARK',
            'theme_title' => 'Penurunan Cacat Scratches pada Wire Drawing Line 3 Sebesar 78%',
            'problem_statement' => 'Tingginya defect scratches sebesar 4.2% pada kawat baja halus mengakibatkan reject rate tinggi dan biaya scrap rata-rata Rp 45.000.000/bulan.',
            'target_statement' => 'Menurunkan defect scratches dari 4.2% menjadi di bawah 0.9% dalam periode 4 bulan implementasi.',
            'presentation_schedule' => now()->addHours(2),
            'presentation_room' => 'Auditorium Lantai 2 / Hybrid Hall A',
            'status' => 'scheduled',
            'final_score' => null,
        ]);

        $circleMembers1 = [
            ['name' => 'Rian Pratama', 'nik' => 'EMP-101', 'role' => 'leader', 'email' => 'rian@bekaert.com'],
            ['name' => 'Dimas Anggara', 'nik' => 'EMP-102', 'role' => 'secretary', 'email' => 'dimas@bekaert.com'],
            ['name' => 'Budi Santoso', 'nik' => 'EMP-103', 'role' => 'member', 'email' => 'budi@bekaert.com'],
            ['name' => 'Wahyu Hidayat', 'nik' => 'EMP-104', 'role' => 'member', 'email' => 'wahyu@bekaert.com'],
            ['name' => 'Teguh Prasetyo', 'nik' => 'EMP-105', 'role' => 'member', 'email' => 'teguh@bekaert.com'],
        ];

        foreach ($circleMembers1 as $m) {
            CircleMember::firstOrCreate(
                ['circle_id' => $circle1->id, 'nik' => $m['nik']],
                $m
            );
        }

        $circle2 = Circle::firstOrCreate(['circle_code' => 'QCC-2026-002'], [
            'assessment_period_id' => $period->id,
            'circle_category_id' => $catMfg->id,
            'department_id' => $deptMaint->id,
            'facilitator_id' => $facilitator->id,
            'circle_name' => 'KAIZEN FORCE',
            'theme_title' => 'Optimasi Waktu Changeover Die Mesin Stranding dari 45 Menit ke 16 Menit (SMED)',
            'problem_statement' => 'Waktu pergantian die size pada mesin stranding memakan waktu 45 menit menyebabkan kehilangan jam produksi 18 jam/minggu.',
            'target_statement' => 'Menerapkan metode SMED untuk mencapai pergantian cetakan di bawah 20 menit tanpa kompromi aspek safety.',
            'presentation_schedule' => now()->addHours(3)->addMinutes(30),
            'presentation_room' => 'Auditorium Lantai 2 / Hybrid Hall A',
            'status' => 'scheduled',
            'final_score' => null,
        ]);

        $circleMembers2 = [
            ['name' => 'Dwi Cahyono', 'nik' => 'EMP-201', 'role' => 'leader', 'email' => 'dwi@bekaert.com'],
            ['name' => 'Eko Sulistyo', 'nik' => 'EMP-202', 'role' => 'secretary', 'email' => 'eko@bekaert.com'],
            ['name' => 'Fajar Nugraha', 'nik' => 'EMP-203', 'role' => 'member', 'email' => 'fajar@bekaert.com'],
        ];

        foreach ($circleMembers2 as $m) {
            CircleMember::firstOrCreate(
                ['circle_id' => $circle2->id, 'nik' => $m['nik']],
                $m
            );
        }

        $circle3 = Circle::firstOrCreate(['circle_code' => 'QCC-2026-003'], [
            'assessment_period_id' => $period->id,
            'circle_category_id' => $catQcp->id,
            'department_id' => $deptQA->id,
            'facilitator_id' => $facilitator->id,
            'circle_name' => 'ZERO DEFECT',
            'theme_title' => 'Pencegahan Kontaminasi Oli pada Proses Galvanizing dengan Poka-Yoke Sensor Otomatis',
            'problem_statement' => 'Kontaminasi residu oli pelumas menyebabkan lapisan seng tidak menempel sempurna sehingga terjadi blistering coating.',
            'target_statement' => 'Pencapaian zero defect blistering coating dan efisiensi chemical pickling 15%.',
            'presentation_schedule' => now()->addDays(1)->setHour(9)->setMinute(0),
            'presentation_room' => 'Meeting Room Bima Plant 1',
            'status' => 'verified',
            'final_score' => null,
        ]);

        $circle4 = Circle::firstOrCreate(['circle_code' => 'QCC-2026-004'], [
            'assessment_period_id' => $period->id,
            'circle_category_id' => $catOffice->id,
            'department_id' => $deptLog->id,
            'facilitator_id' => $facilitator->id,
            'circle_name' => 'FAST LOGIX',
            'theme_title' => 'Digitalisasi Sistem Pemesanan & Pengambilan Sparepart Gudang via Mobile Barcode Scanner',
            'problem_statement' => 'Proses manual pencatatan buku bon sparepart membutuhkan waktu pencarian fisik 25 menit per item teknisi.',
            'target_statement' => 'Memangkas waktu pencarian fisik sparepart menjadi rata-rata 4 menit dengan akurasi stok 99.8%.',
            'presentation_schedule' => now()->addDays(1)->setHour(10)->setMinute(30),
            'presentation_room' => 'Meeting Room Bima Plant 1',
            'status' => 'verified',
            'final_score' => null,
        ]);
    }
}
