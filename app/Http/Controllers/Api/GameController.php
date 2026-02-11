<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * GET /games
     */
    public function index()
    {
        $games = Game::where('status', 1)
            ->select('id', 'name', 'slug', 'image', 'description')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'List game',
            'data' => $games
        ]);
    }

    /**
     * GET /games/{slug}
     */
    public function show($slug)
    {
        $game = Game::where('slug', $slug)
            ->where('status', 1)
            ->with(['products' => function ($q) {
                $q->where('status', 1)
                    ->select(
                        'id',
                        'game_id',
                        'name',
                        'price',
                        'specification',
                        'image'
                    );
            }])
            ->firstOrFail();

        return response()->json([
            'status' => true,
            'message' => 'Detail game',
            'data' => $game
        ]);
    }
}
