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
     * Create a course
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Data validation
            $request->validate([
                'teacher_id' => 'required',
                'classroom_id' => 'required',
            ]);
            // Check if the connected user is a teacher
            if(!auth()->user()->hasRole('admin')){
                $request->validate([
                    'teacher_id' => 'required|in:'.auth()->user()->id
                ]);
            }
            $course = Course::create($request->all());

            return response()->json([
                "data" => $course
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
                'teacher_id' => 'required',
                'classroom_id' => 'required',
            ]);
            $course = Course::find($id);
            if ($course &&
                ($course->teacher_id == auth()->user()->id || auth()->user()->hasRole('admin'))) {
                $course->update($request->all());
                return response()->json([
                    "data" => $course
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
     * Delete a course
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $course = Course::find($id);
            if ($course&&
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
