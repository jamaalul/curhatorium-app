<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fitur & Layanan - Curhatorium</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

                 body {
             font-family: FigtreeReg;
             background-color: #f5f1eb;
             color: #1a1a1a;
             line-height: 1.6;
             font-size: 16px;
         }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Left Sidebar */
                 .sidebar {
             width: 280px;
             background: #ffffff;
             border-right: 1px solid #e5e7eb;
             overflow-y: auto;
             position: fixed;
             height: 100vh;
             left: 0;
             top: 0;
         }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

                 .sidebar-title {
             display: flex;
             align-items: center;
             gap: 12px;
             font-size: 18px;
             font-family: FigtreeBold;
             font-weight: 400;
             color: #1a1a1a;
         }

                                   .sidebar-icon {
              width: 21.6px;
              height: 21.6px;
              color: #1a1a1a;
          }

        .nav-menu {
            padding: 16px 0;
        }

        .nav-item {
            list-style: none;
        }

                                   .nav-link {
              display: block;
              padding: 12px 24px;
              color: #1a1a1a;
              text-decoration: none;
              font-size: 14px;
              font-family: FigtreeReg;
              transition: all 0.2s ease;
              cursor: pointer;
          }

                 .nav-link:hover {
             background: #f5f1eb;
             color: #1a1a1a;
         }

                 .nav-link.active {
             background: #ffd84d7b;
             color: #1a1a1a;
             border-right: 3px solid #ffcd2d;
         }

        

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            display: flex;
        }

        .content-area {
            flex: 1;
            padding: 32px 48px;
            max-width: 800px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #6b7280;
        }

                 .breadcrumb a {
             color: #888;
             text-decoration: none;
             transition: color 0.2s ease;
         }

                                   .breadcrumb a:hover {
              color: #1a1a1a;
          }

                   .back-button {
              display: inline-flex;
              align-items: center;
              gap: 6px;
              padding: 6px 12px;
              background: transparent;
              color: #888;
              text-decoration: none;
              border-radius: 4px;
              font-weight: 400;
              font-size: 13px;
              font-family: FigtreeReg;
              transition: all 0.2s ease;
              margin-bottom: 16px;
          }

                   .back-button:hover {
              background: #f5f1eb;
              color: #1a1a1a;
          }

                   .page-title {
             font-size: 32px;
             font-family: FigtreeBold;
             font-weight: 400;
             color: #1a1a1a;
             margin-bottom: 16px;
         }

                 .page-description {
             font-size: 16px;
             color: #888;
             margin-bottom: 32px;
             line-height: 1.6;
         }

        .document-section {
            margin-bottom: 32px;
        }

        .document-section h2 {
            font-size: 24px;
            font-family: FigtreeBold;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .document-section p {
            font-size: 16px;
            color: #1a1a1a;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .document-section ul {
            list-style: none;
            padding: 0;
            margin: 0 0 24px 0;
        }

        .document-section li {
            font-size: 16px;
            color: #1a1a1a;
            line-height: 1.6;
            margin-bottom: 12px;
            padding-left: 20px;
            position: relative;
        }

        .document-section li:before {
            content: "•";
            color: #ffcd2d;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            font-family: FigtreeReg;
            transition: all 0.2s ease;
        }

        .cta-button:hover {
            background: #888;
            transform: translateY(-1px);
        }

        /* Right Sidebar */
        .right-sidebar {
            width: 280px;
            background: #ffffff;
            border-left: 1px solid #e5e7eb;
            padding: 24px;
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-section {
            margin-bottom: 32px;
        }

        .sidebar-section h3 {
            font-size: 14px;
            font-family: FigtreeBold;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            color: #888;
            text-decoration: none;
            font-size: 14px;
            font-family: FigtreeReg;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            color: #1a1a1a;
        }

                                   .sidebar-link.active {
              color: #888;
              font-weight: normal;
          }

        

        /* Feature Content */
        .feature-content {
            display: none;
        }

        .feature-content.active {
            display: block;
        }

        @media (max-width: 1200px) {
            .right-sidebar {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-area {
                padding: 24px 16px;
            }

            .page-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
              <div class="layout">
         <!-- Left Sidebar -->
         <div class="sidebar">
             <div class="sidebar-header">
                 <a href="/dashboard" class="back-button">
                    ← Kembali
                 </a>
                 <div class="sidebar-title">
                    Dokumentasi Fitur
                 </div>
             </div>
            
                         <ul class="nav-menu">
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Mental Health Test' ? 'active' : '' }}" data-feature="mental-health-test">
                         Mental Health Test
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Share and Talk' ? 'active' : '' }}" data-feature="share-and-talk">
                         Share and Talk
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Missions of The Day Easy' ? 'active' : '' }}" data-feature="missions">
                         Missions of the Day
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Ment-AI' ? 'active' : '' }}" data-feature="ment-ai">
                         Ment-AI
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Deep Cards' ? 'active' : '' }}" data-feature="deep-cards">
                         Deep Cards
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Support Group Discussion' ? 'active' : '' }}" data-feature="support-group">
                         Support Group Discussion
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="#" class="nav-link {{ $feature['title'] == 'Mood and Productivity Tracker' ? 'active' : '' }}" data-feature="mood-tracker">
                         Mood and Productivity Tracker
                     </a>
                 </li>
             </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-area">
                                 <div class="breadcrumb">
                     <a href="javascript:history.back()">Back</a>
                     <span>/</span>
                     <span>Fitur & Layanan</span>
                     <span>/</span>
                     <span id="current-feature">{{ $feature['title'] }}</span>
                 </div>

                                                   <!-- Mental Health Test Content -->
                  <div class="feature-content {{ $feature['title'] == 'Mental Health Test' ? 'active' : '' }}" id="mental-health-test">
                     <h1 class="page-title">Mental Health Test</h1>
                     
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Tes reflektif berbasis Mental Health Continuum – Short Form (MHC-SF), membantu kamu mengenali kesejahteraan emosional, psikologis, dan sosialmu. Hasilnya akan memberi gambaran utuh tentang kondisi mentalmu. Bukan untuk menilai, tapi untuk memahami.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin tahu kondisi mentalmu saat ini secara utuh.</li>
                             <li>Ingin mendapatkan saran yang tepat berdasarkan hasil tes.</li>
                             <li>Butuh panduan awal untuk menentukan langkah selanjutnya.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Kenali dirimu, bukan untuk menilai, tapi untuk lebih merawat dan memahami diri.</p>
                         <a href="/mental-test" class="cta-button">Mulai Tes</a>
                     </div>
                 </div>

                                   <!-- Share and Talk Content -->
                  <div class="feature-content {{ $feature['title'] == 'Share and Talk' ? 'active' : '' }}" id="share-and-talk">
                     <h1 class="page-title">Share and Talk</h1>
                   
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Ruang cerita personal yang bisa kamu pilih sendiri, bareng Ranger (orang terlatih untuk memberikan peer-support) atau psikolog profesional. Cerita bisa melalui chat atau sesi tatap muka daring. Aman, tanpa nama, tanpa paksaan.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin bercerita dengan nyaman dan tanpa tekanan.</li>
                             <li>Butuh tempat aman untuk melepaskan beban pikiran.</li>
                             <li>Ingin mendapat respons empatik atau panduan dari pendengar yang terlatih.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Cerita itu butuh ruang. Temukan ruangmu, dan biarkan dirimu didengar.</p>
                         <a href="/share-and-talk" class="cta-button">Mulai Bercerita</a>
                     </div>
                 </div>

                                   <!-- Missions Content -->
                  <div class="feature-content {{ $feature['title'] == 'Missions of The Day Easy' ? 'active' : '' }}" id="missions">
                     <h1 class="page-title">Missions of the Day</h1>
                   
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Misi harian sederhana yang membantumu terhubung kembali dengan diri sendiri. Setiap misi bisa berupa refleksi, tindakan kecil, atau latihan ringan. Pilih level yang sesuai dengan ritme harimu.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin merawat diri tanpa merasa terbebani.</li>
                             <li>Ingin membentuk kebiasaan sehat secara perlahan.</li>
                             <li>Butuh dorongan kecil untuk tetap hadir dan bertumbuh.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Satu langkah kecil hari ini cukup. Pelan, tapi tetap berarti.</p>
                         <a href="/missions" class="cta-button">Lihat Misi</a>
                     </div>
                 </div>

                                   <!-- Ment-AI Content -->
                  <div class="feature-content {{ $feature['title'] == 'Ment-AI' ? 'active' : '' }}" id="ment-ai">
                     <h1 class="page-title">Ment-AI</h1>
                    
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Sanny AI yang siap menemani kapan pun kamu butuh. Bisa membantu refleksi, latihan napas, atau sekadar menemani saat hari terasa berat.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin ditemani tanpa harus bicara dengan orang lain.</li>
                             <li>Butuh afirmasi, pengingat, atau refleksi ringan.</li>
                             <li>Ingin merasa terhubung saat sedang sendiri.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Tak harus selalu bicara pada orang. Kadang, kehadiran virtual pun bisa menguatkan.</p>
                         <a href="/chatbot" class="cta-button">Mulai Chat</a>
                     </div>
                 </div>

                                   <!-- Deep Cards Content -->
                  <div class="feature-content {{ $feature['title'] == 'Deep Cards' ? 'active' : '' }}" id="deep-cards">
                     <h1 class="page-title">Deep Cards</h1>
                
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Kartu refleksi yang berisi pertanyaan mendalam. Tarik satu kartu, dan tuliskan isi hatimu. Tidak ada jawaban benar atau salah, hanya ruang untuk jujur pada diri.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin menulis tapi tak tahu harus mulai dari mana.</li>
                             <li>Butuh alat sederhana untuk menyelami pikiran dan perasaan.</li>
                             <li>Ingin merasa lebih dekat dengan dirimu sendiri.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Biar kata sulit diucap, tuliskan saja. Itu juga bentuk keberanian.</p>
                         <a href="/deep-cards" class="cta-button">Tarik Kartu</a>
                     </div>
                 </div>

                                   <!-- Support Group Content -->
                  <div class="feature-content {{ $feature['title'] == 'Support Group Discussion' ? 'active' : '' }}" id="support-group">
                     <h1 class="page-title">Support Group Discussion</h1>
                   
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Diskusi reflektif dalam kelompok kecil, dipandu oleh Ranger. Kamu bisa cerita atau cukup mendengarkan. Semua berjalan anonim, semua saling memahami.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin merasa terhubung dengan orang lain yang punya pengalaman serupa.</li>
                             <li>Ingin menyadari bahwa kamu tidak sendiri.</li>
                             <li>Nyaman di ruang kolektif yang hangat, bukan sesi personal.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Kamu tidak sendiri. Ada suara lain yang ingin memahami dan menemanimu.</p>
                         <a href="/sgd" class="cta-button">Gabung Grup</a>
                     </div>
                 </div>

                                   <!-- Mood Tracker Content -->
                  <div class="feature-content {{ $feature['title'] == 'Mood and Productivity Tracker' ? 'active' : '' }}" id="mood-tracker">
                     <h1 class="page-title">Mood and Productivity Tracker</h1>
                   
                     <div class="document-section">
                         <h2>Apa Itu?</h2>
                         <p>Alat sederhana berbasis Positive and Negative Affect Schedule (PANAS) untuk mencatat suasana hati dan produktivitas harian. Bantu kamu mengenali pola emosimu dari waktu ke waktu. Refleksi kecil yang bisa berdampak besar.</p>
                     </div>

                     <div class="document-section">
                         <h2>Kenapa Pilih Fitur Ini?</h2>
                         <ul>
                             <li>Kamu ingin tahu bagaimana perasaanmu bergerak setiap hari melalui grafik terukur.</li>
                             <li>Ingin mengenali momen saat kamu butuh istirahat atau dukungan.</li>
                             <li>Butuh ruang untuk jujur pada diri, tanpa penilaian dari siapa pun.</li>
                         </ul>
                     </div>

                     <div class="document-section">
                         <h2>Mulai Sekarang</h2>
                         <p>Satu menit untuk jujur pada diri sendiri bisa jadi langkah awal pulih.</p>
                         <a href="/tracker" class="cta-button">Mulai Tracking</a>
                     </div>
                 </div>
            </div>

            <!-- Right Sidebar -->
            <div class="right-sidebar">
                <div class="sidebar-section">
                    <h3>Di halaman ini</h3>
                    <p class="sidebar-link" data-section="what-is">Deskripsi</p>
                    <p class="sidebar-link" data-section="why-choose">Kenapa Pilih Fitur Ini</p>
                    <p class="sidebar-link" data-section="get-started">Mulai Sekarang</p>
                </div>
             </div>
        </div>
    </div>

    <script>
        // Feature navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all nav links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Hide all feature content
                document.querySelectorAll('.feature-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show selected feature content
                const featureId = this.getAttribute('data-feature');
                document.getElementById(featureId).classList.add('active');
                
                // Update breadcrumb
                document.getElementById('current-feature').textContent = this.textContent.trim();
            });
        });
    </script>
</body>
</html> 