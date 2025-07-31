<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kebijakan Privasi | Curhatorium</title>
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

    /* Login Button Section */
    .login-section {
      background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
      border-radius: var(--border-radius-lg);
      padding: 2rem;
      text-align: center;
      margin-top: 2rem;
    }

    .login-section h2 {
      color: var(--text-primary);
      margin-bottom: 1rem;
      font-size: 1.25rem;
    }

    .login-section p {
      color: var(--text-secondary);
      margin-bottom: 1.5rem;
    }

    .login-btn {
      display: inline-flex;
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
      font-size: 1.125rem;
    }

    .login-btn:hover {
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
        <h1>Kebijakan Privasi</h1>
        <p class="subtitle">Bagaimana Curhatorium melindungi dan mengelola data pribadi Anda</p>
        <div class="updated-date">
          <strong>Terakhir diperbarui:</strong> 19 Juli 2025
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content-card">
      <div class="section">
        <h2>1. PENGANTAR</h2>
        <p>Curhatorium berkomitmen untuk melindungi privasi dan data pribadi seluruh pengguna platform. Dokumen ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi yang Anda berikan saat menggunakan layanan Curhatorium. Dengan menggunakan platform ini, Anda menyatakan telah membaca, memahami, dan menyetujui isi kebijakan privasi ini.</p>
      </div>

      <div class="section">
        <h2>2. RUANG LINGKUP KEBIJAKAN</h2>
        <p>Kebijakan ini berlaku untuk seluruh pengguna Curhatorium, baik yang hanya mengakses informasi maupun yang menggunakan seluruh layanan secara aktif, termasuk Support Group, Chatbot, Tracker, dan fitur lain di dalam platform.</p>
      </div>

      <div class="section">
        <h2>3. DATA APA SAJA YANG KAMI KUMPULKAN</h2>
        <p>Curhatorium hanya mengumpulkan data seperlunya, terbatas, dan relevan untuk operasional non-komersial dan peningkatan layanan. Data yang dikumpulkan antara lain:</p>
        <p><strong>a. Data Identifikasi Minimal</strong></p>
        <ul>
          <li>Alamat email (dihash untuk tujuan autentikasi dan verifikasi akun)</li>
          <li>Username/alias (bukan nama asli)</li>
        </ul>
        <p><strong>b. Data Aktivitas Pengguna</strong></p>
        <ul>
          <li>Riwayat penggunaan fitur (misalnya: sesi Support Group yang diikuti, progress tracker, aktivitas misi harian)</li>
          <li>Waktu dan durasi penggunaan layanan</li>
        </ul>
        <p><strong>c. Data Opsional</strong></p>
        <ul>
          <li>Hasil tes kesehatan mental (screening non-diagnostik, jika diisi secara sukarela)</li>
          <li>Feedback atau rating pasca diskusi</li>
        </ul>
        <p><strong>d. Data Teknis</strong></p>
        <ul>
          <li>IP address dan jenis perangkat/browser (untuk deteksi keamanan dan statistik internal)</li>
          <li>Cookies dan session ID</li>
        </ul>
        <p><strong>Curhatorium tidak mengumpulkan:</strong></p>
        <ul>
          <li>Nama asli</li>
          <li>Nomor telepon</li>
          <li>Alamat rumah</li>
          <li>Kontak darurat</li>
          <li>Informasi medis resmi</li>
        </ul>
      </div>

      <div class="section">
        <h2>4. TUJUAN PENGGUNAAN DATA</h2>
        <p>Data Anda digunakan untuk:</p>
        <ul>
          <li>Memastikan layanan berjalan dengan aman dan optimal</li>
          <li>Personalisasi pengalaman pengguna (misalnya: penyesuaian rekomendasi)</li>
          <li>Monitoring dan evaluasi kualitas layanan secara agregat dan anonim</li>
          <li>Mendeteksi pelanggaran atau aktivitas yang mencurigakan</li>
          <li>Memfasilitasi respon darurat (dalam kondisi ekstrem dan sangat terbatas)</li>
        </ul>
      </div>

      <div class="section">
        <h2>5. DASAR HUKUM PENGOLAHAN DATA</h2>
        <p>Pengolahan data dilakukan berdasarkan:</p>
        <ul>
          <li>Persetujuan eksplisit pengguna saat mendaftar atau menggunakan layanan</li>
          <li>Kebutuhan pelaksanaan layanan yang diminta oleh pengguna</li>
          <li>Kepentingan sah Curhatorium dalam menjaga keamanan dan mutu platform</li>
          <li>Kewajiban hukum jika diminta oleh otoritas yang berwenang</li>
        </ul>
      </div>

      <div class="section">
        <h2>6. PENYIMPANAN DAN PERLINDUNGAN DATA</h2>
        <ul>
          <li>Semua data disimpan dalam server terenkripsi dan terlindungi dengan standar keamanan tinggi (AES 256-bit / TLS 1.3).</li>
          <li>Akses internal terhadap data pengguna sangat terbatas, hanya diberikan kepada tim teknis yang ditunjuk dengan autentikasi ganda (2FA).</li>
          <li>Backup data dilakukan secara periodik dan disimpan secara terpisah untuk mitigasi kehilangan data.</li>
        </ul>
      </div>

      <div class="section">
        <h2>7. PELAKSANAAN PRINSIP ANONIMITAS</h2>
        <p>Curhatorium menjunjung tinggi prinsip anonimitas sebagai landasan etika utama:</p>
        <ul>
          <li>Seluruh aktivitas pengguna di ruang diskusi tidak dapat dilacak ke identitas asli.</li>
          <li>Ranger dan pengguna tidak memiliki akses terhadap data identifikasi satu sama lain.</li>
          <li>Tidak ada fitur pencarian atau pelacakan berbasis nama, lokasi, atau akun media sosial.</li>
        </ul>
      </div>

      <div class="section">
        <h2>8. HAK-HAK PENGGUNA TERHADAP DATA PRIBADI</h2>
        <p>Pengguna Curhatorium memiliki hak sebagai berikut:</p>
        <ul>
          <li><strong>Hak Akses:</strong> Melihat dan mengetahui data pribadi yang disimpan oleh platform.</li>
          <li><strong>Hak Koreksi:</strong> Memperbarui informasi akun jika diperlukan.</li>
          <li><strong>Hak Hapus:</strong> Menghapus akun dan seluruh data pribadi secara permanen.</li>
          <li><strong>Hak Penarikan Persetujuan:</strong> Menolak penggunaan data opsional sewaktu-waktu.</li>
        </ul>
        <p>Permintaan dapat diajukan melalui email ke: hello@curhatorium.com. Waktu pemrosesan maksimal: 14 hari kerja.</p>
      </div>

      <div class="section">
        <h2>9. PENGUNGKAPAN KEPADA PIHAK KETIGA</h2>
        <p>Curhatorium tidak pernah menjual, menyewakan, atau memperdagangkan data pengguna kepada pihak ketiga untuk tujuan iklan atau keuntungan komersial. Namun, data dapat diungkapkan dalam kondisi terbatas berikut:</p>
        <ul>
          <li>Atas permintaan resmi dari otoritas hukum negara Republik Indonesia</li>
          <li>Dalam kondisi darurat yang mengancam keselamatan pengguna (misalnya indikasi bunuh diri atau kekerasan)</li>
          <li>Dengan persetujuan eksplisit tertulis dari pengguna</li>
        </ul>
      </div>

      <div class="section">
        <h2>10. PENGGUNAAN COOKIES & ANALITIK</h2>
        <p>Kami menggunakan cookies dan alat analitik internal untuk:</p>
        <ul>
          <li>Mengingat preferensi login pengguna</li>
          <li>Mengukur statistik interaksi pengguna</li>
          <li>Mendeteksi penyalahgunaan layanan</li>
        </ul>
        <p>Pengguna dapat menonaktifkan cookies dari browser masing-masing, namun beberapa fitur mungkin menjadi terbatas.</p>
      </div>

      <div class="section">
        <h2>11. PENYIMPANAN DATA INTERNASIONAL</h2>
        <p>Saat ini, seluruh server Curhatorium berlokasi di dalam wilayah hukum Republik Indonesia. Jika di masa depan terjadi pengalihan lokasi server (termasuk ke luar negeri), pengguna akan diberi notifikasi dan opsi persetujuan ulang.</p>
      </div>

      <div class="section">
        <h2>12. MASA SIMPAN DATA</h2>
        <ul>
          <li>Data akun aktif disimpan selama pengguna menggunakan layanan.</li>
          <li>Data pengguna yang meminta penghapusan akun akan dihapus permanen dalam waktu maksimal 14 hari.</li>
          <li>Data agregat anonim untuk keperluan statistik dapat disimpan tanpa batas waktu (tanpa identitas personal).</li>
        </ul>
      </div>

      <div class="section">
        <h2>13. PEMBARUAN KEBIJAKAN PRIVASI</h2>
        <p>Curhatorium dapat memperbarui isi kebijakan ini secara berkala. Perubahan akan diumumkan melalui situs/platform dan akan berlaku efektif sejak tanggal revisi yang diumumkan.</p>
      </div>

      <div class="section">
        <h2>14. KONTAK DAN PERNYATAAN</h2>
        <p>Untuk pertanyaan, permintaan, atau laporan pelanggaran terkait data pribadi, silakan hubungi:</p>
        <p><strong>hello@curhatorium.com</strong> (Email)</p>
        <p><strong>Jam operasional:</strong> Setiap hari, pukul 08.00 – 17.00 WIB</p>
      </div>

      <div class="section">
        <h2>15. PERSETUJUAN</h2>
        <p>Dengan mendaftar atau menggunakan Curhatorium, Anda menyatakan telah membaca, memahami, dan menyetujui seluruh isi Kebijakan Privasi ini.</p>
      </div>
    </div>

    <!-- Login Button Section -->
    <div class="login-section">
      <h2>Mulai Perjalanan Anda</h2>
      <p>Setelah membaca kebijakan privasi kami, Anda dapat melanjutkan untuk masuk ke akun Curhatorium Anda.</p>
      <a href="{{ route('login') }}" class="login-btn">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
        </svg>
        Masuk ke Akun
      </a>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
      <h2>KONTAK RESMI</h2>
      <p>Untuk pertanyaan atau klarifikasi tentang kebijakan privasi, silakan hubungi kami:</p>
      <div class="contact-links">
        <a href="mailto:hello@curhatorium.com" class="contact-link">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
          </svg>
          hello.curhatorium.com
        </a>
        <a href="https://instagram.com/curhatorium_" target="_blank" class="contact-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
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