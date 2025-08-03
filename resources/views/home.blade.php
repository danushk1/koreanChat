@extends('layouts.main')

@section('content')
<style>
    .chat-box {
        height: 50vh;
        overflow-y: auto;
        background-color: #e5ddd5;
        border-bottom: 1px solid #ccc;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 8px;
    }
</style>

<div class="min-h-screen flex items-center justify-center bg-[#ece5dd]">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg flex flex-col mb-12">
        <div class="bg-green-500 text-black text-center p-4 rounded-t-lg font-bold">
            Sinhala - Korean  Chat
        </div>

        <div id="chat-box" class="chat-box"></div>

        <form id="chat-form" class="border-t p-3 bg-gray-100">
            <div class="flex items-center space-x-2">
                <button id="record-btn" type="button"
                    class="w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                    <img src="https://img.icons8.com/ios-filled/24/000000/microphone.png" alt="Voice">
                </button>
                <audio id="audio-preview" controls style="display:none;"></audio>
                <textarea id="user-input" rows="1" placeholder="Type a message..."
                    class="flex-1 resize-none rounded-full px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-400 bg-white"
                    required></textarea>

                <button type="button"
                    class="w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center text-xl"
                    id="send-btn">
                    ➤
                </button>
            </div>
        </form>

        <div class="text-center text-sm text-gray-500 py-2">
            Free plan: Max 20 text messages, no voice chat
        </div>
    </div>
</div>
@section('scripts')
    <script>
       
    </script>
    
@endsection
<script src="{{ asset('js/chat.js') }}"></script>

@endsection