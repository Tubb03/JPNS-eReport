<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SSTP LaporKini - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-slate-100 p-4 md:p-8 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-200 text-center">
        <div class="flex items-center justify-center gap-3 mb-6">
            <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-16 w-auto">
            <div class="text-left">
                <h1 class="text-3xl font-black text-blue-900 italic leading-none">LaporKini</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">
                    Sektor Sumber dan<br>Teknologi Pendidikan
                </p>
            </div>
        </div>

        <h2 class="text-xl font-black text-slate-800 mb-6">Staff Registration</h2>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="name" id="name" placeholder="Full Name" value="{{ old('name') }}" aria-label="Staff Full Name" required
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            <div>
                <input type="email" name="email" id="email" placeholder="Staff Email" value="{{ old('email') }}" aria-label="Staff Email Address" required
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            <div>
                <input type="password" name="password" id="password" placeholder="Password (Min 8 characters)" aria-label="Password" required minlength="8"
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            <div>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" aria-label="Confirm Password" required minlength="8"
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 text-sm">
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-3 rounded-lg text-xs font-bold border border-red-200 text-left">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-xl font-black text-base hover:bg-blue-700 transition shadow-md uppercase tracking-widest mt-2">
                Register
            </button>
        </form>

        <div class="mt-6 text-sm font-semibold text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Log in here</a>
        </div>
    </div>
</body>

</html>
