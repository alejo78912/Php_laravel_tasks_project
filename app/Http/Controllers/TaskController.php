<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $req)
    {
        $user = auth('api')->user();
        $tasks = Task::where('user_id', $user->id)
                     ->orderBy('created_at','desc')
                     ->paginate(10);

        return response()->json(['success' => true, 'data' => $tasks]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(['pending','in_progress','completed'])],
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $data['user_id'] = auth('api')->id();
        $task = Task::create($data);

        return response()->json(['success' => true, 'data' => $task], 201);
    }

    protected function findOwnTask($id)
    {
        $task = Task::findOrFail($id);
        if ($task->user_id !== auth('api')->id()) abort(403, 'Forbidden');
        return $task;
    }

    public function show($id)
    {
        $task = $this->findOwnTask($id);
        return response()->json(['success' => true, 'data' => $task]);
    }

    public function update(Request $req, $id)
    {
        $task = $this->findOwnTask($id);

        $data = $req->validate([
            'title' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(['pending','in_progress','completed'])],
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        $task->update($data);
        return response()->json(['success' => true, 'data' => $task]);
    }

    public function destroy($id)
    {
        $task = $this->findOwnTask($id);
        $task->delete();
        return response()->json(['success' => true, 'data' => null]);
    }

    public function weather($id)
    {
        $task = $this->findOwnTask($id);

        if (! $task->due_date) {
            return response()->json(['success' => false, 'message' => 'Task has no due_date'], 400);
        }

        $key = env('WEATHER_API_KEY');

        if (! $key) {
            return response()->json(['success' => false, 'message' => 'Weather API key not configured'], 500);
        }

        $lat = '4.8133';
        $lon = '-75.6961';

        $resp = Http::get('https://api.openweathermap.org/data/2.5/forecast', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $key,
            'units' => 'metric',
        ]);

        if ($resp->failed()) {
            return response()->json(['success' => false, 'message' => 'Weather API error'], 502);
        }

        $forecast = $resp->json();

        // Try to match forecast items to the task due_date (if inside next 5 days)
        $due = $task->due_date;
        $matched = [];
        foreach ($forecast['list'] as $item) {
            if (strpos($item['dt_txt'], $due) === 0) {
                $matched[] = $item;
            }
        }

        return response()->json(['success' => true, 'data' => [
            'task' => $task,
            'weather' => [
                'matched_forecast' => $matched,
                'raw' => $forecast
            ]
        ]]);
    }
}
