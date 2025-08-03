@extends('layouts.main')

@section('content')
<div class="flex flex-col lg:flex-row h-screen bg-gray-50">

    {{-- Sidebar: Show user's projects --}}
    <div class="w-full lg:w-1/4 bg-white border-r border-gray-200 p-6 shadow-sm">
        <h5 class="text-lg font-semibold text-gray-800 mb-4">Your Projects</h5>
        <select name="project_id" class="w-full p-2 border rounded-lg text-sm" id="projectSelect">
            @foreach($projects as $p)
                <option value="{{ $p->project_name }}" {{ $p->id == $currentProject->id ? 'selected' : '' }}>
                    {{ $p->project_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Canvas Area --}}
    <div class="w-full lg:w-2/4 p-8">
        <form id="queryForm" method="POST" action="{{ route('projects.generateQuery', $currentProject->id) }}">
            @csrf
            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700 mb-1">Describe what you want</label>
                <textarea name="prompt" rows="3" class="w-full border p-3 rounded-lg focus:outline-none focus:ring focus:ring-blue-300" required placeholder="e.g., Show all item names and codes..."></textarea>
            </div>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 shadow">
                Generate SQL
            </button>
        </form>

        @if(session('generated_sql'))
        <hr class="my-6">
        <h5 class="text-md font-semibold mb-2">Generated SQL:</h5>
        <pre class="bg-gray-100 text-sm p-4 rounded-lg border border-gray-300 text-blue-800 whitespace-pre-wrap">
{{ session('generated_sql') }}
        </pre>
        @endif
    </div>

    {{-- Explanation Area --}}
   <div class="hidden lg:block w-full lg:w-1/4 p-6 bg-white border-l border-gray-200 shadow-sm">
    <h4 class="text-lg font-semibold text-gray-800 mb-4">How to write a good query</h4>

    <ul class="list-disc pl-5 text-sm text-gray-700 space-y-3">
        <li>
            For example: <span class="text-blue-600 font-medium">"Show all item names and codes"</span><br>
            If your query involves joins, make sure to mention the correct table names.
        </li>
       <li class="text-sm text-red-600 font-semibold">
            To improve accuracy, always use real table  from your uploaded database file.
        </li>
        <li>
            You can describe your query in simple English or Singlish.
        </li>
       <li class="text-sm text-gray-700">
            By using accurate table names, you can obtain the best results for your query.
</li>


    </ul>
</div>

</div>

<script>
    document.getElementById('queryForm').addEventListener('submit', function (e) {
        const selectedProjectId = document.getElementById('projectSelect').value;

        const routeTemplate = @json(route('projects.generateQuery', ['id' => '__ID__']));
        this.action = routeTemplate.replace('__ID__', selectedProjectId);
    });
</script>
@endsection
