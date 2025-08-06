<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating 100k+ translation records...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $batchSize = 2000;
        $totalRecords = 100000;
        $batches = ceil($totalRecords / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $this->command->info("Creating batch " . ($i + 1) . " of {$batches}");
            Translation::factory($batchSize)->create();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Translation seeding completed!');
    }
}
