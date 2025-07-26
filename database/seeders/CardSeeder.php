<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Card;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pertanyaan = [
            "Apa hal paling berat yang kamu rasakan minggu ini?",
            "Kapan terakhir kali kamu merasa hidupmu tenang?",
            "Apa yang biasanya kamu lakukan saat merasa cemas?",
            "Bagaimana cara kamu mengatasi kewalahan?",
            "Siapa orang yang membuatmu merasa aman?",
            "Apa ketakutan terdalam yang jarang kamu ungkapkan?",
            "Kapan terakhir kali kamu merasa didengar?",
            "Apa arti 'rumah' bagimu?",
            "Dalam hal apa kamu merasa tidak cukup?",
            "Jika kamu kembali pada 10 tahun lalu, apa yang akan kamu katakan pada dia?",
            "Apakah ada pengalaman yang mengubah cara pandangmu tentang hidup?",
            "Kapan kamu merasa paling sendiri, dan apa yang kamu lakukan saat itu?",
            "Bagaimana cara kamu menghadapi kehilangan?",
            "Apakah kamu mudah memaafkan diri sendiri ketika berbuat kesalahan/kegagalan?",
            "Apa yang kamu takut orang lain ketahui tentang dirimu?",
            "Apa bagian dari dirimu yang sedang kamu usahakan untuk terima?",
            "Apakah kalian bersyukur dengan hidup?",
            "Apa luka masa kecil yang masih terasa sampai sekarang?",
            "Apa kalimat yang paling ingin kamu dengar hari ini?",
            "Apa harapanmu yang belum tercapai hingga sekarang?",
            "Kapan terakhir kali kamu merasa bangga terhadap diri sendiri?",
            "Apa yang membuatmu merasa dicintai?",
            "Apa yang kamu pelajari dari rasa sakit terbesarmu?",
            "Siapa kalian sebenarnya?",
            "Jika kamu bisa menghapus satu kesalahan besar yang kalian perbuat selama hidup, apakah itu?",
            "Apa yang membuatmu merasa sedih?",
            "Bagaimana kalian menyelesaikan masalah?",
            "Apa pesan yang ingin kamu sampaikan ke versi kecil dari dirimu?",
            "Apa yang sedang ingin kalian capai sekarang?",
            "Apa yang membuatmu tetap bertahan sejauh ini?",
            "Apakah kamu merasa dirimu cukup baik untuk dicintai?",
            "Apa yang selama ini ingin kalian lakukan tapi masih belum tersampaikan?",
            "Adakah sosok yang menjadi motivasi kalian untuk hidup dan menjadi diri kalian yang sekarang?",
            "Apa yang kamu lakukan saat merasa tidak berdaya?",
            "Apa perasaan yang paling kamu hindari?",
            "Dalam hal apa kamu merasa tidak dimengerti/didengarkan?",
            "Jika kamu bisa menghapus satu kenangan, kamu akan menghapus yang mana?",
            "Apa hal kecil yang bisa membuat kalian senang?",
            "Adakah hal yang mengganggu perasaan kalian belakangan ini?",
            "Apa bentuk perhatian yang paling berarti buatmu?",
            "Apa yang paling ingin kamu ubah dari masa lalumu?",
            "Dalam hal apa kamu merasa kuat?",
            "Apa kalimat penyemangat yang ingin kamu dengar dari orang terdekat?",
            "Apa hal paling indah yang kamu alami selama hidup?",
            "Bagaimana kamu tahu bahwa kamu sedang tidak baik-baik saja?",
            "Apa pola pikir yang ingin kamu ubah?",
            "Apa yang membuatmu merasa terjalin dengan orang lain?",
            "Kapan kamu merasa benar-benar tidak berdaya?",
            "Apa makna 'menyerah' dalam hidupmu?",
            "Apa kejadian yang belum sempat kamu ceritakan ke siapapun?",
            "Apa perasaan yang paling sering kamu sembunyikan dari orang lain?",
            "Apa bentuk validasi yang paling kamu dambakan?",
            "Apa tantangan terbesar yang kamu hadapi saat hidup?",
            "Apa yang kamu pelajari tentang dirimu dari rasa kehilangan?",
            "Sudahkah kalian menghargai usaha diri kalian sendiri?",
            "Apa hal yang paling kamu ingin lepaskan, tapi belum bisa?",
            "Apa hal yang membuatmu merasa hidup?",
            "Siapa versi dirimu yang ingin kamu ulang kembali?",
            "Apa hal kecil yang bisa kamu lakukan untuk menyayangi diri sendiri?",
            "Sudahkah kalian mengapresiasi diri?"
        ];

        $pernyataan = [
            "Sebutkan 3 hal yang kamu syukuri dari dirimu.",
            "Sebutkan satu hal yang ingin kamu inginkan (bukan uang maupun materi lainnya).",
            "Gambarkan dirimu dalam 3 kata yang paling mencerminkan siapa kamu.",
            "Ceritakan satu kenangan yang masih kamu ingat sampai sekarang (bisa baik maupun buruk).",
            "Jelaskan satu emosi yang sedang kamu rasakan saat ini.",
            "Ceritakan satu momen terindah di hidupmu.",
            "Sebutkan hal kecil yang bisa kamu lakukan hari ini untuk dirimu sendiri.",
            "Berikan afirmasi untuk dirimu sekarang.",
            "Ceritakan satu pengalaman yang membuatmu merasa berkembang.",
            "Pikirkan satu red flag di diri kalian yang bahkan kalian tidak sadari sebelumnya.",
            "Ceritakan satu momen ketika kamu merasa benar-benar didengar.",
            "Sebutkan satu kalimat afirmasi untuk diri kalian sendiri.",
            "Sebutkan satu ketakutan yang sedang kamu hadapi dan bagaimana kamu ingin menghadapinya.",
            "Gambarkan ‘versi terbaik’ dari dirimu dalam kalimat singkat.",
            "Ceritakan satu momen ketika kamu merasa gagal.",
            "Ceritakan satu hal yang kamu harap bisa dimengerti orang lain tentangmu.",
            "Sebutkan satu kebiasaan baik yang ingin kamu bangun.",
            "Ceritakan momen ketika kamu memilih untuk bertahan hingga akhir.",
            "Sebutkan satu hal yang kamu ingin lepaskan.",
            "Ceritakan emosi yang paling sering kalian alami belakangan ini."
        ];

        // Delete old data
        Card::truncate();

        foreach ($pertanyaan as $q) {
            Card::create([
                'content' => $q,
                'category' => 'pertanyaan',
            ]);
        }
        foreach ($pernyataan as $p) {
            Card::create([
                'content' => $p,
                'category' => 'pernyataan',
            ]);
        }
    }
} 