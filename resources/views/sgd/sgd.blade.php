<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Curhatorium | Support Group Discussion</title>
   <link rel="stylesheet" href="{{ asset('/css/sgd.css') }}">
   <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    @include('components.navbar')
    
    <h1 class="page-title">Support Group Discussion</h1>
   <!-- Search and Filter Bar -->
   <div class="search-filter-bar">
       <div class="container">
           <form method="GET" action="{{ route('sgd') }}" class="search-form">
               <div class="search-input-group">
                   <input type="text" name="search" placeholder="Search groups..." 
                          value="{{ request('search') }}" class="search-input">
                   <button type="submit" class="search-btn">
                       <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                           <circle cx="11" cy="11" r="8"></circle>
                           <path d="m21 21-4.35-4.35"></path>
                       </svg>
                   </button>
               </div>
               
               <div class="filter-controls">
                   <select name="category" class="filter-select">
                       <option value="all">All Categories</option>
                       @foreach($categories as $category)
                           <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                               {{ ucfirst(str_replace('-', ' ', $category)) }}
                           </option>
                       @endforeach
                   </select>
                   
                   <select name="sort" class="filter-select">
                       <option value="schedule">Sort by Schedule</option>
                       <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Sort by Title</option>
                       <option value="category" {{ request('sort') == 'category' ? 'selected' : '' }}>Sort by Category</option>
                   </select>
                   
                   <button type="submit" class="apply-btn">Apply</button>
                   <a href="{{ route('sgd') }}" class="clear-btn">Clear</a>
               </div>
           </form>
       </div>
   </div>

   <div class="container">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="main-content">
            <!-- Discussion Groups -->
            <div class="groups-section">
                <div class="section-header">
                    <div class="results-count">{{ $groups->count() }} group(s) found</div>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="/admin/sgd-groups/create" class="admin-create-button">
                                Create New Group
                            </a>
                        @endif
                    @endauth
                </div>
                
                <!-- Groups container - populated by Blade loop -->
                <div id="groups-container" class="groups-container">
                    @if($groups->count() > 0)
                        @foreach($groups as $group)
                            <div class="group-card">
                                <div class="card-header">
                                </div>
                                <div class="card-content">
                                    <h3 class="card-title">{{ $group->title }}</h3>
                                    <div class="card-meta">
                                        <span>{{ ucfirst(str_replace('-', ' ', $group->category)) }}</span>
                                        <span>Scheduled: {{ \Carbon\Carbon::parse($group->schedule)->format('M d, Y g:i A') }}</span>
                                    </div>
                                    <p class="card-text">{{ $group->topic }}</p>
                                    <form action="{{ route('group.join') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                                        <button type="submit" class="join-button">
                                            Join Group <span>➔</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="no-results">
                            <p>No groups found matching your criteria.</p>
                            @if(request('search') || request('category') || request('status'))
                                <a href="{{ route('sgd') }}" class="clear-link">Clear filters to see all groups</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>