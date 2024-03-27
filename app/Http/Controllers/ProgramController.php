<?php

namespace App\Http\Controllers;

use App\Mail\RegisterToProgram;
use App\Models\program;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Get all programs
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $programs = program::all();
            $programs->load('subjects');
            return response()->json(["programs"=>$programs], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while getting programs"
            ], 500);
        }
    }

    /**
     * Create a new program
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Vérification de l'existence du professeur
            $headTeacher = Auth::user ();
            if (!$headTeacher || !$headTeacher->hasRole('teacher')) {
                return response()->json([
                    "message" => "You are not a teacher"
                ], 404);
            }

            // Validation des données
            $this->validate($request,[
                'name' => 'string|required',
                'description' => 'string|required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);
            $program = new program();
            $program->name = $request->name;
            $program->description = $request->description;
            $program->illustration = $request->illustration;
            $program->start_date = $request->start_date;
            $program->end_date = $request->end_date;
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
                $program->illustration = $fileName;
            }
            else {
                return response()->json([
                    "message" => "Illustration is required"
                ], 400);
            }
            $program->head_teacher_id = $headTeacher->id;
            $program->save();
            return response()->json(["program"=>$program], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while creating program : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all students of a program
     *
     * @param int $id Program id
     * @return JsonResponse
     */
    public function getStudents(int $id): JsonResponse
    {
        try {
            $program = program::find($id);
            if ($program) {
                $students = $program->students;
                return response()->json(["students"=>$students], 200);
            } else {
                return response()->json([
                    "message" => "program not found"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting students"
            ], 500);
        }
    }

    /**
     * Get all programs of connected students.
     *
     * @return JsonResponse
     */
    public function getByConnectedStudent(): JsonResponse
    {
        try{
            //Vérifier si l'utilisateur connecté est un étudiant
            $user = Auth::user();
            if (!$user->hasRole('student')) {
                return response()->json(['message' => 'You are not a student'], 401);
            }
            $user = User::find($user->id);
            // Récupérer les programmes auxquels l'étudiant est inscrit
            $programs = $user->attendedPrograms();
            // Charger les matières de chaque programme
            $programs->load('subjects');
            return response()->json(["programs"=>$programs]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Register connected student to a program
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerStudent(Request $request)
    {
        try {
            $student = Auth::user();
            // Validation des données
            $this->validate($request,[
                'program_id' => 'integer|required'
            ]);
            $program = program::find($request->program_id);
            if ($program) {
                $program->students()->attach($student->id);
                // Envoyer un email de confirmation à l'étudiant
                Mail::to ($student->email)->send (new RegisterToProgram($student,$program));

                return response()->json(["program"=>$program], 200);
            } else {
                return response()->json([
                    "message" => "program not found"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while registering student : " . $e->getMessage()
            ], 500);
        }
    }




    /**
     * Get all programs where the head-teacher is the connected user
     *
     * @return JsonResponse
     */
    public function getByConnectedHeadTeacher()
    {
        try {
            $teacherId = auth()->user()->id;
            $programs = program::where('head_teacher_id', $teacherId)->get();
            return response()->json(["programs"=>$programs], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting programs"
            ], 500);
        }
    }

    /**
     * Get all programs teached by connected teacher
     *
     * @return JsonResponse
     */
    public function getByConnectedTeacher()
    {
        try {
            $teacher = Auth::user();
            $programs = $teacher->teachedprograms;
            return response()->json(["programs"=>$programs], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting programs"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function getById(int $id)
    {
        try {
            $program = program::find($id);
            if ($program) {
                return response()->json(["program"=>$program], 200);
            } else {
                return response()->json([
                    "message" => "program not found"
                ], 404);
            }
        }
        catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting program"
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Validation des données
            $this->validate($request,[
                'name' => 'required',
                'head_teacher_id' => 'required'
            ]);

            // Mise à jour de l'objet
            $program = program::find($id);
            if ($program) {
                $program->name = $request->name;
                $program->save();
                return response()->json(["program"=>$program], 200);
            } else {
                return response()->json([
                    "message" => "program not found"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while updating program"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $program = program::find($id);
            if ($program) {
                $program->delete();
                return response()->json();
            } else {
                return response()->json([
                    "message" => "program not found"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while deleting program"
            ], 500);
        }
    }
}
