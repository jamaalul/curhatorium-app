<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = [
            'Luka adalah tempat cahaya masuk ke dalam dirimu.',
            'Setiap hari adalah kesempatan baru untuk mulai lagi.',
            'Kamu berhak untuk bahagia.',
            'Bahkan malam tergelap pun akan berakhir dan matahari akan terbit.',
            'Setiap proses pulih memiliki makna yang berarti dalam hidup.',
            'Terima kasih sudah bertahan. Kalian orang-orang kuat!',
            'Patah hati membuka ruang untuk kalian bertumbuh.',
            'Setiap perjalanan hidup kalian dapat memberikan pelajaran.',
            'Belajar untuk menerima diri kalian sendiri baik di saat kalian berada pada potensi terbaik maupun tidak.',
            'Tumbuh dari rasa sakit adalah bentuk keberanian.',
            'Jangan keras pada dirimu sendiri, kamu sedang mencoba yang terbaik.',
            'Memaafkan diri sendiri adalah bentuk cinta paling dalam.',
            'Kamu berharga, bahkan saat kamu merasa tidak.',
            'Tidak ada manusia yang sempurna—dan itulah yang membuat kita seorang manusia.',
            'Rangkullah kekuranganmu sebagaimana itu merupakan bagian dari dirimu.',
            'Kamu tidak harus jadi versi terbaikmu setiap hari.',
            'Berhenti membandingkan prosesmu dengan milik orang lain.',
            'Cintai dirimu, terutama saat rasanya paling sulit dilakukan.',
            'Kesalahan bukan akhir—mereka adalah pengingat bahwa kamu masih belajar.',
            'Menjadi lembut pada diri sendiri adalah bentuk mencintai diri sendiri.',
            'Kamu berhasil menamatkan hari-hari sebelumnya! Selanjutnya pasti bisa.',
            'Keberanian adalah ketika diri kalian tetap melangkah ke depan meski dilanda rasa takut.',
            'Bangun dari tempat tidur merupakan langkah awal keberanian.',
            'Badai pasti berlalu. Tapi kamu pasti bisa bertahan.',
            'Setiap langkah kecil adalah bentuk kekuatan besar.',
            'Jangan remehkan kekuatan survive kalian dan bertahanlah.',
            'Kamu lebih kuat dari yang kamu pikirkan.',
            'Tidak apa-apa jatuh selama kamu tidak berhenti bangkit.',
            'Ada kekuatan luar biasa dalam dirimu yang belum kamu sadari.',
            'Kamu layak dirayakan bagaimanapun keadaannya.',
            'Hari ini, cukup jadi versi mindful dari dirimu.',
            'Kamu tidak sendirian.',
            'Pikiran buruk tentang dirimu tidak mendefinisikan dirimu yang sebenarnya.',
            'Kebaikan kecil bisa membuat hari besar.',
            'Tidak semua hari harus produktif. Beberapa hari hadir untukmu sebagai waktu istirahat.',
            'Istirahat itu bagian dari perjalanan.',
            'Merasa gagal bukan berarti kamu gagal seutuhnya.',
            'Kamu punya hak untuk didengarkan dan dipahami.',
            'Satu senyum bisa jadi awal perubahan besar.',
            'Kamu berhak dipentingkan.',
            'Apa yang kamu lalui hari ini bisa jadi cerita kekuatanmu besok.',
            'Jangan takut berubah. Bertumbuh boleh jadi berarti kehilangan beberapa hal yang membuatmu kuat.',
            'Ketenangan tidak selalu datang dari luar. Kadang kamu sendiri adalah rumah bagi dirimu sendiri.',
            'Jalan panjang dimulai dari satu langkah penuh harapan.',
            'Dengarkan dirimu. Dia tahu apa yang kamu butuhkan.',
            'Terkadang diam dan meratapi adalah bentuk merawat diri.',
            'Kamu tidak harus tahu semua jawaban itu sekarang.',
            'Berproses bukan berarti kamu lamban, itu merupakan satu step penting dalam kehidupanmu.',
            'Yang penting bukan kecepatan, tapi konsistensi.',
            'Kamu sedang menulis kisahmu sendiri, itu sudah lebih dari luar biasa.'
        ];

        foreach ($quotes as $text) {
            Quote::create(['quote' => $text]);
        }
    }
} 