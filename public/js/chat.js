$(document).ready(function () {
localStorage.removeItem('chat_history');
    const chatBox = $('#chat-box');
    const userInput = $('#user-input');
    const audioPreview = $('#audio-preview');
    const token = localStorage.getItem('secret_token');
    const deviceId = getOrSetDeviceId();


    let recorder = null;
    let chunks = [];
    let audioBlob = null;

    // Start recording - called on press down
    function startRecording() {
        chunks = [];
        navigator.mediaDevices.getUserMedia({ audio: true }).then(stream => {
            recorder = new MediaRecorder(stream);
            recorder.ondataavailable = e => {
                if (e.data.size > 0) chunks.push(e.data);
            };
            recorder.onstop = () => {
                audioBlob = new Blob(chunks, { type: 'audio/webm' });
                const url = URL.createObjectURL(audioBlob);
                audioPreview.attr('src', url).show();
                userInput.hide();
            };
            recorder.start();
        }).catch(err => {
            alert('Microphone access denied or error');
            console.error(err);
        });
    }

    // Stop recording - called on press up
    function stopRecording() {
        if (recorder && recorder.state === 'recording') {
            recorder.stop();
        }
    }

    // Event handlers for recording button
    $('#record-btn').on('mousedown touchstart', function (e) {
        e.preventDefault();
        startRecording();
    });

    $('#record-btn').on('mouseup touchend', function (e) {
        e.preventDefault();
        stopRecording();
    });



    // Send message on button click
    $('#send-btn').on('click', function () {
        if (audioBlob) {
            // Send voice message
            const formData = new FormData();
            formData.append('audio', audioBlob, 'voice.webm');
            formData.append('device_id', deviceId);

            $.ajax({
                url: '/chat/voice',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-DEVICE-TOKEN': token
                },
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {

                    appendMessage('You (voice)', data.text, 'user-msg');
                    appendMessage('AI', data.reply, 'ai-msg');
                    resetRecordingUI();
                },
                error: function (xhr) {
if (xhr.status === 401) {
            window.location.href = '/login';
        }
                    if (xhr.status === 403 || xhr.status === 429) {
                        window.location.href = '/subscribe';
                    } else {
                        alert('Voice message send error');
                        console.error(xhr.responseText);
                    }
                }
            });
        } else {
            const message = userInput.val().trim();
            if (message === '') return;

            // Show user message

            appendMessage('You', message, 'user-msg');
  const chatHistory = JSON.parse(localStorage.getItem('chat_history') || '[]');
    const latestHistory = chatHistory.slice(-3); 
            userInput.val('');
console.log(latestHistory);

            // Send to backend
            $.ajax({
                url: '/chat/send',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    device_id: deviceId,
                    message: message,
                     chat_history: latestHistory
                }),
                success: function (data) {
console.log("error");
                    appendMessage('AI', data.reply, 'ai-msg');
                },
                error: function (xhr) {
console.log("error");
                   if (xhr.status === 403 || xhr.status === 429) {
console.log("error");

                        window.location.href = '/subscribe';
                   }
                }
            });
        }
    });

    // Display chat message in UI
function appendMessage(sender, message, type, save = true) {
    const msgDiv = $('<div>')
        .addClass(type === 'user-msg'
            ? 'bg-green-200 text-right p-2 rounded-lg self-end max-w-[70%] ml-auto'
            : 'bg-white text-left p-2 rounded-lg self-start max-w-[70%] mr-auto')
        .text((sender === 'You' ? '' : '-') + message);

    chatBox.append(msgDiv);
    chatBox.scrollTop(chatBox[0].scrollHeight);

    if (save) {
        // Format history as role/content for OpenAI
        let chatHistory = JSON.parse(localStorage.getItem('chat_history') || '[]');
        const role = sender === 'You' ? 'user' : 'assistant';
        chatHistory.push({ role: role, content: message });

        // Keep last 3 only
        if (chatHistory.length > 3) {
            chatHistory = chatHistory.slice(-3);
        }

        localStorage.setItem('chat_history', JSON.stringify(chatHistory));
    }
}




    // Generate or get device_id
    function getOrSetDeviceId() {
        let id = localStorage.getItem('device_id');
        if (!id) {
            id = 'device_' + Math.random().toString(36).substring(2, 10);
            localStorage.setItem('device_id', id);
        }
        return id;
    }

    function resetRecordingUI() {
        audioBlob = null;
        audioPreview.hide().attr('src', '');
        userInput.show();
    }

});