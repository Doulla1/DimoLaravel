<?php

namespace App\Http\Controllers;

use App\Models\Skin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class SkinController extends Controller
{
    /**
     * Get all skins
     *
     * @response array{skins: Skin[]}
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $skins = Skin::all();
            return response()->json(["skins" => $skins]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new skin
     *
     * @param Request $request
     * @response array{skin: Skin}
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $skin = new Skin();
            $skin->user_id = $request->user_id;
            $skin->skin_part_version_id = $request->skin_part_version_id;
            $skin->color = $request->color;
            $skin->save();
            return response()->json(["skin" => $skin]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single skin
     *
     * @param int $id
     * @response array{skin: Skin}
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        try {
            $skin = Skin::find($id);
            return response()->json(["skin" => $skin]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @response array{skin: Skin}
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            $skin = Skin::find($id);
            $skin->user_id = $request->user_id;
            $skin->skin_part_version_id = $request->skin_part_version_id;
            $skin->color = $request->color;
            $skin->save();
            return response()->json(["skin" => $skin]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $skin = Skin::find($id);
            $skin->delete();
            return response()->json(["message" => "Le skin a bien été supprimé"]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
