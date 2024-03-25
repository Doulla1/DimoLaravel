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
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $skins = Skin::all();
            return response()->json($skins);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new skin
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $skin = new Skin();
            $skin->hair = $request->name;
            $skin->save();
            return response()->json($skin);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single skin
     *
     * @return JsonResponse
     */
    public function get($id)
    {
        try {
            $skin = Skin::find($id);
            return response()->json($skin);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $skin = Skin::find($id);
            $skin->name = $request->name;
            $skin->save();
            return response()->json($skin);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
