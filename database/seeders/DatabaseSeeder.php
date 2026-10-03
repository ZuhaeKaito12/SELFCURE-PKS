<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Production-safe seeding: reference data (5 NIST CSF domains, 17 questions
     * and their actions) plus the administrator account. No demo users or data.
     *
     * Safe to run on every deploy. QuestionSeeder deletes and rebuilds questions,
     * which would cascade to every answer and action item, so it only runs on an
     * empty database.
     */
    public function run(): void
    {
        $this->call(DomainSeeder::class);

        if (Question::query()->doesntExist()) {
            $this->call(QuestionSeeder::class);
        } else {
            $this->command?->info('Questions already present; reference data left unchanged.');
        }

        $this->call(AdminUserSeeder::class);
    }
}
