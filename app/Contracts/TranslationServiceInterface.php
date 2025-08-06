<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Translation;

interface TranslationServiceInterface
{
    public function create(array $data): Translation;
    public function update(int $id, array $data): Translation;
    public function find(int $id): ?Translation;
    public function search(array $filters): LengthAwarePaginator;
    public function exportForLocale(string $locale): array;
    public function bulkCreate(array $translations): bool;
}
