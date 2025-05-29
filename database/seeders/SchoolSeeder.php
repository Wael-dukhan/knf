<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::insert([
            [
                'name' => 'مدرسة الأمل الابتدائية',
                'location' => 'الرياض',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مدرسة المستقبل الثانوية',
                'location' => 'جدة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مدرسة التميز الدولية',
                'location' => 'الدمام',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
