<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Professional Dashboard - {{ $professional->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/professional-dashboard.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8fafc;
            font-family: FigtreeReg;
            color: #374151;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .page-title {
            font-family: FigtreeBold;
            font-size: 1.5rem;
            color: #111827;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-family: FigtreeBold;
            font-size: 0.875rem;
            color: #111827;
        }

        .user-role {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* Content Area */
        .content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-family: FigtreeBold;
            font-size: 0.875rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-family: FigtreeBold;
            font-size: 2rem;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.875rem;
            color: #059669;
        }

        .status-indicator {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            display: inline-block;
        }

        .status-online {
            background-color: #10b981;
        }

        .status-offline {
            background-color: #6b7280;
        }

        .status-busy {
            background-color: #f59e0b;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .card-title {
            font-family: FigtreeBold;
            font-size: 1.125rem;
            color: #111827;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-family: FigtreeBold;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .radio-group {
            display: flex;
            gap: 1.5rem;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .radio-input {
            width: 1rem;
            height: 1rem;
            accent-color: #3b82f6;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-family: FigtreeBold;
            font-size: 0.875rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-family: FigtreeBold;
            font-size: 0.75rem;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }

        .table tr:hover {
            background: #f9fafb;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-family: FigtreeBold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-gray {
            background: #f3f4f6;
            color: #374151;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Grid Layout */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .top-bar {
                padding: 1rem;
            }
        }

        /* Mobile menu toggle */
        .mobile-menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="flex items-center gap-4">
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <h1 class="page-title">Dashboard Fasilitator</h1>
                </div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-name">{{ $professional->name }}</div>
                        <div class="user-role">{{ $professional->title }}</div>
                    </div>
                    <div class="user-avatar">
                        @if($professional->avatar)
                            <img src="{{ asset('storage/' . $professional->avatar) }}" alt="{{ $professional->name }}" style="width:100%;height:100%;object-fit:cover;display:block;border-radius:50%;" />
                        @else
                            <svg width="32" height="32" fill="currentColor" viewBox="0 0 20 20" style="display:block;margin:auto;">
                                <circle cx="10" cy="10" r="10" fill="#e5e7eb"/>
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" fill="#6b7280"/>
                            </svg>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('professional.dashboard.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Current Status</div>
                            <div class="status-indicator 
                                @if($professional->getEffectiveAvailability() === 'online') status-online
                                @elseif($professional->getEffectiveAvailability() === 'busy') status-busy
                                @else status-offline @endif">
                            </div>
                        </div>
                        <div class="stat-value">{{ $professional->getEffectiveAvailabilityText() }}</div>
                        <div class="stat-change">Available for sessions</div>
                    </div>

                    <!-- Removed Total Sessions stat card -->

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Professional Type</div>
                        </div>
                        <div class="stat-value">
                            @if($professional->type === 'psychiatrist')
                                Psikolog
                            @else
                                Ranger
                            @endif
                        </div>
                        <div class="stat-change">{{ $professional->title }}</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">WhatsApp</div>
                        </div>
                        <div class="stat-value">{{ $professional->whatsapp_number }}</div>
                        <div class="stat-change">Contact number</div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid-2">
                    <!-- Availability Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Availability Settings</h2>
                        </div>
                        <div class="card-body">
                            <form id="availabilityForm">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="radio-group">
                                        <label class="radio-item">
                                            <input type="radio" name="availability" value="online" 
                                                {{ $professional->availability === 'online' ? 'checked' : '' }}
                                                class="radio-input">
                                            <span>Online</span>
                                        </label>
                                        <label class="radio-item">
                                            <input type="radio" name="availability" value="offline" 
                                                {{ $professional->availability === 'offline' ? 'checked' : '' }}
                                                class="radio-input">
                                            <span>Offline</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Availability Message</label>
                                    <textarea name="availabilityText" 
                                        class="form-input form-textarea"
                                        placeholder="Enter your availability message...">{{ $professional->availabilityText }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-up" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1z"/>
                                        <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708z"/>
                                    </svg>
                                    Update Availability
                                </button>
                            </form>

                            <div id="updateMessage" class="mt-4 hidden"></div>
                        </div>
                    </div>

                    <!-- Password Change -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Change Password</h2>
                        </div>
                        <div class="card-body">
                            <form id="passwordForm">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" required
                                        class="form-input"
                                        placeholder="Enter your current password">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" required
                                        class="form-input"
                                        placeholder="Enter new password (minimum 8 characters)">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" required
                                        class="form-input"
                                        placeholder="Confirm new password">
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Change Password
                                </button>
                            </form>

                            <div id="passwordMessage" class="mt-4 hidden"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Sessions -->
                @if(isset($recentSessions) && $recentSessions->count() > 0)
                <div class="card mt-6">
                    <div class="card-header">
                        <h2 class="card-title">Recent Sessions</h2>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSessions as $session)
                                    <tr>
                                        <td>{{ $session->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ ucfirst($session->type) }}</td>
                                        <td>
                                            <span class="badge
                                                @if($session->status === 'active') badge-success
                                                @elseif($session->status === 'pending') badge-warning
                                                @else badge-gray @endif">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($session->start && $session->end)
                                                {{ $session->start->diffInMinutes($session->end) }} min
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        // Availability form
        document.getElementById('availabilityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                availability: formData.get('availability'),
                availabilityText: formData.get('availabilityText')
            };

            fetch(`/professional/{{ $professional->id }}/update-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('updateMessage');
                const messageText = document.getElementById('messageText');
                
                messageDiv.classList.remove('hidden');
                messageDiv.className = data.success ? 
                    'alert alert-success' : 
                    'alert alert-error';
                
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                
                // Reload page after 2 seconds to show updated status
                if (data.success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('updateMessage');
                
                messageDiv.classList.remove('hidden');
                messageDiv.className = 'alert alert-error';
                messageDiv.innerHTML = '<p>An error occurred while updating availability.</p>';
            });
        });

        // Password form
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                current_password: formData.get('current_password'),
                new_password: formData.get('new_password'),
                new_password_confirmation: formData.get('new_password_confirmation')
            };

            fetch(`/professional/{{ $professional->id }}/change-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('passwordMessage');
                
                messageDiv.classList.remove('hidden');
                messageDiv.className = data.success ? 
                    'alert alert-success' : 
                    'alert alert-error';
                
                messageDiv.innerHTML = `<p>${data.message}</p>`;
                
                // Clear form on success
                if (data.success) {
                    document.getElementById('passwordForm').reset();
                    setTimeout(() => {
                        messageDiv.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('passwordMessage');
                
                messageDiv.classList.remove('hidden');
                messageDiv.className = 'alert alert-error';
                messageDiv.innerHTML = '<p>An error occurred while changing password.</p>';
            });
        });
    </script>
</body>
</html> 