<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{

    /**
     * Get all subjects
     *
     * @response array{subjects: \App\Models\Subject[]}
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $subjects = Subject::all();
            return response()->json(["subjects"=>$subjects], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while fetching subjects : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a subject
     *
     * @param $id
     * @response array{subject: Subject}
     * @return JsonResponse
     */
    public function getById($id): JsonResponse
    {
        try {
            $subject = Subject::find($id);
            if (!$subject) {
                return response()->json([
                    "message" => "Subject not found"
                ], 404);
            }
            return response()->json(["subject"=>$subject], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while fetching subject : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new subject
     *
     * @param Request $request
     * @response array{subject: Subject}
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
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
                $subject->illustration = env('APP_URL', "https://api.dimovr.com").'/storage/'.$fileName;
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
     * @param Request $request
     * @param $id
     * @response array{subject: Subject}
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
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
                $subject->illustration = env('APP_URL', "https://api.dimovr.com").'/storage/'.$fileName;
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
     * @param Request $request
     * @response array{subject: Subject}
     * @return JsonResponse
     */
    public function joinSubject(Request $request): JsonResponse
    {
        try {
            // Vérification de l'existence du professeur
            $teacher = Auth::user ();
            $teacher = User::find($teacher->id);
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
     * Leave a subject as a teacher
     *
     * @param Request $request
     * @response array{subject: Subject}
     * @return JsonResponse
     */
    public function leaveSubject(Request $request): JsonResponse
    {
        try {
            // Vérification de l'existence du professeur
            $teacher = Auth::user ();
            $teacher = User::find($teacher->id);
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
            // Supprimer le lien dans la table teachers
            $teacher->teachedSubjects()->detach($subject->id);
            return response()->json(["subject"=>$subject], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while leaving subject : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a subject
     *
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
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
