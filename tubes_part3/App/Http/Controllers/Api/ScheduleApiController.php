<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); 
    }

    public function index()
    {
        $schedules = Schedule::where('user1_id', Auth::id())
            ->orWhere('user2_id', Auth::id())
            ->orderBy('scheduled_at', 'desc')
            ->with(['user1', 'user2'])
            ->get();

        return response()->json([
            'message' => 'Schedules fetched successfully.',
            'data' => $schedules
        ], 200);
    }

    public function store(Request $request)
    {
        Log::info('Current authenticated user ID', ['user_id' => Auth::id()]);

        try {
            $request->validate([
                'user2_id' => 'required|exists:users,id|different:' . Auth::id(),
                'scheduled_at' => 'required|date|after:now',
                'method' => 'required|in:online,offline',
                'notes' => 'nullable|string',
            ]);

            $schedule = Schedule::create([
                'user1_id' => Auth::id(),
                'user2_id' => $request->user2_id,
                'scheduled_at' => $request->scheduled_at,
                'method' => $request->method,
                'notes' => $request->notes,
                'status' => 'upcoming',
            ]);

            return response()->json([
                'message' => 'Schedule created successfully!',
                'data' => $schedule
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API Schedule Store Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            
            return response()->json(['message' => 'Failed to create schedule.'], 500);
        }
    }

    public function show($id)
    {
        \Log::info('User:', [auth()->user()]);
        return Schedule::findOrFail($id);

        try {
            $schedule = Schedule::where(function ($query) {
                    $query->where('user1_id', Auth::id())
                          ->orWhere('user2_id', Auth::id());
                })
                ->find($id);

            if (!$schedule) {
                return response()->json(['message' => 'Schedule not found.'], 404);
            }

            return response()->json([
                'message' => 'Schedule fetched successfully.',
                'data' => $schedule
            ], 200);
        } catch (\Exception $e) {
            Log::error('API Schedule Show Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'schedule_id' => $id,
            ]);
            return response()->json(['message' => 'Failed to fetch schedule.'], 500);
        }
    }

    // public function show($id)
    // {
    //     $schedule = Schedule::findOrFail($id);
    //     return response()->json($schedule);
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $schedule = Schedule::where(function ($query) {
    //                 $query->where('user1_id', Auth::id())
    //                       ->orWhere('user2_id', Auth::id());
    //             })
    //             ->find($id);

    //         if (!$schedule) {
    //             return response()->json(['message' => 'Schedule not found.'], 404);
    //         }

    //         $request->validate([
    //             'scheduled_at' => 'required|date|after:now',
    //             'method' => 'required|in:online,offline',
    //             'notes' => 'nullable|string',
    //             'status' => 'required|in:upcoming,completed,cancelled',
    //         ]);

    //         $schedule->update([
    //             'scheduled_at' => $request->scheduled_at,
    //             'method' => $request->method,
    //             'notes' => $request->notes,
    //             'status' => $request->status,
    //         ]);

    //         return response()->json([
    //             'message' => 'Schedule updated successfully!',
    //             'data' => $schedule
    //         ], 200);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation failed.',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         Log::error('API Schedule Update Error: ' . $e->getMessage(), [
    //             'user_id' => Auth::id(),
    //             'schedule_id' => $id,
    //         ]);
    //         return response()->json(['message' => 'Failed to update schedule.'], 500);
    //     }
    // }

    // public function destroy($id)
    // {
    //     try {
    //         $schedule = Schedule::where(function ($query) {
    //                 $query->where('user1_id', Auth::id())
    //                       ->orWhere('user2_id', Auth::id());
    //             })
    //             ->find($id);

    //         if (!$schedule) {
    //             return response()->json(['message' => 'Schedule not found.'], 404);
    //         }

    //         $schedule->delete();

    //         return response()->json(['message' => 'Schedule deleted successfully.'], 200);
    //     } catch (\Exception $e) {
    //         Log::error('API Schedule Delete Error: ' . $e->getMessage(), [
    //             'user_id' => Auth::id(),
    //             'schedule_id' => $id,
    //         ]);
    //         return response()->json(['message' => 'Failed to delete schedule.'], 500);
    //     }
    // }
}