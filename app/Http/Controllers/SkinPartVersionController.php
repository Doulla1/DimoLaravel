<?php

namespace App\Http\Controllers;

use App\Models\SkinPartVersion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SkinPartVersionController extends Controller
{

    /**
     * Get all SkinPartVersions
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $skinPartVersions = SkinPartVersion::all();
            return response()->json(["skinPartVersions"=>$skinPartVersions], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Create a new SkinPartVersion
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
        $validator = Validator::make($request->all(), [
            'skin_part_id' => 'required|integer',
            'name' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $skinPartVersion = new SkinPartVersion();

        // Si image est présent, on vérifie que c'est bien une image avec les bons formats et on la stocke
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $image = $request->file('image');
            // On stocke l'image dans le dossier uploads
            $fileName = $image->store ('uploads', 'public');
            // On stocke le nom du fichier dans la base de données
            $skinPartVersion->image = $fileName;
        }

        $skinPartVersion->skin_part_id = $request->skin_part_id;
        $skinPartVersion->name = $request->name;
        $skinPartVersion->save();
        return response()->json( ["skinPartVersion"=> $skinPartVersion] , 201);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Get SkinPartVersion by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getById(int $id): JsonResponse
    {
        try {
            $skinPartVersion = SkinPartVersion::find($id);
            return response()->json(["skinPartVersion"=>$skinPartVersion], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update SkinPartVersion by ID
     *
     * @param  Request  $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {   try {
        $validator = Validator::make($request->all(), [
            'skin_part_id' => 'required|integer',
            'name' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $skinPartVersion = SkinPartVersion::find($id);

        // Si image est présent, on vérifie que c'est bien une image avec les bons formats, on supprime l'ancienne et on la stocke
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            // On supprime l'ancienne image
            if ($skinPartVersion->image) {
                unlink(storage_path('app/public/'.$skinPartVersion->image));
            }
            $image = $request->file('image');
            // On stocke l'image dans le dossier uploads
            $fileName = $image->store ('uploads', 'public');
            // On stocke le nom du fichier dans la base de données
            $skinPartVersion->image = $fileName;
        }

        $skinPartVersion->skin_part_id = $request->skin_part_id;
        $skinPartVersion->name = $request->name;
        $skinPartVersion->save();
        return response()->json(["skinPartVersion"=>$skinPartVersion], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Delete SkinPartVersion by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $skinPartVersion = SkinPartVersion::find($id);
            $skinPartVersion->delete();
            return response()->json(["message"=>"La version du skin a bien été supprimée"], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
