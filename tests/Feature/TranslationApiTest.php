<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    private string $apiToken = 'apiToken@12345';

    public function test_can_create_translation(): void
    {
        $data = [
            'key' => 'test.greeting',
            'locale' => 'en',
            'content' => 'Hello World',
            'tags' => ['web', 'mobile'],
        ];

        $response = $this->postJson('/api/v1/translations', $data, [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure(['data', 'message']);
        
        $this->assertDatabaseHas('translations', [
            'key' => 'test.greeting',
            'locale' => 'en',
            'content' => 'Hello World',
        ]);
    }

    public function test_can_search_translations(): void
    {
        Translation::factory()->create([
            'key' => 'search.test',
            'locale' => 'en',
            'content' => 'Search content',
            'tags' => ['web'],
        ]);

        $response = $this->getJson('/api/v1/translations?locale=en&tag=web', [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'meta']);
    }

    public function test_can_export_translations(): void
    {
        Translation::factory()->count(5)->create(['locale' => 'en']);

        $response = $this->getJson('/api/v1/translations/export/en', [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['locale', 'translations', 'generated_at']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/translations');
        
        $response->assertStatus(401);
    }

    public function test_performance_with_large_dataset(): void
    {
       
        Translation::factory()->count(1000)->create();

        $startTime = microtime(true);
        
        $response = $this->getJson('/api/v1/translations/export/en', [
            'Authorization' => 'Bearer ' . $this->apiToken,
        ]);
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; 

        $response->assertStatus(200);
        
     
        $this->assertLessThan(500, $executionTime, 'Export endpoint should respond in under 500ms');
    }
}