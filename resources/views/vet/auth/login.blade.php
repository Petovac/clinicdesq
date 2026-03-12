<!DOCTYPE html>
<html>
<head>
    <title>Vet Login</title>
</head>
<body style="max-width:400px;margin:100px auto;font-family:sans-serif;">

<h2>Vet Login</h2>

<form method="POST" action="{{ route('vet.login') }}">
    @csrf

    <div style="margin-bottom:12px;">
        <input type="email" name="email" placeholder="Email" required style="width:100%;padding:8px;">
    </div>

    <div style="margin-bottom:12px;">
        <input type="password" name="password" placeholder="Password" required style="width:100%;padding:8px;">
    </div>

    <button style="padding:8px 16px;">Login</button>

    @error('email')
        <p style="color:red;">{{ $message }}</p>
    @enderror
</form>

</body>
</html>