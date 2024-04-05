<?php

namespace App\Http\Controllers;

use App\Models\Skin2;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class Skin2Controller extends Controller
{
    /**
     * Get skin of connected user
     *
     * @response array{skin: Skin2}
     * @return JsonResponse
     */
    public function getByUser(): JsonResponse
    {
        try{
            $userId = Auth::user ()->id;
            $skin = Skin2::where('user_id', $userId)->first();
            return response()->json(["skin"=>$skin]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

    /**
     * Update the connected user's skin
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'hair_version' => 'required|integer',
                'hair_color' => 'required|string',
                'upper_body_color' => 'required|string',
                'lower_body_color' => 'required|string',
                'skin_color' => 'required|string'
            ]);
            $userId = Auth::user ()->id;

            $skin = Skin2::where('user_id', $userId)->first();
            $skin->hair_version = $request->hair_version;
            $skin->hair_color = $request->hair_color;
            $skin->upper_body_color = $request->upper_body_color;
            $skin->lower_body_color = $request->lower_body_color;
            $skin->skin_color = $request->skin_color;
            $skin->save();

            return response()->json(["skin"=>$skin]);
        } catch (Exception $e) {
            return response ()->json ($e->getMessage (), 500);
        }
    }

}
