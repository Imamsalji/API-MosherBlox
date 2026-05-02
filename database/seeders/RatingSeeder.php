<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Rating;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users    = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Pastikan sudah ada data User dan Product terlebih dahulu.');
            return;
        }

        $reviews = [
            5 => [
                ['title' => 'Luar biasa!',        'body' => 'Aplikasi ini sangat membantu pekerjaan saya sehari-hari. Fiturnya lengkap dan mudah digunakan.'],
                ['title' => 'Sangat Puas',         'body' => 'Performa cepat, UI bersih, tidak ada bug berarti. Recommended banget!'],
                ['title' => 'Top Banget',          'body' => 'Sudah coba banyak aplikasi sejenis, ini yang terbaik sejauh ini.'],
            ],
            4 => [
                ['title' => 'Bagus',               'body' => 'Secara keseluruhan bagus, hanya ada beberapa fitur kecil yang masih perlu diperbaiki.'],
                ['title' => 'Hampir Sempurna',     'body' => 'Aplikasi berjalan lancar, UI cukup intuitif. Butuh sedikit peningkatan di bagian notifikasi.'],
                ['title' => 'Cukup Memuaskan',     'body' => 'Fitur utama bekerja dengan baik. Harap tambahkan mode gelap di update berikutnya.'],
            ],
            3 => [
                ['title' => 'Lumayan',             'body' => 'Aplikasi cukup fungsional tapi masih ada beberapa bug kecil yang mengganggu.'],
                ['title' => 'Biasa Saja',          'body' => 'Tidak ada yang spesial, fitur standar. Masih butuh banyak pengembangan.'],
                ['title' => 'Perlu Peningkatan',   'body' => 'Loading agak lambat di beberapa halaman. Semoga diperbaiki di versi berikutnya.'],
            ],
            2 => [
                ['title' => 'Kurang Memuaskan',    'body' => 'Sering crash saat membuka halaman tertentu. Harap segera diperbaiki.'],
                ['title' => 'Masih Banyak Bug',    'body' => 'Fitur filter tidak bekerja dengan baik. Pengalaman pengguna masih jauh dari ekspektasi.'],
            ],
            1 => [
                ['title' => 'Sangat Mengecewakan', 'body' => 'Aplikasi tidak bisa digunakan sama sekali, selalu force close saat dibuka.'],
                ['title' => 'Tidak Recommended',   'body' => 'Terlalu banyak iklan dan bug. Tidak sesuai dengan yang dijanjikan.'],
            ],
        ];

        $bar = $this->command->getOutput()->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            // Ambil subset user secara acak (5-15 user per produk)
            $raters = $users->random(min(rand(5, 15), $users->count()));

            foreach ($raters as $user) {
                $score  = $this->weightedScore();
                $review = $reviews[$score][array_rand($reviews[$score])];

                /** @var Rating $rating */
                $rating = Rating::create([
                    'user_id'      => $user->id,
                    'ratable_id'   => $product->id,
                    'ratable_type' => Product::class,
                    'score'        => $score,
                    'title'        => $review['title'],
                    'body'         => $review['body'],
                    'status'       => 'approved',
                ]);

                // Tambahkan like/dislike secara acak dari user lain
                $reactors = $users->except($user->id)->random(min(rand(0, 5), $users->count() - 1));

                foreach ($reactors as $reactor) {
                    Reaction::create([
                        'user_id'        => $reactor->id,
                        'reactable_id'   => $rating->id,
                        'reactable_type' => Rating::class,
                        'type'           => rand(0, 1) ? 'like' : 'dislike',
                    ]);
                }
            }

            // Sync summary setelah semua rating produk selesai
            $product->recalculateRatingSummary();

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✅ RatingSeeder selesai!');
    }

    /**
     * Distribusi score lebih realistis:
     * 5⭐ → 40%, 4⭐ → 30%, 3⭐ → 15%, 2⭐ → 10%, 1⭐ → 5%
     */
    private function weightedScore(): int
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 40  => 5,
            $rand <= 70  => 4,
            $rand <= 85  => 3,
            $rand <= 95  => 2,
            default      => 1,
        };
    }
}
