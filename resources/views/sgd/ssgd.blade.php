<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Curhatorium | Support Group Discussion</title>
    <link rel="stylesheet" href="{{ asset('css/sgd.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo-box">
                <img src="assets/support_group_discussion.svg" alt="sgd">
                <div class="logo-text">
                    <h3>Support</h3>
                    <h3>Group</h3>
                    <h3>Discussion</h3>
                </div>
            </div>
            <div class="make-group-form">
                <form action="{{ route('group.create') }}" method="POST">
                    @csrf
                    <input type="text" placeholder="Judul" name="title" class="title" required>
                    <textarea rows="4" type="text" placeholder="Topik" name="topic" class="topic" required></textarea>
                    <button type="submit" class="create-group-btn">Buat Grup <b>+</b></button>
                </form>
            </div>
        </div>
        <div class="groups-box">
            {{-- <div class="group-cards card-1 ofc">
                <div class="card-top">
                    <h3>Judul Grup</h3>
                    <p>Topik yang Dibahas di Grup</p>
                </div>
                <button>
                    Bergabung ->
                </button>
            </div>
            <div class="group-cards card-2 ofc">
                <div class="card-top">
                    <h3>Judul Grup</h3>
                    <p>Topik yang Dibahas di Grup</p>
                </div>
                <button>
                    Bergabung ->
                </button>
            </div>
            <div class="group-cards card-3">
                <div class="card-top">
                    <h3>Judul Grup</h3>
                    <p>Topik yang Dibahas di Grup</p>
                </div>
                <button>
                    Bergabung ->
                </button>
            </div>
            <div class="group-cards card-1">
                <div class="card-top">
                    <h3>Judul Grup</h3>
                    <p>Topik yang Dibahas di Grup</p>
                </div>
                <button>
                    Bergabung ->
                </button>
            </div> --}}
        </div>
    </div>

    <script src="js/sgd.js"></script>
</body>
</html>







{{-- <div class="groups-box">
    <!-- Discussion Groups -->
    <div class="groups-box">
        <!-- Group 1 -->
        <div class="group-card">
            <div class="card-header"></div>
                <div class="card-content">
                        <h3 class="card-title">Judul Grup Diskusi</h3>
                        <p class="card-text">Topik yang Dibicarakan di Grup Diskusi</p>
                        <button class="join-button">
                        Bergabung <span>➔</span>
                        </button>
            </div>
        </div>

        <!-- Group 2 -->
        <div class="group-card">
        <div class="card-header"></div>
        <div class="card-content">
            <h3 class="card-title">Judul Grup Diskusi</h3>
            <p class="card-text">Topik yang Dibicarakan di Grup Diskusi</p>
            <button class="join-button">
            Bergabung <span>➔</span>
            </button>
        </div>
        </div>

        <!-- Group 3 -->
        <div class="group-card">
        <div class="card-header"></div>
        <div class="card-content">
            <h3 class="card-title">Judul Grup Diskusi</h3>
            <p class="card-text">Topik yang Dibicarakan di Grup Diskusi</p>
            <button class="join-button">
            Bergabung <span>➔</span>
            </button>
        </div>
        </div>
    </div>
</div> --}}