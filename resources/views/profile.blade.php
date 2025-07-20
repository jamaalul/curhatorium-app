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
        <!-- Bagian Tiket (placeholder) -->
        <div class="tickets-section">
          <div class="tickets-title">Tiket Anda</div>
          <div class="tickets-list">
            <!-- Ticket Card 1 -->
            <div class="ticket-card">
              <img src="/assets/support_group_discussion.svg" alt="SGD">
              <div class="ticket-feature">SGD Group</div>
              <div class="ticket-count">2 Tiket</div>
            </div>
            <!-- Ticket Card 2 -->
            <div class="ticket-card">
              <img src="/assets/deep_cards.svg" alt="Deep Cards">
              <div class="ticket-feature">Deep Cards</div>
              <div class="ticket-count">1 Tiket</div>
            </div>
            <!-- Ticket Card 3 -->
            <div class="ticket-card">
              <img src="/assets/mental_support_chatbot.svg" alt="Ment-AI">
              <div class="ticket-feature">Ment-AI</div>
              <div class="ticket-count">3 Tiket</div>
            </div>
            <!-- Ticket Card 4 -->
            <div class="ticket-card">
              <img src="/assets/mental_health_test.svg" alt="Mental Test">
              <div class="ticket-feature">Mental Test</div>
              <div class="ticket-count">0 Tiket</div>
            </div>
            <!-- Ticket Card 5 -->
            <div class="ticket-card">
              <img src="/assets/share_talk.svg" alt="Share & Talk">
              <div class="ticket-feature">Share & Talk</div>
              <div class="ticket-count">1 Tiket</div>
            </div>
            <!-- Ticket Card 6 -->
            <div class="ticket-card">
              <img src="/assets/missions_of_the_day.svg" alt="Missions of the Day">
              <div class="ticket-feature">Missions of the Day</div>
              <div class="ticket-count">2 Tiket</div>
            </div>
            <!-- Ticket Card 7 -->
            <div class="ticket-card">
              <img src="/assets/torch.svg" alt="Mental Tracker">
              <div class="ticket-feature">Mental Tracker</div>
              <div class="ticket-count">4 Tiket</div>
            </div>
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
            <form method="POST" action="#">
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
      </script>
</body>
</html>
