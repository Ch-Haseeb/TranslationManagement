<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\TranslationServiceInterface;
use App\Http\Requests\CreateTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Requests\SearchTranslationRequest;
use App\Http\Resources\TranslationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationServiceInterface $translationService
    ) {}

    public function index(SearchTranslationRequest $request): JsonResponse
    {
        $translations = $this->translationService->search($request->validated());

        return response()->json([
            'data' => TranslationResource::collection($translations),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ],
        ]);
    }

    public function store(CreateTranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->create($request->validated());

        return response()->json([
            'data' => new TranslationResource($translation),
            'message' => 'Translation created successfully',
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $translation = $this->translationService->find($id);

        if (!$translation) {
            return response()->json([
                'message' => 'Translation not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => new TranslationResource($translation),
        ]);
    }

    public function update(UpdateTranslationRequest $request, int $id): JsonResponse
    {
        try {
            $translation = $this->translationService->update($id, $request->validated());

            return response()->json([
                'data' => new TranslationResource($translation),
                'message' => 'Translation updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Translation not found',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $translation = $this->translationService->find($id);

        if (!$translation) {
            return response()->json([
                'message' => 'Translation not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $translation->delete();

        return response()->json([
            'message' => 'Translation deleted successfully',
        ]);
    }

    public function export(string $locale): JsonResponse
    {
        $translations = $this->translationService->exportForLocale($locale);

        return response()->json([
            'locale' => $locale,
            'translations' => $translations,
            'generated_at' => now()->toISOString(),
        ]);
    }
}
