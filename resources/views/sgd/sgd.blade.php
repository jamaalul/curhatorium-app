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
   <div class="container">
        <h1 class="page-title">Support Group Discussion</h1>

        <div class="main-content">
            <!-- Create Group Form -->
            <div class="form-container">
                <h2 class="form-title">Create New Group</h2>
                <form class="form" id="create-group-form">
                    <div class="form-group">
                        <input type="text" id="group-title" placeholder="Enter group title" class="form-input">
                    </div>
                    <div class="form-group">
                        <textarea id="group-topic" placeholder="Describe what your group will discuss" class="form-textarea"></textarea>
                    </div>
                    <div class="form-group">
                        <select id="group-category" class="form-input">
                           <option value="" disabled selected>Kategori</option>
                           <option value="mental-health">Mental Health</option>
                           <option value="education">Education</option>
                           <option value="career">Career</option>
                           <option value="relationships">Relationships</option>
                           <option value="other">Other</option>
                        </select>
                    </div>
                    <button type="submit" class="form-button">
                        Create Group <span>+</span>
                    </button>
                </form>
            </div>

            <!-- Discussion Groups -->
            <div class="groups-section">
                <div class="section-header">
                    <h2 class="section-title">Available Groups</h2>
                </div>
                
                <!-- Groups container - will be populated by JavaScript -->
                <div id="groups-container" class="groups-container">
                    <!-- Loading spinner will be shown here initially -->
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/sgd.js') }}"></script>
</body>
</html>