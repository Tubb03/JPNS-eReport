<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSTP One Page Report - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('KPM Logo.png') }}" alt="Logo" class="h-12 w-auto">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-red-900 tracking-tight leading-none">Admin Area</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">User Management</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-black shadow-md hover:bg-blue-700 transition">BACK TO DASHBOARD</a>
                <div class="hidden md:flex flex-col items-end ml-2 border-l pl-4 border-gray-300">
                    <p class="text-xs font-bold text-gray-600 mb-1">{{ auth()->user()->email }} (Admin)</p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded shadow-sm text-xs font-bold hover:bg-red-100 transition whitespace-nowrap">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4 font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 font-bold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-xs uppercase tracking-wider text-gray-500">
                            <th class="p-4 font-black">Name</th>
                            <th class="p-4 font-black">Email</th>
                            <th class="p-4 font-black">Role</th>
                            <th class="p-4 font-black text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-b hover:bg-slate-50 transition">
                                <td class="p-4 font-bold text-slate-800">{{ $user->name }}</td>
                                <td class="p-4 text-slate-600">{{ $user->email }}</td>
                                <td class="p-4">
                                    @if($user->role === 'admin')
                                        <span class="bg-red-100 text-red-800 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider">Admin</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-2 py-1 rounded uppercase tracking-wider">Staff</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <button onclick="openPasswordModal('{{ $user->id }}', '{{ addslashes($user->name) }}')" class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded text-xs font-black uppercase tracking-wider hover:bg-emerald-100 transition mr-2">Reset Password</button>
                                    
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to permanently delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded text-xs font-black uppercase tracking-wider hover:bg-red-100 transition">Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Password Reset Modal -->
    <div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm">
            <h3 class="text-lg font-black text-slate-800 mb-2">Reset Password</h3>
            <p class="text-sm text-gray-500 mb-4">Set a new password for <span id="pwdUserName" class="font-bold text-blue-600"></span>.</p>
            
            <form id="passwordForm" method="POST">
                @csrf
                @method('PUT')
                <input type="password" name="password" placeholder="New Password (Min 8)" required minlength="8"
                    class="w-full p-3 border rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-emerald-400 bg-gray-50 text-sm mb-4">
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePasswordModal()" class="px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 uppercase text-xs tracking-wider">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-black rounded-lg hover:bg-emerald-700 uppercase text-xs tracking-wider transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPasswordModal(userId, userName) {
            document.getElementById('pwdUserName').innerText = userName;
            const form = document.getElementById('passwordForm');
            // Assuming routes are like /admin/users/1/password
            form.action = `/admin/users/${userId}/password`;
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordForm').reset();
        }
    </script>
</body>
</html>
