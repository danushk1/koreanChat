<?php

namespace App\Http\Controllers;

use App\Models\project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
  public function create()
    {
        return view('firstquaryproject');
    }

    public function store(Request $request)
    {
$excel = Excel::toArray([], $request->file('database_file'));
    $sheet = $excel[0];

    $schema = [];

    foreach ($sheet as $row) {
        if (count($row) >= 2) {
            $table = trim($row[0]);
            $column = trim($row[1]);
            $schema[$table][] = $column;
        }
    }
//dd(Auth::id());
  $id = DB::table('project_schemas')->insertGetId([
    'user_id' => Auth::id(),
    'project_name' => $request->project_name,
    'schema_json' => json_encode($schema),
    'created_at' => now(),
    'updated_at' => now()
]);

return redirect()->route('projects.canvas');
}

public function canvas()
{
    $projects = DB::table('project_schemas')
        ->where('user_id', Auth::id())
->get();

    if (!$projects) {
        abort(404);
    }

    $currentProject = $projects->first();

    return view('project_canvas', [
        'projects' => $projects,
        'currentProject' => $currentProject
    ]);
}



    public function generateQuery(Request $request, $projectName)
{
    $user_id = Auth::id();

    $request->validate(['prompt' => 'required|string']);
    $prompt = $request->prompt;

    // Get project by user and name
    $project = DB::select("SELECT * FROM project_schemas WHERE user_id = ? AND project_name = ?", [$user_id, $projectName]);

    if (!$project || count($project) === 0) {
        return back()->with('error', 'Project not found.');
    }

    $project = $project[0];

    $schema = json_decode($project->schema_json, true);

    if (!$schema || !is_array($schema)) {
        return back()->with('error', 'Invalid project schema.');
    }

    // Filter schema based on prompt keywords (table name matching)
    $matchedSchema = '';
    foreach ($schema as $table => $columns) {
        if (stripos($prompt, $table) !== false) {
            $matchedSchema .= "Table: $table → " . implode(', ', $columns) . "\n";
        }
    }

    // Fallback to top 5 tables if nothing matched
    if (empty($matchedSchema)) {
        $sliced = array_slice($schema, 0, 5);
        foreach ($sliced as $table => $columns) {
            $matchedSchema .= "Table: $table → " . implode(', ', $columns) . "\n";
        }
    }

    // Final prompt to send to OpenAI
    $finalPrompt = "You are a SQL expert. Below is a database schema followed by a user request. Generate a clean and correct SQL query based on the request.\n\n";
    $finalPrompt .= "Database Schema:\n$matchedSchema\n\n";
    $finalPrompt .= "User Request: $prompt";

    // Call OpenAI API
    $response = Http::withToken(env('OPENAI_API_KEY'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a SQL expert.'],
                ['role' => 'user', 'content' => $finalPrompt],
            ],
        ]);

    // Handle error if response fails
    if (!$response->successful()) {
        return back()->with('error', 'OpenAI API error: ' . $response->status());
    }

    // Extract the generated SQL
    $sql = $response['choices'][0]['message']['content'] ?? 'Error generating SQL.';

    return back()->with('generated_sql', $sql);
}
}