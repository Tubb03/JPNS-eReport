<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SSTP One Page Report - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-slate-100 p-4 md:p-8 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-200 text-center">
        <div class="flex items-center justify-center gap-3 mb-6">
            <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-16 w-auto">
            <div class="text-left">
                <h1 class="text-2xl md:text-3xl font-black text-blue-900 tracking-tight leading-none">One Page Report</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">
                    Sektor Sumber dan<br>Teknologi Pendidikan
                </p>
            </div>
        </div>

        <h2 class="text-xl font-black text-slate-800 mb-6">Staff Login Portal</h2>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <input type="email" name="email" id="email" placeholder="Staff Email" value="{{ old('email') }}" aria-label="Staff Email Address" required
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            <div>
                <input type="password" name="password" id="password" placeholder="Password" aria-label="Password" required
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            
            @if ($errors->any())
                <p class="text-red-500 text-xs font-bold uppercase tracking-wider">{{ $errors->first() }}</p>
            @endif

            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-xl font-black text-base hover:bg-blue-700 transition shadow-md uppercase tracking-widest mt-2">
                Log In
            </button>
        </form>

        <div class="mt-6 text-sm font-semibold text-gray-500">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register here</a>
        </div>
    </div>
</body>

</html>