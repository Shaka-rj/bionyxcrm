<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('css/main.css?v=1.0.2') }}">
</head>
<body>
    @if ($user['isrequest'])
        <div class="header">
            <h3>So'rovingiz ko'rib chiqilmoqda. Biroz kuting</h3>
        </div>
    @else
        <div class="header">
            <h3>Ro'yxatdan o'tish uchun so'rov qoldiring</h3>
        </div>
        <form class="registration" autocomplete="off" method="post" action="{{ route('registration.store') }}">
            @csrf
            <label>Ism</label>
            <input type="text" name="firstname" required>
            <label>Familiya</label>
            <input type="text" name="lastname" required>
            <input type="submit" value="Jo'natish">
        </form>
    @endif
</body>
</html>