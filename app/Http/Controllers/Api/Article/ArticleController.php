<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Articles\StoreArticleRequest;
use App\Http\Requests\Articles\UpdateArticleRequest;
use App\Http\Resources\Articles\ArticleResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Articles\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    /*
    |--------------------------------------------------------------------------
    | Public Endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/v1/articles
     * List semua artikel published dengan filter & pagination
     */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::query()
            ->with(['author:id,name', 'categories:id,name,slug', 'tags:id,name,slug'])
            ->withCount(['comments', 'views'])
            ->published()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->category, fn($q) => $q->whereHas(
                'categories',
                fn($q) => $q->where('slug', $request->category)
            ))
            ->when($request->tag, fn($q) => $q->whereHas(
                'tags',
                fn($q) => $q->where('slug', $request->tag)
            ))
            ->when($request->author_id, fn($q) => $q->byAuthor($request->author_id))
            ->latest('published_at')
            ->paginate($request->per_page ?? 15);

        return $this->successResponse(
            ArticleResource::collection($articles)->response()->getData(true)
        );
    }

    /**
     * GET /api/v1/articles/{slug}
     * Detail artikel published
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $article = Article::query()
            ->with([
                'author:id,name',
                'categories:id,name,slug',
                'tags:id,name,slug',
                'comments.user:id,name',
                'metas',
            ])
            ->withCount(['comments', 'views'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $article->recordView($request->ip());

        return $this->successResponse(new ArticleResource($article));
    }

    /*
    |--------------------------------------------------------------------------
    | Protected Endpoints (Auth Required)
    |--------------------------------------------------------------------------
    */

    /**
     * GET /api/v1/admin/articles
     * List artikel milik user yang sedang login
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Article::class);

        $articles = Article::query()
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->withCount(['comments', 'views'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->search($request->search))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return $this->successResponse(
            ArticleResource::collection($articles)->response()->getData(true)
        );
    }

    /**
     * POST /api/v1/articles
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $this->authorize('create', Article::class);

        $article = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $request->file('thumbnail')
                    ->store('articles/thumbnails', 'public');
            }

            $data['author_id'] = Auth::id();

            if ($data['status'] === Article::STATUS_PUBLISHED && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            $article = Article::create($data);
            $article->categories()->sync($data['category_ids'] ?? []);
            $article->tags()->sync($data['tag_ids'] ?? []);

            return $article->load(['author:id,name', 'categories:id,name,slug', 'tags:id,name,slug']);
        });

        return $this->createdResponse(new ArticleResource($article), 'Artikel berhasil dibuat.');
    }

    /**
     * PUT/PATCH /api/v1/articles/{article}
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $this->authorize('update', $article);

        $article = DB::transaction(function () use ($request, $article) {
            $data = $request->validated();

            if ($request->hasFile('thumbnail')) {
                if ($article->thumbnail) {
                    Storage::disk('public')->delete($article->thumbnail);
                }
                $data['thumbnail'] = $request->file('thumbnail')
                    ->store('articles/thumbnails', 'public');
            }

            if (
                isset($data['status']) &&
                $data['status'] === Article::STATUS_PUBLISHED &&
                $article->status === Article::STATUS_DRAFT &&
                empty($article->published_at)
            ) {
                $data['published_at'] = now();
            }

            $article->update($data);

            if (array_key_exists('category_ids', $data)) {
                $article->categories()->sync($data['category_ids'] ?? []);
            }

            if (array_key_exists('tag_ids', $data)) {
                $article->tags()->sync($data['tag_ids'] ?? []);
            }

            return $article->load(['author:id,name', 'categories:id,name,slug', 'tags:id,name,slug']);
        });

        return $this->successResponse(new ArticleResource($article), 'Artikel berhasil diperbarui.');
    }

    /**
     * DELETE /api/v1/articles/{article}
     */
    public function destroy(Article $article): JsonResponse
    {
        $this->authorize('delete', $article);

        DB::transaction(function () use ($article) {
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            $article->categories()->detach();
            $article->tags()->detach();
            $article->delete();
        });

        return $this->noContentResponse();
    }
}
