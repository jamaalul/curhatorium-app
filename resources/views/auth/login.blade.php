<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Login</title>
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
    @vite(['resources/css/app.css'])
</head>
<body class="h-screen bg-cover bg-center bg-[url('/images/background.jpg')]">
  <div class="flex justify-center items-center h-full flex-row p-5 md:flex-col">
    <div class="flex-1 flex justify-center items-center md:mb-5">
      <img src="/assets/logo.svg" alt="logo" class="max-w-[70%] h-auto md:max-w-[50%] lg:translate-x-[-7vw]" />
    </div>
    <div class="flex-1 max-w-[350px] bg-white p-[30px] rounded-[10px] shadow-lg md:translate-x-[-10vw] lg:translate-x-[-15vw] sm:p-5 sm:w-full">
      <h1 class="text-center tracking-[0.03em] mb-5 font-black text-[3rem] text-[#2b9c94]">Login</h1>

      <!-- Social Login Buttons -->
      <div class="flex flex-col gap-[10px] mb-5">
        <a href="{{ route('socialite.redirect', 'google') }}" class="flex items-center justify-center px-5 py-3 border border-gray-300 rounded-[5px] no-underline text-gray-800 text-sm font-medium transition-all duration-300 bg-white hover:border-gray-400 hover:shadow-md hover:bg-gray-50">
          <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-[18px] h-[18px] mr-[10px]">
          Login with Google
        </a>
      </div>

      <div class="flex items-center my-5 text-center before:flex-1 before:h-px before:bg-gray-300 after:flex-1 after:h-px after:bg-gray-300">
        <span class="px-[10px] text-gray-600 text-sm">or</span>
      </div>

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="text" placeholder="Username/Email" required name="login" autofocus class="w-full p-[10px] my-[10px] border border-gray-300 rounded-[5px]"/>
        @error('login')
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror
        <input type="password" placeholder="Password" required name="password" class="w-full p-[10px] my-[10px] border border-gray-300 rounded-[5px]"/>
        @error('password')
            <div class="text-red-500 text-sm">{{ $message }}</div>
        @enderror
                <label class="text-sm">
            <input type="checkbox" name="remember" class="mr-[5px]">
            Ingat Saya
        </label>
        <div class="flex justify-between text-sm mt-[10px]">
          <a href="{{ route('register') }}" class="text-[#2b9c94] no-underline">Signup</a>
          {{-- <a href="{{ route('password.request') }}">Lupa Password</a> --}}
        </div>
        <button type="submit" class="w-full p-[10px] mt-5 bg-[#2b9c94] text-white border-none rounded-[5px] cursor-pointer hover:bg-[#258a83]">Log In</button>
      </form>
    </div>
  </div>
</body>
</html>