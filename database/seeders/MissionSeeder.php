<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $missions = [
            // Easy missions (30 XP free / 40 XP subscription)
            [
                'title' => 'Syukuri Hari Ini',
                'description' => 'Tulis 3 hal yang kamu syukuri hari ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Air Pertama di Pagi Hari',
                'description' => 'Minum 1 gelas air putih saat bangun tidur.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Lagu untuk Ketenangan',
                'description' => 'Dengarkan 1 lagu yang membuatmu merasa tenang.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Napas Sadar',
                'description' => 'Tarik napas dalam dan buang perlahan selama 3 kali.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Menyapa Dunia',
                'description' => 'Lihat pemandangan di luar jendela selama 1 menit.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Peregangan Ringan',
                'description' => 'Lakukan peregangan ringan selama 5 menit.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Kirim Kebaikan',
                'description' => 'Kirim pesan baik ke teman atau keluarga.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Senyum untuk Diri',
                'description' => 'Tersenyum pada diri sendiri di cermin.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Jalan 5 Menit',
                'description' => 'Berjalan kaki 5 menit, bisa di dalam rumah.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Afirmasi Positif',
                'description' => 'Tulis 1 kalimat afirmasi untuk dirimu (contoh: "Saya layak dicintai").',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Peluk Diri',
                'description' => 'Peluk dirimu sendiri selama 10 detik.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Alarm Penyemangat',
                'description' => 'Atur alarm dengan pesan motivasi.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Catatan Keberhasilan Harian',
                'description' => 'Tulis 1 hal kecil yang berhasil kamu lakukan hari ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Kutipan Inspiratif',
                'description' => 'Baca 1 kutipan inspiratif dari internet.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Ekspresikan Lewat Gambar',
                'description' => 'Gambar atau coret-coret bebas di kertas.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Pernapasan 4-7-8',
                'description' => 'Tarik napas 4 detik, tahan 7 detik, hembuskan 8 detik selama 1 menit.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Hiburan Singkat',
                'description' => 'Tonton 1 video lucu singkat.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Diam dan Hadir',
                'description' => 'Duduk diam tanpa melakukan apa pun selama 2 menit.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Rapikan Ruangan',
                'description' => 'Rapikan satu sudut kecil di ruanganmu.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Deskripsi Langit',
                'description' => 'Lihat dan deskripsikan langit yang kamu lihat saat ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Pelajari Hal Positif',
                'description' => 'Pelajari 1 prinsip hidup atau nilai positif baru hari ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Nikmati Aroma Favorit',
                'description' => 'Gunakan aromaterapi atau lilin esensial di ruanganmu.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Surat untuk Diri',
                'description' => 'Tulis "surat afirmasi" yang menyemangati dirimu sendiri.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Apresiasi Tubuhmu',
                'description' => 'Berkaca dan lihat hal positif dari tubuhmu selama 2 menit.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Playlist Nyaman',
                'description' => 'Buat playlist lagu yang membuatmu nyaman dan tenang.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Tarik Napas Sebelum Mulai',
                'description' => 'Tarik napas 3 kali sebelum mengerjakan tugas/proyek hari ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Kenangan Positif',
                'description' => 'Lihat 1 foto kenangan yang membuatmu tersenyum.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Warna Hari Ini',
                'description' => 'Pilih 1 warna favorit hari ini dan temukan benda dengan warna itu.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Kenali Emosi',
                'description' => 'Sadari dan beri nama 1 emosi yang kamu rasakan saat ini.',
                'difficulty' => 'easy',
            ],
            [
                'title' => 'Sentuhan Lembut',
                'description' => 'Pijat lembut tanganmu sendiri selama beberapa detik.',
                'difficulty' => 'easy',
            ],
            // Medium missions (40 XP free / 50 XP subscription)
            [
                'title' => 'Jurnal Perasaan Hari Ini',
                'description' => 'Tulis jurnal bebas tentang apa yang kamu rasakan hari ini, tanpa batas panjang atau pendek.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Teknik Grounding 5-4-3-2-1',
                'description' => 'Deskripsikan 5 hal yang kamu lihat, 4 yang kamu sentuh, 3 yang kamu dengar, 2 yang kamu cium, dan 1 yang kamu cicipi.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Jurnal Beban Pikiran',
                'description' => 'Tulis jurnal tentang beban pikiran atau kesulitan yang sedang kamu hadapi.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Meditasi 10 Menit',
                'description' => 'Bermeditasi minimal selama 10 menit untuk menenangkan diri.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Detoks Media Sosial',
                'description' => 'Hindari sosial media selama 6 jam dan lakukan aktivitas tanpa keterlibatan medsos.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Waktu Tanpa Gadget',
                'description' => 'Off gadget minimal 2 jam hari ini untuk memberi ruang bagi pikiran dan tubuh.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Baca Buku Pengembangan Diri',
                'description' => 'Baca minimal 5 halaman buku yang membantumu berkembang secara pribadi.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Daftar Pencapaian Hidup',
                'description' => 'Buat daftar 3 pencapaian hidup terpenting menurut versimu.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Batasan Sehat',
                'description' => 'Tuliskan 3 batasan sehat yang ingin kamu mulai terapkan dalam hidupmu.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Cerita tentang Ketakutan',
                'description' => 'Ceritakan 1 hal yang kamu takuti, lewat journaling atau berbicara dengan orang terdekat.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Tidur Sebelum Jam 10',
                'description' => 'Tidur maksimal pukul 22.00 untuk memberikan istirahat optimal bagi tubuh.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Bantah Ucapan Negatif untuk Diri',
                'description' => 'Refleksikan 1 ucapan negatif ke diri sendiri dan bantah dengan pemikiran yang sehat.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Child\'s Pose 5 Menit',
                'description' => 'Lakukan pose yoga "child\'s pose" selama 5 menit untuk relaksasi.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Perspektif Orang Lain',
                'description' => 'Renungkan sudut pandang orang yang membuatmu kesal, dan coba pahami alasan di baliknya.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Aktivitas Kreatif 15 Menit',
                'description' => 'Luangkan waktu 15 menit untuk menggambar, menulis, atau aktivitas kreatif lainnya.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Mindful Eating',
                'description' => 'Makan dengan penuh kesadaran tanpa gadget, fokus pada rasa, aroma, dan tekstur makanan.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Refleksi Pengalaman Buruk',
                'description' => 'Renungkan 1 pengalaman buruk dan ambil pelajaran darinya.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Video Kesehatan Mental',
                'description' => 'Tonton 1 video pendek seputar kesehatan mental dan internalisasikan maknanya.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Ambil Keputusan Kecil',
                'description' => 'Ambil 1 keputusan kecil yang sebelumnya kamu hindari.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Rencana Harian Realistis',
                'description' => 'Buat 3 rencana kecil dan realistis untuk hari ini, lalu coba jalankan.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Olahraga Ringan 15–20 Menit',
                'description' => 'Lakukan aktivitas fisik ringan minimal 15 menit untuk menyegarkan tubuh.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Permintaan Maaf',
                'description' => 'Kirim permintaan maaf kepada seseorang yang pernah kamu sakiti.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Vision Board Mini',
                'description' => 'Buat papan visual kecil berisi bayangan hidup ideal versimu.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Daftar Coping Mechanism',
                'description' => 'Tulis daftar coping sehat dan tidak sehat yang pernah kamu lakukan.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Refleksi Inner Child Needs',
                'description' => 'Tuliskan 1 kebutuhan masa kecil (inner child) yang masih kamu rasakan hingga kini.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Pesan untuk Diri Masa Depan',
                'description' => 'Tulis kata-kata atau pesan untuk dirimu satu tahun ke depan.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Refleksi atas Kegagalan',
                'description' => 'Tulis responsmu terhadap 1 kegagalan, lalu refleksikan apakah itu respons sehat atau tidak.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Lawan Overthinking',
                'description' => 'Lawan 1 pikiran negatif dengan bukti objektif atau fakta yang menenangkan.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Bicara pada Diri Seperti Sahabat',
                'description' => 'Bicaralah dengan dirimu sendiri dengan kasih, seolah berbicara pada sahabat dekat selama 10 menit.',
                'difficulty' => 'medium',
            ],
            [
                'title' => 'Selesaikan Sebagian Tugas',
                'description' => 'Kerjakan sebagian kecil dari tugas/proyek yang kamu tangguhkan hari ini.',
                'difficulty' => 'medium',
            ],
            // Hard missions (80 XP free / 90 XP subscription)
            [
                'title' => 'Ikut Sesi Curhatorium',
                'description' => 'Ikuti 1 sesi SGD atau Share and Talk Curhatorium hari ini.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Hadapi Ketakutan Kecil',
                'description' => 'Lakukan 1 hal kecil yang selama ini kamu hindari karena takut.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Bongkar Luka Lewat Jurnal',
                'description' => 'Tulis jurnal tentang pengalaman menyakitkan atau tidak menyenangkan yang pernah kamu alami.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Evaluasi Hubungan',
                'description' => 'Tinjau relasi pertemanan/percintaan dan refleksikan apakah ada tanda toxic relationship.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Puasa Distraksi 12 Jam',
                'description' => 'Hindari 1 distraksi utama (misal: gadget, cemilan, rokok) selama 12 jam.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Berani Minta Bantuan',
                'description' => 'Mintalah bantuan secara terbuka jika mengalami kesulitan hari ini.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Evaluasi Aktivitas Harian',
                'description' => 'Tinjau kegiatan harianmu, tandai mana yang menguras energi, lalu buat solusi.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Refleksi Nilai Hidup',
                'description' => 'Renungkan nilai hidup penting bagimu dan cocokan dengan 1 rutinitasmu.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Jogging 30 Menit',
                'description' => 'Lakukan jogging selama 30 menit untuk menyehatkan tubuh dan pikiran.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Pola Makan 30 Hari',
                'description' => 'Buat daftar pola makan sehat yang ingin kamu jalani selama 30 hari ke depan.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Deep Talk',
                'description' => 'Lakukan pembicaraan mendalam dengan satu orang (psikolog atau orang terdekat).',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Hadiri SGD Curhatorium',
                'description' => 'Ikuti 1 sesi SGD yang diadakan Curhatorium sebagai bentuk refleksi dan berbagi.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Daftar Luka Batin',
                'description' => 'Tulis daftar luka batin masa lalu dan bagaimana pengaruhnya pada dirimu saat ini.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Evaluasi Konflik Diri',
                'description' => 'Renungkan konflik internal dalam dirimu yang belum terselesaikan.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Latih Diri di Situasi Sosial',
                'description' => 'Hadapi situasi sosial yang menantang (misal: berbicara di grup) untuk melatih keberanian.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Journaling 3 Hari',
                'description' => 'Lakukan journaling setiap hari selama 3 hari berturut-turut.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Evaluasi Citra Diri',
                'description' => 'Refleksikan cara kamu melihat dirimu selama satu bulan terakhir.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Minta Feedback Emosional',
                'description' => 'Tanyakan feedback jujur dari orang terdekat tentang kebiasaan emosionalmu.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'To-Do List Mingguan',
                'description' => 'Buat daftar tugas dan kegiatan selama satu minggu ke depan.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Refleksi Masa Kecil dan Relasi Dewasa',
                'description' => 'Renungkan bagaimana masa kecilmu membentuk pola relasimu saat ini.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Ubah Kebiasaan Buruk',
                'description' => 'Pilih dan ubah 1 kebiasaan buruk yang berdampak negatif pada mentalmu.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Red Flags & Green Flags',
                'description' => 'Buat daftar tanda bahaya dan tanda sehat dalam hubungan versi dirimu.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Tantang Diri Bicara Emosi',
                'description' => 'Ungkapkan perasaanmu secara langsung pada seseorang hari ini.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Catat Call Center Mental Health',
                'description' => 'Cari dan tulis 3 nomor layanan/kontak kesehatan mental terpercaya.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Tes Mental 3 Hari Curhatorium',
                'description' => 'Lakukan tes kesehatan mental Curhatorium selama 3 hari berturut-turut.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Audit Rutinitas Harian',
                'description' => 'Evaluasi rutinitas harianmu dan ubah kebiasaan yang tidak mendukung kesehatan mental.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'To-Do List Mingguan (Ulang)',
                'description' => 'Buat ulang daftar tugas mingguan, pastikan realistis dan terukur.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Latihan Pengampunan',
                'description' => 'Latih diri untuk memaafkan diri sendiri dan orang lain atas kesalahan masa lalu.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Ambil Keputusan Besar',
                'description' => 'Ambil 1 keputusan besar yang sebelumnya kamu tunda karena takut gagal.',
                'difficulty' => 'hard',
            ],
            [
                'title' => 'Visualisasi Diri Terbaik',
                'description' => 'Bayangkan versi terbaik dirimu dan tulis langkah realistis menuju ke sana.',
                'difficulty' => 'hard',
            ],
        ];

        // Delete old data - handle foreign key constraints for different database types
        $connection = \DB::connection()->getDriverName();
        
        if ($connection === 'mysql') {
            // For MySQL, disable foreign key checks temporarily
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Mission::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            // For SQLite and other databases, delete related records first
            \DB::table('mission_completions')->delete();
            Mission::truncate();
        }

        foreach ($missions as $mission) {
            Mission::create($mission);
        }
    }
} 