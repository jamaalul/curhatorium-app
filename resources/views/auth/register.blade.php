<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Signup</title>
    <!-- Mouseflow Tracking Script -->
    <script type="text/javascript">
      window._mfq = window._mfq || [];
      (function() {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.defer = true;
        mf.src = "//cdn.mouseflow.com/projects/c5eb0d0a-6b75-427c-81f3-ee3c9e946eca.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
      })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
      /* Social Login Styles */
      .social-login {
          display: flex;
          flex-direction: column;
          gap: 10px;
          margin-bottom: 20px;
      }

      .social-btn {
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 12px 20px;
          border: 1px solid #ddd;
          border-radius: 5px;
          text-decoration: none;
          color: #333;
          font-size: 14px;
          font-weight: 500;
          transition: all 0.3s ease;
          background: white;
      }

      .social-btn:hover {
          border-color: #ccc;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .social-icon {
          width: 18px;
          height: 18px;
          margin-right: 10px;
      }

      .google-btn:hover {
          background-color: #f8f9fa;
      }

      .divider {
          display: flex;
          align-items: center;
          margin: 20px 0;
          text-align: center;
      }

      .divider::before,
      .divider::after {
          content: '';
          flex: 1;
          height: 1px;
          background-color: #ddd;
      }

      .divider span {
          padding: 0 10px;
          color: #666;
          font-size: 14px;
      }
    </style>
</head>
<body>
    <div class="container">
    <div class="illustration">
      <img src="/assets/logo.svg" alt="logo" />
    </div>
    <div class="signup-box">
      <h1>Signup</h1>
      <!-- Social Login Buttons -->
      <div class="social-login">
        <a href="{{ route('socialite.redirect', 'google') }}" class="social-btn google-btn">
          <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="social-icon">
          Register with Google
        </a>
      </div>

      <div class="divider">
        <span>or</span>
      </div>
      <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" placeholder="Username (Disarankan samaran)" required name="username" autofocus/>
        @error('username')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="text" placeholder="Email" required name="email" autofocus/>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="password" placeholder="Password" required name="password"/>
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required/>

        <label>
            <input type="checkbox" name="terms" required>
            Setuju dengan <a href="/terms-and-conditions" target="_blank">Syarat dan Ketentuan</a>
        </label>
        @error('terms') <div>{{ $message }}</div> @enderror
        <div class="links">
          <a href="/login">Sudah Punya Akun</a>
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </div>
  </div>
</body>
</html>