@extends('layouts.main')

@section('content')
<div class="flex flex-col lg:flex-row justify-center items-start gap-8 px-6 py-12">

    {{-- Form Card --}}
    <div class="w-full lg:w-1/2 bg-white p-8 rounded-2xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Create New Project</h2>
       @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
                <input type="text" name="project_name" id="project_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label for="database_file" class="block text-sm font-medium text-gray-700 mb-1">Upload Database File (Excel)</label>
                <input type="file" name="database_file" id="database_file" class="w-full text-sm text-gray-700 bg-white border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
            </div>

            <div class="text-center">
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Create Project
                </button>
            </div>
        </form>
    </div>

    {{-- Explanation Card --}}
    <div class="w-full lg:w-1/2 bg-slate-50 p-6 rounded-2xl shadow-md border border-blue-100">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">What Happens When You Create a Project?</h3>

        <ol class="list-decimal pl-5 text-gray-700 space-y-3 text-sm">
            <li>
                The system <strong>saves your project name</strong> and reads the uploaded Excel file.
            </li>
            <li>
                It <strong>extracts the table and column structure</strong> from the uploaded file.
            </li>
            <li>
                Internally, it uses the following SQL query to understand your database structure:
                <pre class="bg-white p-3 border border-gray-300 rounded-md text-sm mt-2 overflow-x-auto"><code class="text-blue-700">
SELECT 
    table_name,
    GROUP_CONCAT(column_name ORDER BY ordinal_position SEPARATOR ',') AS columns
FROM information_schema.columns
WHERE table_schema = 'YourDatabaseName'
GROUP BY table_name;
                </code></pre>
                This helps AI understand how to generate accurate SQL queries for your data.
            </li>
            <li>
                Your schema is saved securely in the system and used later when you ask questions like:
                <span class="italic text-gray-600">“Show item name and quantity from item history”</span>
            </li>
            <li>
                Then, AI generates the appropriate SQL based on your prompt and the uploaded schema.
            </li>
        </ol>
    </div>
</div>
@endsection
