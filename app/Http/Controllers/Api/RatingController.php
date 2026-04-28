<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Requests\Rating\UpdateRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Product;
use App\Models\Rating;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    public function __construct(private readonly RatingService $service) {}

    /**
     * GET /api/products/{product}/ratings
     */
    public function index(Request $request, Product $product): AnonymousResourceCollection
    {
        $ratings = $product->approvedRatings()
            ->with(['user', 'reactions'])
            ->when($request->score, fn($q, $s) => $q->byScore((int)$s))
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return RatingResource::collection($ratings)
            ->additional([
                'meta' => [
                    'avg'   => $product->rating_avg,
                    'count' => $product->rating_count,
                    'distribution' => $this->getDistribution($product),
                ]
            ]);
    }

    /**
     * POST /api/products/{product}/ratings
     */
    public function store(StoreRatingRequest $request, Product $product): JsonResponse
    {
        $rating = $this->service->store($request->user(), $product, $request->validated());

        return (new RatingResource($rating->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT /api/ratings/{rating}
     */
    public function update(UpdateRatingRequest $request, Rating $rating): RatingResource
    {
        $this->authorize('update', $rating); // Policy

        $updated = $this->service->update($rating, $request->validated());

        return new RatingResource($updated->load(['user', 'reactions']));
    }

    /**
     * DELETE /api/ratings/{rating}
     */
    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('delete', $rating);

        $rating->delete();

        return response()->json(['message' => 'Rating berhasil dihapus.']);
    }

    /**
     * POST /api/ratings/{rating}/react
     */
    public function react(Request $request, Rating $rating): JsonResponse
    {
        $request->validate(['type' => ['required', 'in:like,dislike']]);

        $result = $this->service->toggleReaction($request->user(), $rating, $request->type);

        return response()->json($result);
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function getDistribution(Product $product): array
    {
        $dist = $product->approvedRatings()
            ->selectRaw('score, COUNT(*) as total')
            ->groupBy('score')
            ->pluck('total', 'score')
            ->toArray();

        return collect(range(1, 5))->mapWithKeys(fn($i) => [
            $i => $dist[$i] ?? 0
        ])->toArray();
    }
}
