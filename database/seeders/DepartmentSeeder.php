<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['order' => 1, 'code' => 'MAINT', 'name' => 'Maintenance', 'aliases' => ['MAINT-ENG', 'Maintenance & Engineering']],
            ['order' => 2, 'code' => 'IT', 'name' => 'IT', 'aliases' => ['IT-SYS', 'Information Technology']],
            ['order' => 3, 'code' => 'IE', 'name' => 'IE', 'aliases' => ['Industrial Engineering']],
            ['order' => 4, 'code' => 'EHS', 'name' => 'EHS', 'aliases' => ['SHE', 'Environment Health Safety']],
            ['order' => 5, 'code' => 'GEN', 'name' => 'General', 'aliases' => ['GA', 'General Affairs']],
            ['order' => 6, 'code' => 'HR', 'name' => 'Human Resources', 'aliases' => ['HRD', 'Human Resource']],
            ['order' => 7, 'code' => 'SCM', 'name' => 'Supply Chain', 'aliases' => ['LOG-WH', 'Supply Chain & Warehouse', 'Logistics']],
            ['order' => 8, 'code' => 'QA', 'name' => 'Quality', 'aliases' => ['QA-QC', 'Quality Assurance & Lab', 'QC']],
            ['order' => 9, 'code' => 'TECH', 'name' => 'Technical', 'aliases' => ['Engineering']],
            ['order' => 10, 'code' => 'FIN', 'name' => 'Finance', 'aliases' => ['Accounting', 'Finance & Accounting']],
            ['order' => 11, 'code' => 'PROC', 'name' => 'Procurement', 'aliases' => ['Purchasing']],
            ['order' => 12, 'code' => 'SALES', 'name' => 'Sales', 'aliases' => ['Marketing', 'Sales & Marketing']],
            ['order' => 13, 'code' => 'SUSCON', 'name' => 'Sustainable Construction', 'aliases' => ['Construction']],
            ['order' => 14, 'code' => 'CORD', 'name' => 'CORD', 'aliases' => ['Steel Cord']],
            ['order' => 15, 'code' => 'WWD', 'name' => 'WWD', 'aliases' => ['PROD-W1', 'Production Wire Drawing 1', 'Wire Drawing']],
            ['order' => 16, 'code' => 'HP', 'name' => 'HP', 'aliases' => ['High Products']],
        ];

        $syncedIds = [];

        foreach ($departments as $item) {
            // Cek apakah ada record yang cocok dengan name, code, atau alias lama
            $existing = Department::where('name', $item['name'])
                ->orWhere('code', $item['code'])
                ->first();

            if (! $existing && ! empty($item['aliases'])) {
                $existing = Department::whereIn('code', $item['aliases'])
                    ->orWhereIn('name', $item['aliases'])
                    ->first();
            }

            if ($existing) {
                $existing->update([
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'display_order' => $item['order'],
                    'is_active' => true,
                ]);
                $syncedIds[] = $existing->id;
            } else {
                $new = Department::create([
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'display_order' => $item['order'],
                    'is_active' => true,
                ]);
                $syncedIds[] = $new->id;
            }
        }

        // Nonaktifkan departemen lain yang tidak ada di daftar 16 terlampir
        Department::whereNotIn('id', $syncedIds)->update(['is_active' => false]);
    }
}
