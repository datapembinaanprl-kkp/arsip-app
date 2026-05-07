<?php

namespace App\Http\Controllers\GIS;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpatialDataRequest;
use App\Models\SpatialData;
use App\Services\GisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpatialDataController extends Controller
{
    public function __construct(private readonly GisService $gisService) {}

    /**
     * Render Inertia page — dilindungi auth middleware di routes
     */
    public function index(): Response
    {
        return Inertia::render('GIS/Dashboard', [
            'kategoriList' => SpatialData::getKategoriList(),
        ]);
    }

    public function apiIndex(Request $request): JsonResponse
    {
        return response()->json(
            $this->gisService->getFeatureCollection($request->only(['search', 'kategori']))
        );
    }

    public function apiList(Request $request): JsonResponse
    {
        $paginated = $this->gisService->getPaginatedList(
            $request->only(['search', 'kategori']),
            (int) $request->get('per_page', 12)
        );

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function apiShow(int $id): JsonResponse
    {
        $feature = $this->gisService->getFeatureById($id);
        return $feature
            ? response()->json($feature)
            : response()->json(['message' => 'Tidak ditemukan.'], 404);
    }

    public function apiStore(SpatialDataRequest $request): JsonResponse
    {
        try {
            $id = $this->gisService->create($request->validated());
            return response()->json($this->gisService->getFeatureById($id), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function apiUpdate(SpatialDataRequest $request, int $id): JsonResponse
    {
        try {
            $this->gisService->update($id, $request->validated());
            return response()->json($this->gisService->getFeatureById($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function apiDestroy(int $id): JsonResponse
    {
        return $this->gisService->delete($id)
            ? response()->json(['message' => 'Data berhasil dihapus.'])
            : response()->json(['message' => 'Tidak ditemukan.'], 404);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'geojson.type'     => ['required', 'in:FeatureCollection'],
            'geojson.features' => ['required', 'array', 'min:1'],
        ]);

        try {
            $result = $this->gisService->importGeoJson($request->input('geojson'));
            return response()->json(['message' => "Import selesai: {$result['success']} berhasil.", 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $geojson  = $this->gisService->getFeatureCollection($request->only(['search', 'kategori']));
        $filename = 'spatial-export-' . now()->format('Ymd-His') . '.geojson';

        return response()->streamDownload(
            fn() => print(json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)),
            $filename,
            ['Content-Type' => 'application/geo+json']
        );
    }

    public function categories(): JsonResponse
    {
        return response()->json(['data' => SpatialData::getKategoriList()]);
    }
}