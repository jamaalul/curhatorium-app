<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Syarat & Ketentuan | Curhatorium</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}">
  <style>
    :root {
      --primary-color: #8ecbcf;
      --primary-light: #9acbd0;
      --primary-dark: #7ab8bd;
      --secondary-color: #ffcd2d;
      --secondary-light: #ffd84d;
      --secondary-dark: #f0c020;
      --text-primary: #333333;
      --text-secondary: #595959;
      --text-tertiary: #6b7280;
      --bg-primary: #f5f2eb;
      --bg-secondary: #f3f4f6;
      --bg-tertiary: #e5e7eb;
      --white: #ffffff;
      --success: #10b981;
      --error: #ef4444;
      --warning: #f59e0b;
      --info: #3b82f6;
      --profile-bg: #222222;
      --border-radius-sm: 0.25rem;
      --border-radius: 0.5rem;
      --border-radius-lg: 0.75rem;
      --border-radius-xl: 1rem;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --transition: all 0.3s ease;
      --container-width: 900px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      font-family: "FigtreeReg", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
      max-width: var(--container-width);
      margin: 0 auto;
      padding: 2rem 1.5rem;
    }

    /* Page Header */
    .page-header {
      position: relative;
      overflow: hidden;
      padding: 3.5rem 0;
      margin-bottom: 3rem;
      text-align: center;
      border-radius: var(--border-radius-lg);
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: var(--white);
    }

    .page-header::before {
      content: "";
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
      pointer-events: none;
    }

    .page-header-content {
      position: relative;
      z-index: 1;
      padding: 0 2rem;
    }

    .page-header h1 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      font-family: "FigtreeBold", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-header .subtitle {
      font-size: 1.125rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }

    .updated-date {
      display: inline-block;
      background: rgba(255, 255, 255, 0.2);
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius);
      font-size: 0.875rem;
      margin-top: 1rem;
      backdrop-filter: blur(10px);
    }

    /* Main Content */
    .content-card {
      background: var(--white);
      border: 1px solid rgba(142, 203, 207, 0.1);
      border-radius: var(--border-radius-xl);
      padding: 2.5rem;
      box-shadow: var(--shadow-md);
      margin-bottom: 2rem;
    }

    .content-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      transition: var(--transition);
    }

    /* Section Styling */
    .section {
      margin-bottom: 2.5rem;
    }

    .section:last-child {
      margin-bottom: 0;
    }

    .section h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-family: "FigtreeBold", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .section h2::before {
      content: "";
      width: 4px;
      height: 1.5rem;
      background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-dark) 100%);
      border-radius: var(--border-radius-sm);
    }

    .section p {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 1rem;
      line-height: 1.7;
    }

    .section ul {
      padding-left: 1.5rem;
      margin-bottom: 1rem;
    }

    .section li {
      font-size: 1rem;
      color: var(--text-secondary);
      margin-bottom: 0.75rem;
      line-height: 1.6;
    }

    .section strong {
      color: var(--text-primary);
      font-weight: 600;
    }

    /* Contact Section */
    .contact-section {
      background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
      border-radius: var(--border-radius-lg);
      padding: 2rem;
      text-align: center;
      margin-top: 3rem;
    }

    .contact-section h2 {
      color: var(--text-primary);
      margin-bottom: 1rem;
      font-size: 1.25rem;
    }

    .contact-links {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
      margin-top: 1.5rem;
    }

    .contact-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background: var(--white);
      color: var(--text-primary);
      text-decoration: none;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      font-weight: 500;
    }

    .contact-link:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
      background: var(--primary-color);
      color: var(--white);
    }

    /* Footer */
    .footer {
      text-align: center;
      padding: 2rem 0;
      color: var(--text-tertiary);
      font-size: 0.875rem;
      border-top: 1px solid var(--bg-tertiary);
      margin-top: 3rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }

      .page-header {
        padding: 2.5rem 0;
        margin-bottom: 2rem;
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .page-header .subtitle {
        font-size: 1rem;
      }

      .content-card {
        padding: 1.5rem;
      }

      .section h2 {
        font-size: 1.25rem;
      }

      .contact-links {
        flex-direction: column;
        align-items: center;
      }

      .contact-link {
        width: 100%;
        max-width: 300px;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .page-header h1 {
        font-size: 1.75rem;
      }

      .content-card {
        padding: 1rem;
      }

      .section h2 {
        font-size: 1.125rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-content">
        <h1>Terms & Conditions</h1>
        <p class="subtitle">Syarat & Ketentuan Penggunaan Platform Curhatorium</p>
        <div class="updated-date">
          <strong>Terakhir diperbarui:</strong> 19 Juli 2025
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content-card">
      <div class="section">
        <h2>1. PENGANTAR UMUM</h2>
        <p>Selamat datang di Curhatorium!</p>
        <p>Dengan mengakses atau menggunakan layanan Curhatorium, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat & Ketentuan ini. Apabila Anda tidak setuju terhadap salah satu bagian dari ketentuan ini, mohon untuk tidak menggunakan layanan kami.</p>
      </div>

      <div class="section">
        <h2>2. DEFINISI</h2>
        <ul>
          <li><strong>Curhatorium</strong> adalah platform digital berbasis komunitas yang menyediakan layanan dukungan psikososial non-klinis, termasuk namun tidak terbatas pada: diskusi support group, chatbot reflektif, misi penguatan mental harian, dan pelacakan keseharian.</li>
          <li><strong>Pengguna</strong> adalah setiap individu yang mengakses, mendaftar, dan/atau menggunakan layanan Curhatorium.</li>
          <li><strong>Ranger</strong> adalah fasilitator peer-support yang telah melalui proses seleksi dan pelatihan internal Curhatorium.</li>
          <li><strong>Fitur</strong> merujuk pada seluruh layanan digital yang disediakan oleh Curhatorium, baik bersifat publik maupun terbatas.</li>
          <li><strong>Tim Pusat</strong> adalah entitas pengelola utama Curhatorium yang terdiri dari CEO, COO, CTO, CFO, dan CMO.</li>
        </ul>
      </div>

      <div class="section">
        <h2>3. KETENTUAN UMUM AKSES LAYANAN</h2>
        <ul>
          <li>Curhatorium dapat digunakan oleh pengguna berusia minimal 15 tahun.</li>
          <li>Untuk mengakses layanan tertentu, pengguna harus membuat akun dengan informasi yang akurat dan aktif (contoh: email).</li>
          <li>Pengguna bertanggung jawab atas aktivitas yang dilakukan melalui akun pribadinya.</li>
          <li>Curhatorium berhak membatasi, menangguhkan, atau menghapus akun apabila terdapat pelanggaran terhadap kebijakan ini.</li>
        </ul>
      </div>

      <div class="section">
        <h2>4. KARAKTERISTIK LAYANAN</h2>
        <p>Layanan yang tersedia di Curhatorium tidak bersifat klinis (kecuali untuk Share and Talk Profesional atau Chat dan Meet with Sanny's Aid), dan tidak ditujukan untuk menggantikan psikoterapi, diagnosis, atau intervensi medis profesional.</p>
        <p>Seluruh layanan berbasis prinsip Psychological First Aid dan peer-support, yang bersifat sukarela, berbasis komunitas, dan bersifat anonim.</p>
        <p>Curhatorium tidak menyediakan layanan darurat. Untuk kondisi krisis (misal: risiko bunuh diri, kekerasan domestik), pengguna disarankan menghubungi pihak profesional atau layanan gawat darurat terdekat.</p>
      </div>

      <div class="section">
        <h2>5. HAK DAN KEWAJIBAN PENGGUNA</h2>
        <p><strong>Pengguna berhak untuk:</strong></p>
        <ul>
          <li>Mengakses fitur secara anonim dan aman.</li>
          <li>Menggunakan layanan sesuai kebutuhan tanpa diskriminasi.</li>
          <li>Menyampaikan kritik dan saran secara bertanggung jawab.</li>
          <li>Meminta penghapusan akun dan data pribadi sewaktu-waktu.</li>
        </ul>
        <p><strong>Pengguna berkewajiban untuk:</strong></p>
        <ul>
          <li>Menghormati Ranger dan pengguna lain.</li>
          <li>Tidak menyebarkan konten yang mengandung unsur SARA, kekerasan, pornografi, ujaran kebencian, atau ancaman.</li>
          <li>Tidak melakukan upaya manipulasi, eksploitasi, atau pelacakan terhadap pengguna lain.</li>
          <li>Tidak menyebarluaskan hasil diskusi atau konten dalam platform ke pihak eksternal tanpa izin.</li>
        </ul>
      </div>

      <div class="section">
        <h2>6. PERILAKU DALAM LAYANAN SUPPORT GROUP</h2>
        <ul>
          <li>Setiap sesi diskusi dijalankan secara anonim dan difasilitasi oleh Ranger.</li>
          <li>Tidak diperbolehkan menyebutkan nama asli, akun media sosial, nomor telepon, atau data identitas lainnya selama diskusi berlangsung.</li>
          <li>Curhatorium memiliki kebijakan nol toleransi terhadap perilaku manipulatif, meremehkan, atau mengarahkan peserta secara tidak etis.</li>
          <li>Pelanggaran terhadap prinsip ini dapat menyebabkan pemblokiran permanen.</li>
        </ul>
      </div>

      <div class="section">
        <h2>7. KEBIJAKAN PENGGUNAAN FITUR LAINNYA</h2>
        <ul>
          <li>Fitur chatbot (Ment-AI) hanya menyediakan dukungan reflektif dan validasi umum, tanpa memberikan diagnosis atau keputusan personal.</li>
          <li>Fitur tracker dan misi harian digunakan untuk memantau kondisi emosional dan kebiasaan positif pengguna secara mandiri.</li>
          <li>Setiap data dari fitur ini bersifat pribadi, tidak dapat diakses oleh pengguna lain, dan tidak digunakan untuk iklan atau penargetan komersial.</li>
        </ul>
      </div>

      <div class="section">
        <h2>8. KEAMANAN SISTEM DAN DATA PENGGUNA</h2>
        <ul>
          <li>Seluruh data disimpan secara terenkripsi dan hanya dapat diakses oleh tim teknis terbatas.</li>
          <li>Curhatorium menerapkan sistem keamanan dengan autentikasi dan kontrol perangkat.</li>
          <li>Kami tidak membagikan data Anda kepada pihak ketiga tanpa persetujuan eksplisit, kecuali diwajibkan oleh hukum atau dalam kondisi darurat yang membahayakan nyawa.</li>
        </ul>
      </div>

      <div class="section">
        <h2>9. PEMBATASAN TANGGUNG JAWAB</h2>
        <ul>
          <li>Curhatorium tidak bertanggung jawab atas keputusan atau tindakan yang diambil pengguna berdasarkan informasi atau diskusi dalam platform.</li>
          <li>Kami tidak menjamin bahwa layanan akan selalu bebas dari gangguan teknis, karena mungkin dilakukan pemeliharaan atau pembaruan sistem.</li>
          <li>Segala konsekuensi dari penyalahgunaan layanan di luar batas kontrol Curhatorium menjadi tanggung jawab pribadi pengguna.</li>
        </ul>
      </div>

      <div class="section">
        <h2>10. PEMBARUAN SYARAT & KETENTUAN</h2>
        <p>Curhatorium berhak untuk memperbarui isi Terms & Conditions ini dari waktu ke waktu. Setiap perubahan akan diinformasikan kepada pengguna melalui platform, dan versi terbaru akan berlaku efektif sejak tanggal yang tercantum.</p>
      </div>

      <div class="section">
        <h2>11. HUKUM YANG BERLAKU</h2>
        <p>Dokumen ini tunduk dan diatur berdasarkan hukum yang berlaku di Republik Indonesia, termasuk UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi dan UU ITE.</p>
      </div>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
      <h2>KONTAK RESMI</h2>
      <p>Untuk pertanyaan atau klarifikasi, silakan hubungi kami:</p>
      <div class="contact-links">
        <a href="mailto:curhatorium@gmail.com" class="contact-link">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
          </svg>
          hello@curhatorium.com
        </a>
        <a href="https://instagram.com/curhatorium_" target="_blank" class="contact-link">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.556 15.99 5.828 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
          </svg>
          @curhatorium_
        </a>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      &copy; 2025 Curhatorium. Seluruh hak cipta dilindungi undang-undang.
    </div>
  </div>
</body>
</html>
