<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/features.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/profile.css') }}">
</head>
<body>
    @include('components.navbar')
  <main class="profile-main">
    <section class="profile-section">
      <h2 class="profile-title">Profil Saya</h2>
      <!-- Ringkasan Profil -->
      <div class="profile-overview">
        <form id="profilePicForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:inline;">
          @csrf
          @method('PATCH')
          <input type="hidden" name="username" value="{{ $user->username }}">
          <input type="hidden" name="email" value="{{ $user->email }}">
          <div class="profile-pic-container">
            <img id="profilePicPreview"
                 src="{{ isset($user) && $user['profile_picture'] ? asset('storage/' . $user['profile_picture']) : asset('assets/profile_pict.svg') }}"
                 alt="Profil"
                 class="profile-pic"
                 onerror="this.onerror=null;this.src='{{ asset('assets/profile_pict.svg') }}';">
            <label class="change-pic-label">
              Ganti
              <input id="profilePicInput" name="profile_picture" type="file" class="hidden" accept="image/*" style="display:none;">
            </label>
          </div>
        </form>
        <div class="profile-username">{{ '@' . ($user->username ?? 'username') }}</div>
        <div class="profile-email">{{ $user->email ?? 'email@example.com' }}</div>
        <!-- XP dan Lencana -->
        @php
          $xp = $user->total_xp ?? 0;
          if ($xp >= 4000) {
            $badgeName = 'Nova';
            $badgeImage = asset('assets/nova.svg');
            $badgeColor = '#FF00D4';
          } elseif ($xp >= 3000) {
            $badgeName = 'Inferno';
            $badgeImage = asset('assets/inferno.svg');
            $badgeColor = '#7220C5';
          } elseif ($xp >= 2000) {
            $badgeName = 'Beacon';
            $badgeImage = asset('assets/beacon.svg');
            $badgeColor = '#4E42A6';
          } elseif ($xp >= 1000) {
            $badgeName = 'Torch';
            $badgeImage = asset('assets/torch.svg');
            $badgeColor = '#865A5A';
          } else {
            $badgeName = 'Kindle';
            $badgeImage = asset('assets/kindle.svg');
            $badgeColor = '#8e805d';
          }
        @endphp
        <div class="xp-badge-box">
          <div class="badge-box-profile" style="background: {{ $badgeColor }};">
            <img class="badge-logo-profile" src="{{ $badgeImage }}" alt="lencana">
            <span class="badge-text-profile">{{ $badgeName }}</span>
          </div>
          <span class="xp-profile" id="xp-profile">{{ $xp }} XP</span>
        </div>
        
        <!-- Daily XP Progress Section -->
        @php
          $dailyXpSummary = $user->getDailyXpSummary();
          $xpService = app(\App\Services\XpService::class);
          $membershipType = $xpService->getUserMembershipType($user);
        @endphp
        <div class="daily-xp-section">
          <div class="daily-xp-container">
            <div class="daily-xp-progress-circle">
              <svg width="80" height="80" viewBox="0 0 80 80">
                <circle cx="40" cy="40" r="35" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                <circle id="profile-daily-xp-circle" cx="40" cy="40" r="35" stroke="#10b981" stroke-width="6" fill="none" 
                        stroke-dasharray="219.91" stroke-dashoffset="219.91" transform="rotate(-90 40 40)"/>
              </svg>
              <div class="daily-xp-text">
                <span id="profile-daily-xp-current">{{ $dailyXpSummary['daily_xp_gained'] }}</span>
                <span class="daily-xp-separator">/</span>
                <span id="profile-daily-xp-max">{{ $dailyXpSummary['max_daily_xp'] }}</span>
              </div>
              <div class="daily-xp-label">XP Today</div>
            </div>
            <div class="daily-xp-info">
              <div class="membership-type">
                <span class="membership-label">Membership:</span>
                <span class="membership-value">{{ $membershipType === 'subscription' ? 'Paid' : 'Free' }} ({{ $membershipType === 'subscription' ? 'Paid' : 'Calm Starter' }})</span>
              </div>
              <div class="daily-limit-info">
                <span class="limit-label">Daily Limit:</span>
                <span class="limit-value">{{ $dailyXpSummary['max_daily_xp'] }} XP per day</span>
              </div>
              <div class="progress-percentage">
                <span id="profile-daily-xp-percentage">{{ number_format($dailyXpSummary['daily_progress_percentage'], 1) }}%</span> Complete
              </div>
            </div>
          </div>
        </div>
        <!-- Cinema-style Tickets Section -->
        <div class="cinema-tickets-section">
          <h3 class="cinema-tickets-title">Tiket Anda</h3>
          <div class="cinema-tickets-list">
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
            @foreach ($tickets as $ticket)
              @php
                $isUnlimited = $ticket['remaining_value'] === null;
                $value = $isUnlimited ? 'Unlimited' : $ticket['remaining_value'];
              @endphp
              <div class="cinema-ticket">
                <div class="cinema-ticket-content">
                  <div class="cinema-ticket-feature">{{ $featureNames[$ticket['ticket_type']] ?? ucfirst(str_replace(['_', '-'], ' ', $ticket['ticket_type'])) }}</div>
                  <div class="cinema-ticket-value">
                    @if($isUnlimited)
                      Unlimited
                    @elseif($ticket['limit_type'] === 'hour')
                      {{ is_numeric($value) ? number_format($value, 2, '.', '') : $value }} Jam
                    @elseif($ticket['limit_type'] === 'day')
                      {{ $value }} Hari
                    @else
                      {{ $value }} Tiket
                    @endif
                  </div>
                  @if($ticket['expires_at'])
                    <div class="cinema-ticket-expiry">Exp: {{ \Carbon\Carbon::parse($ticket['expires_at'])->format('d M Y') }}</div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      
          <!-- Form Edit Profil -->
          <div style="background:#fff;padding:28px 24px 18px 24px;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:32px;">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:18px;width:100%;">
              @csrf
              @method('PATCH')
              <div style="font-family:FigtreeBold;font-size:1.1em;color:#333;margin-bottom:4px;">Edit Profil</div>
              <div>
                <label for="username" class="block font-semibold mb-1">Nama Pengguna</label>
                <input id="username" name="username" type="text" value="{{ old('username', $user['username'] ?? '') }}" required class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user['email'] ?? '') }}" required class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <button type="submit" class="profile-save" style="background:#48A6A6;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Simpan Perubahan</button>
            </form>
          </div>
      
          <!-- Bagian Ganti Kata Sandi -->
          <div style="background:#fff;padding:28px 24px 18px 24px;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:32px;">
            <form method="POST" action="#" style="display:flex;flex-direction:column;gap:18px;width:100%;">
              <div style="font-family:FigtreeBold;font-size:1.1em;color:#333;margin-bottom:4px;">Ganti Kata Sandi</div>
              <div>
                <label for="current_password" class="block font-semibold mb-1">Kata Sandi Saat Ini</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="password" class="block font-semibold mb-1">Kata Sandi Baru</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="password_confirmation" class="block font-semibold mb-1">Konfirmasi Kata Sandi Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <button type="submit" class="profile-save" style="background:#48A6A6;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Ganti Kata Sandi</button>
            </form>
          </div>
      
          <!-- Aksi Akun -->
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:24px;">
            <form method="POST" action="{{ route('logout') }}">
              <button type="submit" class="profile-logout" style="background:#e57373;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Keluar</button>
            </form>
            <form method="POST" action="#" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.');">
              <button type="submit" style="background:#fff;color:#e57373;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:1px solid #e57373;transition:background 0.2s;">Hapus Akun</button>
            </form>
          </div>
        </section>
      </main>
      @include('components.footer')
      
      <script>
        // Pratinjau foto profil dan auto-submit
        const input = document.getElementById('profilePicInput');
        const preview = document.getElementById('profilePicPreview');
        if(input && preview) {
          input.addEventListener('change', () => {
            const file = input.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = e => preview.src = e.target.result;
              reader.readAsDataURL(file);
              // Otomatis submit form saat file baru dipilih
              document.getElementById('profilePicForm').submit();
            }
          });
        }
        
        // Update daily XP progress circle color
        document.addEventListener('DOMContentLoaded', function() {
          const dailyXpCircle = document.getElementById('profile-daily-xp-circle');
          const dailyXpCurrent = document.getElementById('profile-daily-xp-current');
          const dailyXpMax = document.getElementById('profile-daily-xp-max');
          const dailyXpPercentage = document.getElementById('profile-daily-xp-percentage');
          
          if (dailyXpCircle && dailyXpCurrent && dailyXpMax && dailyXpPercentage) {
            const current = parseInt(dailyXpCurrent.textContent);
            const max = parseInt(dailyXpMax.textContent);
            const progress = (current / max) * 100;
            
            // Update circle progress
            const circumference = 219.91; // 2 * π * radius (35)
            const offset = circumference - (progress / 100) * circumference;
            dailyXpCircle.style.strokeDashoffset = offset;
            
            // Update color based on progress
            if (progress >= 90) {
              dailyXpCircle.style.stroke = "#ef4444"; // Red when near limit
            } else if (progress >= 70) {
              dailyXpCircle.style.stroke = "#f59e0b"; // Orange when getting close
            } else {
              dailyXpCircle.style.stroke = "#10b981"; // Green for normal progress
            }
          }
        });
      </script>
</body>
</html>
