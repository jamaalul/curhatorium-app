<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Professional Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
  <div class="container">
    <div class="illustration">
      <img src="/assets/logo.svg" alt="logo" />
    </div>
    <div class="login-box">
      <h1>Professional Login</h1>
      <p style="text-align: center; color: #666; margin-bottom: 20px;">Access your professional dashboard</p>
        <form method="POST" action="{{ route('professional.login') }}">
         @csrf
         <input type="text" placeholder="WhatsApp Number (e.g., 6281234567890)" required name="whatsapp_number" autofocus value="{{ old('whatsapp_number') }}"/>
         @error('whatsapp_number')
             <div class="error-message">{{ $message }}</div>
         @enderror
         
         <input type="password" placeholder="Password" required name="password"/>
         @error('password')
             <div class="error-message">{{ $message }}</div>
         @enderror
        
        <label>
            <input type="checkbox" name="remember">
            Ingat Saya
        </label>
        
        <button type="submit">Log In</button>
      </form>
      
      <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('login') }}" style="color: #667eea; text-decoration: none; font-size: 14px;">
          ← Back to User Login
        </a>
      </div>
    </div>
  </div>
</body>
</html> 