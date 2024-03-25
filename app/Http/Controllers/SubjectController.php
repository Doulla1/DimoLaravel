<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Create a new subject
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Vérification de l'existence du professeur
            $teacher = Auth::user ();
            if (!$teacher || !$teacher->hasRole('teacher')) {
                return response()->json([
                    "message" => "You are not a teacher"
                ], 404);
            }

            // Validation des données
            $this->validate($request,[
                'title' => 'string|required',
                'description' => 'string|required',
                'illustration' => 'required',
                'program_id' => 'required',
            ]);
            $subject = new Subject();
            $subject->title = $request->title;
            $subject->description = $request->description;
            $subject->illustration = $request->illustration;
            $subject->program_id = $request->program_id;

            // Récupérer l'image et la sauvegarder
            if ($request->hasFile('illustration')) {
                //Vérifier si le fichier est une image
                $validator = Validator::make($request->all(), [
                    'illustration' => 'mimes:jpeg,png,jpg,gif,svg',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "message" => "Illustration must be an image"
                    ], 400);
                }
                $file = $request->file('illustration');
                $fileName = $file->store ('uploads', 'public');
                $subject->illustration = $fileName;
            }
            else {
                return response()->json([
                    "message" => "Illustration is required"
                ], 400);
            }
            $subject->save();

            // Créer le lien dans la table teachers
            $teacher->teachedSubjects()->attach($subject->id);
            return response()->json(["subject"=>$subject], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while creating subject : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a subject
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Vérification de l'existence du professeur
            $teacher = Auth::user ();
            if (!$teacher || !$teacher->hasRole('teacher')) {
                return response()->json([
                    "message" => "You are not a teacher"
                ], 404);
            }

            // Validation des données
            $this->validate($request,[
                'title' => 'string|required',
                'description' => 'string|required',
                'program_id' => 'required',
            ]);
            $subject = Subject::find($id);
            if (!$subject) {
                return response()->json([
                    "message" => "Subject not found"
                ], 404);
            }
            $subject->title = $request->title;
            $subject->description = $request->description;
            $subject->illustration = $request->illustration;
            $subject->program_id = $request->program_id;

            // Récupérer l'image et la sauvegarder
            if ($request->hasFile('illustration')) {
                //Vérifier si le fichier est une image
                $validator = Validator::make($request->all(), [
                    'illustration' => 'mimes:jpeg,png,jpg,gif,svg',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "message" => "Illustration must be an image"
                    ], 400);
                }
                //Supprimer l'ancienne image
                $oldIllustration = $subject->illustration;
                if ($oldIllustration) {
                    unlink(storage_path('app/public/'.$oldIllustration));
                }
                $file = $request->file('illustration');
                $fileName = $file->store ('uploads', 'public');
                $subject->illustration = $fileName;
            }
            else {
                return response()->json([
                    "message" => "Illustration is required"
                ], 400);
            }
            $subject->save();
            return response()->json(["subject"=>$subject], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while updating subject : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Join a subject as a teacher
     *
     * @return JsonResponse
     */
    public function joinSubject(Request $request)
    {
        try {
            // Vérification de l'existence du professeur
            $teacher = Auth::user ();
            if (!$teacher || !$teacher->hasRole('teacher')) {
                return response()->json([
                    "message" => "You are not a teacher"
                ], 404);
            }

            // Validation des données
            $this->validate($request,[
                'subject_id' => 'integer|required',
            ]);
            $subject = Subject::find($request->subject_id);
            if (!$subject) {
                return response()->json([
                    "message" => "Subject not found"
                ], 404);
            }
            // Créer le lien dans la table teachers
            $teacher->teachedSubjects()->attach($subject->id);
            return response()->json(["subject"=>$subject], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while joining subject : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a subject
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        try {
            $subject = Subject::find($id);
            if (!$subject) {
                return response()->json([
                    "message" => "Subject not found"
                ], 404);
            }
            $subject->delete();
            return response()->json(["message"=>"Subject deleted"], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while deleting subject : ".$e->getMessage()
            ], 500);
        }
    }
}
