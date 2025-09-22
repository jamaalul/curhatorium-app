<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $vite_hmr_host = '';
        if (app()->environment('local') && file_exists(public_path('hot'))) {
            $vite_hmr_host = rtrim(file_get_contents(public_path('hot')));
        }
    @endphp
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline' {{ $vite_hmr_host }}; script-src 'self' 'unsafe-inline' {{ $vite_hmr_host }}; connect-src 'self' {{ $vite_hmr_host }} ws: wss:;">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    
</head>
<body class="bg-gray-50">
    @include('components.navbar')
    
    <main class="max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Profil</h1>
            <p class="mt-2 text-gray-600">Kelola informasi profil dan pengaturan akun Anda</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-4">
            <!-- Left Column - Profile Overview -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-lg shadow border p-6">
                    <div class="text-center">
                        <!-- Profile Picture Section -->
                        <form id="profilePicForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="inline-block" onsubmit="return validateFormSubmission(event);">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="username" value="{{ $user->username }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <div class="relative inline-block">
                                <img id="profilePicPreview"
                                     src="{{ isset($user) && $user['profile_picture'] ? asset('storage/' . $user['profile_picture']) : asset('assets/profile_pict.svg') }}"
                                     alt="Profil"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/profile_pict.svg') }}';">
                                <label class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 cursor-pointer hover:bg-blue-700 transition shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <input id="profilePicInput" name="profile_picture" type="file" class="hidden" accept="image/jpeg,image/jpg,image/png,image/webp">
                                </label>
                            </div>
                        </form>
                        
                        <div class="mt-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ '@' . ($user->username ?? 'username') }}</h3>
                            <p class="text-gray-600">{{ $user->email ?? 'email@example.com' }}</p>
                        </div>

                        <!-- Badge and XP Section -->
                        @php
                          $xp = $user->total_xp ?? 0;
                          if ($xp >= 4000) {
                            $badgeName = 'Nova';
                            $badgeImage = asset('assets/nova.svg');
                            $badgeColor = 'from-pink-500 to-purple-600';
                          } elseif ($xp >= 3000) {
                            $badgeName = 'Inferno';
                            $badgeImage = asset('assets/inferno.svg');
                            $badgeColor = 'from-purple-600 to-indigo-600';
                          } elseif ($xp >= 2000) {
                            $badgeName = 'Beacon';
                            $badgeImage = asset('assets/beacon.svg');
                            $badgeColor = 'from-indigo-500 to-blue-600';
                          } elseif ($xp >= 1000) {
                            $badgeName = 'Torch';
                            $badgeImage = asset('assets/torch.svg');
                            $badgeColor = 'from-orange-500 to-red-500';
                          } else {
                            $badgeName = 'Kindle';
                            $badgeImage = asset('assets/kindle.svg');
                            $badgeColor = 'from-yellow-500 to-orange-500';
                          }
                        @endphp

                        <div class="mt-6">
                            <div class="rounded-lg p-4 text-white bg-gradient-to-r {{ $badgeColor }}">
                                <div class="flex items-center justify-center gap-3">
                                    <img class="w-8 h-8" src="{{ $badgeImage }}" alt="lencana">
                                    <span class="font-semibold">{{ $badgeName }}</span>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-2xl font-bold" id="xp-profile">{{ $xp }}</span>
                                    <span class="text-sm opacity-90">XP</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Progress Card -->
                @php
                  $dailyXpSummary = $user->getDailyXpSummary();
                  $xpService = app(\App\Services\XpService::class);
                  $membershipType = $xpService->getUserMembershipType($user);
                @endphp
                <div class="bg-white rounded-lg shadow border p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Progress Harian</h4>
                    <div class="flex items-center justify-center mb-4">
                        <div class="relative">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 80 80">
                                <circle class="text-gray-200" stroke-width="6" stroke="currentColor" fill="transparent" r="35" cx="40" cy="40"/>
                                <circle id="profile-daily-xp-circle" class="text-emerald-500" stroke-width="6" stroke-dasharray="219.91" stroke-dashoffset="219.91" stroke-linecap="round" stroke="currentColor" fill="transparent" r="35" cx="40" cy="40"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-bold text-gray-900" id="profile-daily-xp-current">{{ $dailyXpSummary['daily_xp_gained'] }}</span>
                                <span class="text-xs text-gray-500">/ {{ $dailyXpSummary['max_daily_xp'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Membership:</span>
                            <span class="font-medium">{{ $membershipType === 'subscription' ? 'Paid' : 'Free' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Batas Harian:</span>
                            <span class="font-medium">{{ $dailyXpSummary['max_daily_xp'] }} XP</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Progress:</span>
                            <span class="font-medium" id="profile-daily-xp-percentage">{{ number_format($dailyXpSummary['daily_progress_percentage'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tickets Section -->
                <div class="bg-white rounded-lg shadow border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Tiket Anda</h3>
                    @php
                      $featureNames = [
                        'mental_test' => 'Tes Kesehatan Mental',
                        'tracker' => 'Mood and Productivity Tracker',
                        'mentai_chatbot' => 'Ment-AI Chatbot',
                        'missions' => 'Missions of The Day',
                        'support_group' => 'Support Group Discussion',
                        'deep_cards' => 'Deep Cards',
                        'share_talk_ranger_chat' => 'Share and Talk via Chat w/ Rangers',
                        'share_talk_psy_chat' => 'Share and Talk via Chat w/ Psikolog',
                        'share_talk_psy_video' => 'Share and Talk via Video Call w/ Psikiater',
                      ];
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($tickets as $ticket)
                            @php
                                $isUnlimited = $ticket['remaining_value'] === null;
                                $value = $isUnlimited ? 'Unlimited' : $ticket['remaining_value'];
                                $featureName = $featureNames[$ticket['ticket_type']] ?? ucfirst(str_replace(['_', '-'], ' ', $ticket['ticket_type']));
                            @endphp
                            <div class="flex max-w-sm w-full h-36 bg-amber-50 rounded-lg shadow-md font-mono text-gray-700 relative overflow-hidden border border-amber-200">
                                <div class="w-24 p-4 flex flex-col justify-between items-center text-center border-r-2 border-dashed border-amber-300">
                                    <div class="flex flex-col justify-between h-full items-center">
                                        <span class="text-xs font-bold tracking-wider transform -rotate-90 whitespace-nowrap absolute -left-7 top-14">Curhatorium</span>
                                        <span class="text-3xl">🎟️</span>
                                        <span class="text-[10px] font-bold">Safest Place</span>
                                    </div>
                                </div>
                                <div class="flex-1 p-4 flex flex-col justify-between">
                                    <div class="h-full flex flex-col justify-between">
                                        <h4 class="text-base font-bold leading-tight">{{ $featureName }}</h4>
                                        <div class="mt-2.5">
                                            <div class="flex items-baseline gap-1">
                                                @if($isUnlimited)
                                                    <span class="text-lg text-green-600 font-bold">Unlimited</span>
                                                @else
                                                    <span class="text-2xl font-bold text-red-500">
                                                        @if($ticket['limit_type'] === 'hour')
                                                            {{ is_numeric($value) ? number_format($value, 2, '.', '') : $value }} Jam
                                                        @elseif($ticket['limit_type'] === 'day')
                                                            {{ $value }} Hari
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                    @if($ticket['limit_type'] !== 'hour' && $ticket['limit_type'] !== 'day')
                                                        <span class="text-xs font-bold">Tiket</span>
                                                    @endif
                                                @endif
                                            </div>
                                            @if($ticket['expires_at'])
                                                <p class="text-xs mt-1">
                                                    Expires: {{ \Carbon\Carbon::parse($ticket['expires_at'])->format('d M Y') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="h-5 w-full bg-[repeating-linear-gradient(to_right,#4a4a4a,#4a4a4a_2px,transparent_2px,transparent_4px)]"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="bg-white rounded-lg shadow border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Edit Profil</h3>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengguna</label>
                                <input id="username" name="username" type="text" value="{{ old('username', $user['username'] ?? '') }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#48a6a6] focus:border-[#48a6a6] sm:text-sm">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user['email'] ?? '') }}" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#48a6a6] focus:border-[#48a6a6] sm:text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white py-2 px-6 rounded-md font-medium transition bg-[#48a6a6] hover:bg-[#3b8b8b]">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-lg shadow border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Ganti Kata Sandi</h3>
                    <form method="POST" action="#" class="space-y-6">
                        @csrf
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#48a6a6] focus:border-[#48a6a6] sm:text-sm">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#48a6a6] focus:border-[#48a6a6] sm:text-sm">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#48a6a6] focus:border-[#48a6a6] sm:text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white py-2 px-6 rounded-md font-medium transition bg-[#48a6a6] hover:bg-[#3b8b8b]">
                                Ganti Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Actions -->
                <div class="bg-white rounded-lg shadow border p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Aksi Akun</h3>
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-white py-2 px-6 rounded-md font-medium transition bg-gray-600 hover:bg-gray-700">
                                Keluar
                            </button>
                        </form>
                        <form method="POST" action="#" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            <button type="submit" class="w-full text-white py-2 px-6 rounded-md font-medium transition bg-red-600 hover:bg-red-700">
                                Hapus Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('components.footer')
    
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($errors->all() as $error)
                    showError('{{ addslashes($error) }}');
                @endforeach
            });
        </script>
    @endif
    
    <script>
        // Profile picture preview and validation
        const input = document.getElementById('profilePicInput');
        const preview = document.getElementById('profilePicPreview');
        
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const MAX_DIMENSIONS = { width: 2048, height: 2048 };
        
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm';
            errorDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-sm">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.parentNode.removeChild(errorDiv);
                }
            }, 5000);
            
            input.value = '';
        }
        
        function showLoading() {
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'upload-loading';
            loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loadingDiv.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-900">Uploading profile picture...</span>
                </div>
            `;
            document.body.appendChild(loadingDiv);
        }
        
        function hideLoading() {
            const loadingDiv = document.getElementById('upload-loading');
            if (loadingDiv && loadingDiv.parentNode) {
                loadingDiv.parentNode.removeChild(loadingDiv);
            }
        }
        
        function validateFormSubmission(event) {
            const form = event.target;
            const fileInput = form.querySelector('input[type="file"]');
            
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                if (!validateFile(file)) {
                    event.preventDefault();
                    return false;
                }
            }
            
            return true;
        }
        
        function validateFile(file) {
            if (!ALLOWED_TYPES.includes(file.type)) {
                showError('Hanya file JPEG, PNG, atau WebP yang diperbolehkan.');
                return false;
            }
            
            if (file.size > MAX_FILE_SIZE) {
                showError('Ukuran file tidak boleh lebih dari 2MB.');
                return false;
            }
            
            const fileName = file.name.toLowerCase();
            if (fileName.includes('..') || fileName.includes('/') || fileName.includes('\\')) {
                showError('Nama file tidak valid.');
                return false;
            }
            
            const allowedExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
            const fileExtension = fileName.substring(fileName.lastIndexOf('.'));
            if (!allowedExtensions.includes(fileExtension)) {
                showError('Ekstensi file tidak diperbolehkan.');
                return false;
            }
            
            return true;
        }
        
        function validateImageDimensions(file) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                const url = URL.createObjectURL(file);
                
                img.onload = function() {
                    URL.revokeObjectURL(url);
                    if (img.width > MAX_DIMENSIONS.width || img.height > MAX_DIMENSIONS.height) {
                        reject('Dimensi gambar tidak boleh lebih dari 2048x2048 pixel.');
                    } else {
                        resolve(true);
                    }
                };
                
                img.onerror = function() {
                    URL.revokeObjectURL(url);
                    reject('File tidak dapat dibaca sebagai gambar.');
                };
                
                img.src = url;
            });
        }
        
        if(input && preview) {
            input.addEventListener('change', async () => {
                const file = input.files[0];
                if (file) {
                    if (!validateFile(file)) {
                        return;
                    }
                    
                    try {
                        await validateImageDimensions(file);
                        showLoading();
                        
                        const reader = new FileReader();
                        reader.onload = e => preview.src = e.target.result;
                        reader.readAsDataURL(file);
                        
                        document.getElementById('profilePicForm').submit();
                    } catch (error) {
                        hideLoading();
                        showError(error);
                    }
                }
            });
        }
        
        // Update daily XP progress circle
        document.addEventListener('DOMContentLoaded', function() {
            const dailyXpCircle = document.getElementById('profile-daily-xp-circle');
            const dailyXpCurrent = document.getElementById('profile-daily-xp-current');
            const dailyXpMax = document.querySelector('#profile-daily-xp-current + .text-xs');
            const dailyXpPercentage = document.getElementById('profile-daily-xp-percentage');
            
            if (dailyXpCircle && dailyXpCurrent) {
                const current = parseInt(dailyXpCurrent.textContent);
                const maxText = dailyXpMax ? dailyXpMax.textContent.replace('/ ', '') : '100';
                const max = parseInt(maxText);
                const progress = (current / max) * 100;
                
                const circumference = 219.91;
                const offset = circumference - (progress / 100) * circumference;
                dailyXpCircle.style.strokeDashoffset = offset;
                
                if (progress >= 90) {
                    dailyXpCircle.style.stroke = "#ef4444";
                } else if (progress >= 70) {
                    dailyXpCircle.style.stroke = "#f59e0b";
                } else {
                    dailyXpCircle.style.stroke = "#10b981";
                }
            }
        });
    </script>
</body>
</html>
