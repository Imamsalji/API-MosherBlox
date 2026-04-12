<?php

namespace App\Http\Controllers\Api\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Services\ArticleService;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $ArticleService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $filters = $request->only(['status', 'published_at']);
        $ArticleService = $this->ArticleService->getAllPaginated(
            filters: $filters
        );

        return response()->json([
            'status' => true,
            'data'   => [
                'Article' => $ArticleService,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleRequest $request)
    {
        // Upload file di luar transaksi (filesystem bukan atomic)
        $imagePath = null;
        if ($request->hasFile('thumbnail')) {
            $imagePath = $request->file('thumbnail')->store('article', 'public');
        }

        try {
            DB::beginTransaction();

            $article = Article::create([
                'title'     => $request->title,
                'slug'     => $request->slug,
                'content'     => $request->content,
                'excerpt'     => $request->excerpt,
                'thumbnail'     => $imagePath,
                'status'     => $request->status,
                'published_at'     => $request->published_at,
                'author_id'     => Auth::user()->id,
                'content'     => $request->content,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Article berhasil ditambahkan',
                'data'    => $article,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Hapus file yang sudah terlanjur diupload
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('ArticleController@store failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menambahkan Article. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
