<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Story Blog</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FingerprintJS library with defer -->
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js" defer></script>
    <!-- Add this before your chat.js -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script defer>
      document.addEventListener('DOMContentLoaded', () => {
        FingerprintJS.load().then(fp => {
          fp.get().then(result => {
            const deviceId = result.visitorId; // device-level unique ID
            localStorage.setItem('device_id', deviceId);
            window.deviceId = deviceId;

            // Authenticate device with the server
            authenticateDevice();
          });
        });
      });

      async function authenticateDevice() {
        const res = await fetch('/device/authenticate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ device_id: window.deviceId })
        });
        const data = await res.json();
        if (data.secret_token) {
          localStorage.setItem('secret_token', data.secret_token);
        }
      }
    </script>
</head>
<body class="">
    @include('partials.navbar')

    <main class="py-8 ">
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
