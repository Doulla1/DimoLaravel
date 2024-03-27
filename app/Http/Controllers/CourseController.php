<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Get all courses
     *
     * @return JsonResponse
     */
    public function getAll()
    {
        try {
            $courses = Course::all();
            return response()->json(["cours"=>$courses], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "An error occurred while getting courses"
            ], 500);
        }
    }

    /**
     * Get a course by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getById(int $id)
    {
        try {
            $course = Course::find($id);
            if ($course) {
                return response()->json(["cours"=>$course], 200);
            } else {
                return response()->json([
                    "message" => "Course not found"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting course"
            ], 500);
        }
    }

    /**
     * Get courses by teacher id
     *
     * @param int $teacherId
     * @return JsonResponse
     */
    public function getByTeacherId(int $teacherId)
    {
        try {
            $courses = Course::where('teacher_id', $teacherId)->get();
            return response()->json(["cours"=>$courses], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting courses"
            ], 500);
        }
    }

    /**
     * Get all courses by connected teacher
     *
     * @return JsonResponse
     */
    public function getByConnectedTeacher()
    {
        try {
            $teacherId = auth()->user()->id;
            $courses = Course::where('teacher_id', $teacherId)->get();
            return response()->json(["cours"=>$courses], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting courses"
            ], 500);
        }
    }

    /**
     * Get all courses of current student
     *
     * @return JsonResponse
     */
    public function getByConnectedStudent()
    {
        try {
            $studentId = auth()->user()->id;
            // Trouver tous les cours qui font partie des matières des programmes auxquels l'étudiant est inscrit
            $courses = Course::whereHas('subject.programs.students', function ($query) use ($studentId) {
                $query->where('students.id', $studentId);
            })->get();
            return response()->json(["cours"=>$courses], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while getting courses"
            ], 500);
        }
    }



    /**
     * Create a course
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Vérification que l'utilisateur à le role teacher
            if (!auth()->user()->hasRole('teacher')) {
                return response()->json([
                    "message" => "You are not a teacher"
                ], 404);
            }
            // Data validation
            $request->validate([
                'subject_id' => 'integer|required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);
            // Create course
            $course = new Course;
            $course->teacher_id = auth()->user()->id;
            $course->subject_id = $request->subject_id;
            $course->start_date = $request->start_date;
            $course->end_date = $request->end_date;
            $course->save();

            return response()->json([
                "course" => $course
            ]);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while creating course"
            ], 500);
        }
    }

    /**
     * Update a course
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        try {
            // Data validation
            $request->validate([
                'teacher_id' => 'integer',
                'subject_id' => 'integer',
                'start_date' => 'date',
                'end_date' => 'date',
            ]);
            $course = Course::find($id);
            if ($course &&
                ($course->teacher_id == auth()->user()->id || auth()->user()->hasRole('admin'))) {
                $course->update($request->all());
                return response()->json([
                    "course" => $course
                ]);
            } else {
                return response()->json([
                    "message" => "Course not found or you are not authorized to update it"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while updating course"
            ], 500);
        }
    }

    /**
     * Start à course
     *
     * Seulement si il y'a une salle de classe disponible (seulement 5 salles de classe disponibles)
     *
     * @param int $course_id
     * @return JsonResponse
     */
    public function start(int $course_id): JsonResponse
    {
        try {
            // Vérifier s'il n'y a pas déjà 5 cours actifs
            $activeCourses = Course::where('is_active', true)->count();
            if ($activeCourses == 5) {
                return response()->json([
                    "message" => "There are already 5 active courses. Wait for a course to end before starting a new one"
                ], 400);
            }

            $course = Course::find($course_id);
            if ($course &&
                ($course->teacher_id == auth()->user()->id || auth()->user()->hasRole('teacher'))) {
                $course->start_date = now();
                $course->is_active = true;
                $course->save();
                return response()->json([
                    "course" => $course
                ]);
            } else {
                return response()->json([
                    "message" => "Course not found or you are not authorized to start it"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while starting course"
            ], 500);
        }
    }

    /**
     * End a course
     *
     * @param int $course_id
     * @return JsonResponse
     */
    public function end(int $course_id): JsonResponse
    {
        try {
            $course = Course::find($course_id);
            if ($course &&
                ($course->teacher_id == auth()->user()->id || auth()->user()->hasRole('teacher'))) {
                $course->end_date = now();
                $course->is_active = false;
                $course->save();
                return response()->json([
                    "course" => $course
                ]);
            } else {
                return response()->json([
                    "message" => "Course not found or you are not authorized to end it"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while ending course"
            ], 500);
        }
    }

    /**
     * Delete a course
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $course = Course::find($id);
            if ($course &&
                ($course->teacher_id == auth()->user()->id || auth()->user()->hasRole('admin'))) {
                $course->delete();
                return response()->json();
            } else {
                return response()->json([
                    "message" => "Course not found or you are not authorized to delete it"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while deleting course"
            ], 500);
        }
    }
}
