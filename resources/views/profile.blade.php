<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: blob:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline';">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="{{ asset('css/main/profile-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    
</head>
<body>
    @include('components.navbar')
    
    <main class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Dashboard Profil</h1>
            <p class="page-subtitle">Kelola informasi profil dan pengaturan akun Anda</p>
        </div>

        <div class="grid-container">
            <!-- Left Column - Profile Overview -->
            <div class="grid-col-span-1 space-y-6">
                <!-- Profile Card -->
                <div class="card">
                    <div class="profile-card-header">
                        <!-- Profile Picture Section -->
                        <form id="profilePicForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-picture-form" onsubmit="return validateFormSubmission(event);">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="username" value="{{ $user->username }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <div class="profile-picture-container">
                                <img id="profilePicPreview"
                                     src="{{ isset($user) && $user['profile_picture'] ? asset('storage/' . $user['profile_picture']) : asset('assets/profile_pict.svg') }}"
                                     alt="Profil"
                                     class="profile-picture"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/profile_pict.svg') }}';">
                                <label class="upload-label">
                                    <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <input id="profilePicInput" name="profile_picture" type="file" class="hidden-input" accept="image/jpeg,image/jpg,image/png,image/webp">
                                </label>
                            </div>
                        </form>
                        
                        <div class="profile-info">
                            <h3 class="profile-username">{{ '@' . ($user->username ?? 'username') }}</h3>
                            <p class="profile-email">{{ $user->email ?? 'email@example.com' }}</p>
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

                        <div class="badge-xp-section">
                            <div class="badge-card bg-gradient-to-r {{ $badgeColor }}">
                                <div class="badge-content">
                                    <img class="badge-image" src="{{ $badgeImage }}" alt="lencana">
                                    <span class="badge-name">{{ $badgeName }}</span>
                                </div>
                                <div class="xp-display">
                                    <span class="xp-value" id="xp-profile">{{ $xp }}</span>
                                    <span class="xp-label">XP</span>
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
                <div class="card">
                    <h4 class="progress-card-title">Progress Harian</h4>
                    <div class="progress-circle-container">
                        <div class="progress-circle-wrapper">
                            <svg class="progress-circle-svg" viewBox="0 0 80 80">
                                <circle class="progress-circle-bg" cx="40" cy="40" r="35"/>
                                <circle id="profile-daily-xp-circle" class="progress-circle-fg" cx="40" cy="40" r="35"/>
                            </svg>
                            <div class="progress-circle-text">
                                <span class="progress-current-xp" id="profile-daily-xp-current">{{ $dailyXpSummary['daily_xp_gained'] }}</span>
                                <span class="progress-max-xp">/ {{ $dailyXpSummary['max_daily_xp'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="progress-details space-y-2">
                        <div class="progress-detail-row">
                            <span class="progress-detail-label">Membership:</span>
                            <span class="progress-detail-value">{{ $membershipType === 'subscription' ? 'Paid' : 'Free' }}</span>
                        </div>
                        <div class="progress-detail-row">
                            <span class="progress-detail-label">Batas Harian:</span>
                            <span class="progress-detail-value">{{ $dailyXpSummary['max_daily_xp'] }} XP</span>
                        </div>
                        <div class="progress-detail-row">
                            <span class="progress-detail-label">Progress:</span>
                            <span class="progress-detail-value" id="profile-daily-xp-percentage">{{ number_format($dailyXpSummary['daily_progress_percentage'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Main Content -->
            <div class="grid-col-span-2 space-y-6">
                <!-- Tickets Section -->
                <div class="card">
                    <h3 class="tickets-title">Tiket Anda</h3>
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
                    <div class="tickets-grid">
                        @foreach ($tickets as $ticket)
                            @php
                                $isUnlimited = $ticket['remaining_value'] === null;
                                $value = $isUnlimited ? 'Unlimited' : $ticket['remaining_value'];
                                $featureName = $featureNames[$ticket['ticket_type']] ?? ucfirst(str_replace(['_', '-'], ' ', $ticket['ticket_type']));
                            @endphp
                            <div class="ticket-vintage">
                                <div class="ticket-stub">
                                    <div class="ticket-stub-content">
                                        <span class="ticket-brand">Curhatorium</span>
                                        <span class="ticket-feature-icon">🎟️</span>
                                        <span class="ticket-admit">Safest Place</span>
                                    </div>
                                </div>
                                <div class="ticket-main">
                                    <div class="ticket-main-content">
                                        <h4 class="ticket-feature-name">{{ $featureName }}</h4>
                                        <div class="ticket-details">
                                            <div class="ticket-value">
                                                @if($isUnlimited)
                                                    <span class="ticket-value-text unlimited">Unlimited</span>
                                                @else
                                                    <span class="ticket-value-text">
                                                        @if($ticket['limit_type'] === 'hour')
                                                            {{ is_numeric($value) ? number_format($value, 2, '.', '') : $value }} Jam
                                                        @elseif($ticket['limit_type'] === 'day')
                                                            {{ $value }} Hari
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </span>
                                                    @if($ticket['limit_type'] !== 'hour' && $ticket['limit_type'] !== 'day')
                                                        <span class="ticket-value-label">Tiket</span>
                                                    @endif
                                                @endif
                                            </div>
                                            @if($ticket['expires_at'])
                                                <p class="ticket-expiry-date">
                                                    Expires: {{ \Carbon\Carbon::parse($ticket['expires_at'])->format('d M Y') }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ticket-barcode"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="card">
                    <h3 class="form-title">Edit Profil</h3>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        <div class="form-grid">
                            <div>
                                <label for="username" class="form-label">Nama Pengguna</label>
                                <input id="username" name="username" type="text" value="{{ old('username', $user['username'] ?? '') }}" required
                                       class="form-input">
                            </div>
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user['email'] ?? '') }}" required
                                       class="form-input">
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button button-blue">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="card">
                    <h3 class="form-title">Ganti Kata Sandi</h3>
                    <form method="POST" action="#" class="space-y-6">
                        @csrf
                        <div>
                            <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                                   class="form-input">
                        </div>
                        <div class="form-grid">
                            <div>
                                <label for="password" class="form-label">Kata Sandi Baru</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       class="form-input">
                            </div>
                            <div>
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       class="form-input">
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button button-blue">
                                Ganti Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Actions -->
                <div class="card">
                    <h3 class="form-title">Aksi Akun</h3>
                    <div class="account-actions">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type.submit" class="button button-gray">
                                Keluar
                            </button>
                        </form>
                        <form method="POST" action="#" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.');">
                            @csrf
                            <button type="submit" class="button button-red">
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
