@extends('layouts.main')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black py-16 px-4">
    <h1 class="text-4xl font-extrabold mb-12 text-center text-white tracking-wide">Subscription Plans</h1>

    <div class="max-w-6xl mx-auto grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($plans as $plan)
        <div class="relative rounded-3xl border border-white/10 bg-gradient-to-br from-gray-800 via-gray-700 to-gray-900 shadow-xl transform hover:scale-105 transition-transform duration-500 cursor-pointer">

            <div class="p-8 flex flex-col h-full justify-between">

                <div>
                    <h2 class="text-3xl font-extrabold mb-4 tracking-tight text-white">{{ $plan['name'] }}</h2>
                    <p class="text-gray-300 mb-6 leading-relaxed">{{ $plan['details'] }}</p>
                </div>

                <div class="flex items-center justify-between mt-auto">
                    <span class="text-4xl font-extrabold tracking-wider text-white">{{ $plan['price'] }}</span>

                  <a href=" https://docs.google.com/forms/d/e/1FAIpQLSfbRUfuIYHNEeFDEW8rUICBmC4Ftx1r19sHYmH1_g6jYIzUyg/viewform?usp=header">
                        <button
                            class="bg-white text-gray-900 font-semibold py-2 px-6 rounded-full shadow hover:bg-gray-100 transition">
                            Choose Plan
                        </button>
                        </a>
                  
                </div>
            </div>

            <!-- Subtle glow effects -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 rounded-full bg-pink-500 opacity-20 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 rounded-full bg-indigo-500 opacity-10 blur-3xl pointer-events-none"></div>
        </div>
        @endforeach
    </div>
</div>

@endsection
