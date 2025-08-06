<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\TranslationServiceInterface;

class PopulateTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations-command';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Populate database with translation records for testing';

    /**
     * Execute the console command.
     */
   public function __construct(
        private TranslationServiceInterface $translationService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $count = (int) $this->argument('count');
        
        $this->info("Generating {$count} translation records...");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Generate translations in chunks
        $chunkSize = 1000;
        $chunks = ceil($count / $chunkSize);

        for ($i = 0; $i < $chunks; $i++) {
            $currentChunkSize = min($chunkSize, $count - ($i * $chunkSize));
            $translations = $this->generateTranslations($currentChunkSize);
            
            $this->translationService->bulkCreate($translations);
            $bar->advance($currentChunkSize);
        }

        $bar->finish();
        $this->newLine();
        $this->info('Translation population completed successfully!');
    }

    private function generateTranslations(int $count): array
    {
        $locales = ['en', 'fr', 'es', 'de', 'it', 'pt', 'ru', 'ja', 'zh'];
        $tags = ['web', 'mobile', 'desktop', 'email', 'sms', 'push'];
        $keyPrefixes = ['nav', 'form', 'error', 'success', 'common', 'auth'];

        $translations = [];

        for ($i = 0; $i < $count; $i++) {
            $translations[] = [
                'key' => fake()->randomElement($keyPrefixes) . '.' . fake()->word() . '_' . fake()->numberBetween(1, 999),
                'locale' => fake()->randomElement($locales),
                'content' => fake()->sentence(fake()->numberBetween(3, 15)),
                'tags' => json_encode(fake()->randomElements($tags, fake()->numberBetween(1, 3))),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $translations;
    }
}
