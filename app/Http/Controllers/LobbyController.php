<?php

namespace App\Http\Controllers;

use App\Models\Lobby;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LobbyController extends Controller
{
    /**
     * Create a new lobby
     *
     * @response array{lobbies: Lobby[]}
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $lobbies = Lobby::all();
            return response()->json(["lobbies" => $lobbies], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching the lobbies', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a lobby
     *
     * @param int $id
     * @param Request $request
     * @response array{lobby: Lobby}
     * @return JsonResponse
     * @response array{lobby: Lobby}
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);

            $lobby = Lobby::find($id);
            $lobby->name = $request->name;
            $lobby->save();

            return response()->json(['lobby' => $lobby], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while updating the lobby', 'error' => $e->getMessage()], 500);
        }
    }
}
