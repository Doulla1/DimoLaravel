<?php

namespace App\Http\Controllers;

use App\Mail\RegisterToProgram;
use App\Models\Program;
use App\Models\Subject;
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
     * @response array{programs: program[]}
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
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
     * @response array{program: program}
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
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
                $program->illustration = env('APP_URL', "https://api.dimovr.com").'/storage/'.$fileName;
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
     * @response array{students: User[]}
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
     * @response array{programs: program[]}
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

            $user = User::with('teachedSubjects', 'attendedPrograms')->find($user->id);

            // Récupérer les programmes auxquels l'étudiant est inscrit
            $programs = $user->attendedPrograms;
            // Charger les matières de chaque programme
            //$programs->load('subjects');
            return response()->json(["programs"=>$programs]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Register connected student to a program
     *
     * @param Request $request
     * @response array{program: program}
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
     * @response array{programs: program[]}
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
     * @response array{programs: program[]}
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
     * Get a program by its id
     *
     * @param int $id
     * @response array{program: Program, subjects: Subject[]}
     * @return JsonResponse
     */
    public function getById(int $id): JsonResponse
    {
        try {
            $program = Program::find($id);
            if ($program) {
                $program->load('subjects');
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
     * Get all subjects of a program
     *
     * @param int $id
     * @return JsonResponse
     * @response array{subjects: Subject[]}
     */
    public function getSubjectsByProgram(int $id): JsonResponse
    {
        try {
            $subjects = Subject::where('program_id', $id)->get();
            return response()->json(["subjects"=>$subjects], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while getting subjects : ".$e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing program
     *
     * @param Request $request
     * @param int $id
     * @response array{program: program}
     * @return JsonResponse
     */
    public function update(Request $request, $id)
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
            $this->validate($request, [
                'name' => 'string|required',
                'description' => 'string|required',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);

            // Récupérer le programme à mettre à jour
            $program = Program::findOrFail($id);

            // Vérifier si le professeur est le responsable du programme
            if ($program->head_teacher_id !== $headTeacher->id) {
                return response()->json([
                    "message" => "You are not allowed to update this program"
                ], 403);
            }

            // Mettre à jour les attributs du programme avec les nouvelles valeurs
            $program->name = $request->name;
            $program->description = $request->description;
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
                $program->illustration = env('APP_URL', "https://api.dimovr.com").'/storage/'.$fileName;
            }
            else {
                return response()->json([
                    "message" => "Illustration is required"
                ], 400);
            }

            // Sauvegarder les modifications apportées au programme
            $program->save();

            return response()->json(["program" => $program], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while updating program : " . $e->getMessage()
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
