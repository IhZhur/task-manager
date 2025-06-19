<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        return Task::orderBy('position')->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
        {
            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $task = Task::create([
                'title' => $request->title,
                'completed' => false,
            ]);

            return response()->json($task, 201);
        }

    /**
     * Display the specified resource.
     */
      public function show(Task $task)
    {
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);

        $task->update($request->only('title', 'completed'));

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Задача удалена']);
    }

    public function sort(Request $request): JsonResponse
    {
        $positions = $request->input('positions', []);
        foreach ($positions as $index => $id) {
            Task::where('id', $id)->update(['position' => $index]);
        }
        return response()->json(['message' => 'Task positions updated']);
    }


}
