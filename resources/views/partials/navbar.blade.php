<nav class="bg-gray-500 shadow mb-4">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-xl font-bold">Home</a>
        
 <a href="{{ route('home') }}" class="text-gray-100">Korean Chat</a>
<a href="{{ route('projects.create') }}" class="text-gray-100">Project</a>
@if(auth()->check())
    <a href="{{ route('projects.canvas') }}" class="text-gray-100">generate query</a>
@else
    <a href="{{ route('login') }}" class="text-gray-100">generate query</a>
@endif


<div>
            @auth
                <span class="text-white mr-2">Hi, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:underline">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white mr-4 hover:underline">Login</a>
                <a href="{{ route('register') }}" class="text-white hover:underline">Register</a>
            @endauth
        </div>
 
    </div>
</nav>