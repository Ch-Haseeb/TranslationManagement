<?php

namespace Tests\Unit;

use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    private TranslationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TranslationService();
    }

    public function test_can_create_translation(): void
    {
        $data = [
            'key' => 'test.key',
            'locale' => 'en',
            'content' => 'Test content',
            'tags' => ['test'],
        ];

        $translation = $this->service->create($data);

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals('test.key', $translation->key);
        $this->assertEquals('en', $translation->locale);
    }

    public function test_can_bulk_create_translations(): void
    {
        $translations = [
            [
                'key' => 'bulk.test1',
                'locale' => 'en',
                'content' => 'Bulk content 1',
                'tags' => json_encode(['bulk']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bulk.test2',
                'locale' => 'en',
                'content' => 'Bulk content 2',
                'tags' => json_encode(['bulk']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $result = $this->service->bulkCreate($translations);

        $this->assertTrue($result);
        $this->assertDatabaseHas('translations', ['key' => 'bulk.test1']);
        $this->assertDatabaseHas('translations', ['key' => 'bulk.test2']);
    }
}
