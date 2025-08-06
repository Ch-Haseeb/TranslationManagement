<?php

namespace App\Services;

use App\Contracts\TranslationServiceInterface;
use App\Models\Translation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TranslationService implements TranslationServiceInterface
{
    public function create(array $data): Translation
    {

        $this->clearCacheForLocale($data['locale']);

        return Translation::create($data);
    }

    public function update(int $id, array $data): Translation
    {
        $translation = Translation::findOrFail($id);

        $this->clearCacheForLocale($translation->locale);
        if (isset($data['locale']) && $data['locale'] !== $translation->locale) {
            $this->clearCacheForLocale($data['locale']);
        }

        $translation->update($data);
        return $translation->fresh();
    }

    public function find(int $id): ?Translation
    {
        return Translation::find($id);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Translation::query();


        if (!empty($filters['locale'])) {
            $query->forLocale($filters['locale']);
        }

        if (!empty($filters['tag'])) {
            $query->withTag($filters['tag']);
        }

        if (!empty($filters['key_search'])) {
            $query->keySearch($filters['key_search']);
        }

        if (!empty($filters['content_search'])) {
            $query->contentSearch($filters['content_search']);
        }


        $query->orderBy('updated_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function exportForLocale(string $locale): array
    {

        $cacheKey = "translations_export_{$locale}";

        return Cache::remember($cacheKey, 300, function () use ($locale) {
            $translations = Translation::forLocale($locale)
                ->select('key', 'content', 'tags')
                ->get();

            $result = [];
            foreach ($translations as $translation) {
                $result[$translation->key] = [
                    'content' => $translation->content,
                    'tags' => $translation->tags ?? [],
                ];
            }

            return $result;
        });
    }

    public function bulkCreate(array $translations): bool
    {
        DB::beginTransaction();

        try {

            $locales = collect($translations)->pluck('locale')->unique();
            foreach ($locales as $locale) {
                $this->clearCacheForLocale($locale);
            }


            $chunks = array_chunk($translations, 1000);

            foreach ($chunks as $chunk) {
                Translation::insert($chunk);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function clearCacheForLocale(string $locale): void
    {
        Cache::forget("translations_export_{$locale}");
    }
}
