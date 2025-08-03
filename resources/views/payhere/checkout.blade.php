<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to PayHere...</title>
</head>
<body>
    <p style="text-align: center; margin-top: 50px; font-size: 18px;">Redirecting to PayHere Payment Gateway...</p>

    <form id="payhere-form" method="POST" action="https://sandbox.payhere.lk/pay/checkout">
        @foreach ($data as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.getElementById('payhere-form').submit();
    </script>
</body>
</html>
