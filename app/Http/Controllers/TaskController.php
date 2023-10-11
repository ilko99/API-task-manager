<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\TaskCollection;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }       

    public function index()
    {
        $tasks = QueryBuilder::for(Task::class)
        ->allowedFilters([
            'is_done',
            'scheduled_at',
            'due_at',
            AllowedFilter::scope('scheduledBetween'),
            AllowedFilter::scope('dueBetween'),
            AllowedFilter::scope('due', null, 'past_today')
            ])
        ->defaultSort('created_at')
        ->allowedSorts(['title', 'is_done', 'created_at'])
        ->paginate();
        return new TaskCollection($tasks);
    }

    public function show(Request $request,Task $task)
    {
        return new TaskResource($task);
    }

    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        $task = Auth::user()->tasks()->create($validated); // Create a task with asigning a user id to it
         // Task::create($validated); 
        
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        $task->update($validated);

        return new TaskResource($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
