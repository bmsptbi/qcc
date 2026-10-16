<?php

namespace App\Console\Commands;

use App\Models\AssessmentCriterion;
use App\Models\CriteriaCategory;
use Illuminate\Console\Command;

class SeedPenjurianLapanganCriteria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qcc:seed-lapangan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memasukkan butir kriteria penilaian ke tahap Penjurian Lapangan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Cari atau pastikan kategori Penjurian Lapangan ada
        $category = CriteriaCategory::where('name', 'like', '%Penjurian Lapangan%')
            ->orWhere('name', 'like', '%Lapangan%')
            ->first();

        if (! $category) {
            $category = CriteriaCategory::create([
                'name' => '2. Penjurian Lapangan',
                'stage' => 'check',
                'weight_percentage' => 50.00,
                'display_order' => 2,
                'description' => 'Penilaian implementasi faktual di lapangan / gemba',
            ]);
            $this->info("Kategori '{$category->name}' dibuat.");
        } else {
            $this->info("Menggunakan kategori yang ada: '{$category->name}' (ID: {$category->id})");
        }

        // 25 butir kriteria dari list user
        $items = [
            'Kelengkapan notulen/dokumentasi',
            'Standarisasi (SOP) dari tema sebelumnya',
            'Kerjasama dalam aktifitas QCC',
            'Absensi pertemuan',
            'Perubahan paradigma(pola pikir)',
            'Pemahaman anggota ttg materi',
            'Analisa pemilihan thema (Penggunaan 7 Tools Diagram)',
            'SMART',
            'Diagram sebab akibat dapat diuji terbalik.',
            'Target setiap aktifitas',
            'Originalitas ide perbaikan',
            'Korelasi terhadap batas waktu',
            'Cost >< Benefit',
            'Monitor standarisasi',
            'Ada thema berikut',
            'Ada Thema Leader Baru & Ada Rencana Kerja',
            'Efektifitas penyampaian ide perbaikan',
            'Penampilan team presentator',
            'Ketepatan Waktu Presentasi',
            'Inovasi',
            'Relevansi',
            'Effectivitas',
            'Efisiensi',
            'Keberlanjutan',
            'Dampak Improvement',
        ];

        // Bobot per item: 50% / 25 item = 2% per butir
        $weightPerItem = 2.00;

        foreach ($items as $index => $itemTitle) {
            $num = $index + 1;
            $code = sprintf('PL%02d', $num);

            $criterion = AssessmentCriterion::updateOrCreate(
                [
                    'criteria_category_id' => $category->id,
                    'code' => $code,
                ],
                [
                    'title' => $itemTitle,
                    'guidelines' => "Evaluasi lapangan terkait: {$itemTitle}",
                    'weight' => $weightPerItem,
                    'min_score' => 0,
                    'max_score' => 100,
                    'display_order' => $num,
                    'is_active' => true,
                ]
            );

            $this->line(" [OK] ({$code}) {$itemTitle}");
        }

        $this->info("Berhasil memasukkan 25 butir kriteria ke tahap '{$category->name}'!");

        return Command::SUCCESS;
    }
}
