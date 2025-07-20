<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/features.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main/profile.css') }}">
</head>
<body>
  <main class="profile-main">
    <section class="profile-section">
      <h2 class="profile-title">My Profile</h2>
      <!-- Profile Overview -->
      <div class="profile-overview">
        <div class="profile-pic-container">
          <img id="profilePicPreview" src="https://via.placeholder.com/120" alt="Profile" class="profile-pic" onerror=`this.onerror=null;this.src="https://via.placeholder.com/120";`>
          <label class="change-pic-label">
            Change
            <input id="profilePicInput" type="file" class="hidden" accept="image/*" style="display:none;">
          </label>
        </div>
        <div class="profile-username">@johndoe</div>
        <div class="profile-email">john@example.com</div>
        <!-- XP and Badge -->
        <div class="xp-badge-box">
          <div class="badge-box-profile">
            <img class="badge-logo-profile" src="/assets/kindle.svg" alt="badge">
            <span class="badge-text-profile">Kindle</span>
          </div>
          <span class="xp-profile" id="xp-profile">1250 XP</span>
        </div>
        <!-- Tickets Section -->
        <div class="tickets-section">
          <div class="tickets-title">Your Tickets</div>
          <div class="tickets-list">
            <!-- Ticket Card 1 -->
            <div class="ticket-card">
              <img src="/assets/support_group_discussion.svg" alt="SGD">
              <div class="ticket-feature">SGD Group</div>
              <div class="ticket-count">2 Tickets</div>
            </div>
            <!-- Ticket Card 2 -->
            <div class="ticket-card">
              <img src="/assets/deep_cards.svg" alt="Deep Cards">
              <div class="ticket-feature">Deep Cards</div>
              <div class="ticket-count">1 Ticket</div>
            </div>
            <!-- Ticket Card 3 -->
            <div class="ticket-card">
              <img src="/assets/mental_support_chatbot.svg" alt="Ment-AI">
              <div class="ticket-feature">Ment-AI</div>
              <div class="ticket-count">3 Tickets</div>
            </div>
            <!-- Ticket Card 4 -->
            <div class="ticket-card">
              <img src="/assets/mental_health_test.svg" alt="Mental Test">
              <div class="ticket-feature">Mental Test</div>
              <div class="ticket-count">0 Ticket</div>
            </div>
            <!-- Ticket Card 5 -->
            <div class="ticket-card">
              <img src="/assets/share_talk.svg" alt="Share & Talk">
              <div class="ticket-feature">Share & Talk</div>
              <div class="ticket-count">1 Ticket</div>
            </div>
            <!-- Ticket Card 6 -->
            <div class="ticket-card">
              <img src="/assets/missions_of_the_day.svg" alt="Missions of the Day">
              <div class="ticket-feature">Missions of the Day</div>
              <div class="ticket-count">2 Tickets</div>
            </div>
            <!-- Ticket Card 7 -->
            <div class="ticket-card">
              <img src="/assets/torch.svg" alt="Mental Tracker">
              <div class="ticket-feature">Mental Tracker</div>
              <div class="ticket-count">4 Tickets</div>
            </div>
          </div>
        </div>
      </div>
      
          <!-- Profile Edit Form -->
          <div style="background:#fff;padding:28px 24px 18px 24px;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:32px;">
            <form method="POST" action="#" style="display:flex;flex-direction:column;gap:18px;width:100%;">
              <div style="font-family:FigtreeBold;font-size:1.1em;color:#333;margin-bottom:4px;">Edit Profile</div>
              <div>
                <label for="username" class="block font-semibold mb-1">Username</label>
                <input id="username" name="username" type="text" value="johndoe" required class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input id="email" name="email" type="email" value="john@example.com" required class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <button type="submit" class="profile-save" style="background:#48A6A6;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Save Changes</button>
            </form>
          </div>
      
          <!-- Change Password Section -->
          <div style="background:#fff;padding:28px 24px 18px 24px;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:32px;">
            <form method="POST" action="#" style="display:flex;flex-direction:column;gap:18px;width:100%;">
              <div style="font-family:FigtreeBold;font-size:1.1em;color:#333;margin-bottom:4px;">Change Password</div>
              <div>
                <label for="current_password" class="block font-semibold mb-1">Current Password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="password" class="block font-semibold mb-1">New Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <div>
                <label for="password_confirmation" class="block font-semibold mb-1">Confirm New Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full p-2 border rounded" style="width:100%;padding:12px 14px;border-radius:8px;border:1px solid #e5e7eb;font-size:1em;">
              </div>
              <button type="submit" class="profile-save" style="background:#48A6A6;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Change Password</button>
            </form>
          </div>
      
          <!-- Account Actions -->
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:24px;">
            <form method="POST" action="#">
              <button type="submit" class="profile-logout" style="background:#e57373;color:#fff;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:none;transition:background 0.2s;">Logout</button>
            </form>
            <form method="POST" action="#" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
              <button type="submit" style="background:#fff;color:#e57373;padding:12px 28px;border-radius:8px;font-weight:600;font-size:1em;border:1px solid #e57373;transition:background 0.2s;">Delete Account</button>
            </form>
          </div>
        </section>
      </main>
      @include('components.footer')
      
      <script>
        // Profile picture preview
        const input = document.getElementById('profilePicInput');
        const preview = document.getElementById('profilePicPreview');
        if(input && preview) {
          input.addEventListener('change', () => {
            const file = input.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = e => preview.src = e.target.result;
              reader.readAsDataURL(file);
            }
          });
        }
        // XP and Badge logic (placeholder, set only once)
        const xpElement = document.getElementById('xp-profile');
        const badgeLogoElement = document.querySelector('.badge-logo-profile');
        const badgeTextElement = document.querySelector('.badge-text-profile');
        // In the future, replace these with real user data
        const placeholderXP = 1250;
        const placeholderBadgeName = 'Kindle';
        const placeholderBadgeImage = '/assets/kindle.svg';
        xpElement.textContent = `${placeholderXP} XP`;
        badgeTextElement.textContent = placeholderBadgeName;
        badgeLogoElement.src = placeholderBadgeImage;
      </script>
</body>
</html>
