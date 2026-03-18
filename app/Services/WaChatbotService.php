<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class WaChatbotService
{
    public function process($number, $message)
    {
        if (Str::lower($message) ==  'hallo' | Str::lower($message) ==  'helo' | Str::lower($message) ==  'halo' | Str::lower($message) ==  'hai') {
            return $this->menu();
        }

        if (Str::lower($message) ==  'menu') {
            return $this->menu();
        }

        if ($message == '1') {
            return $this->registerTemplate();
        }

        if ($message == '2') {
            return $this->productList();
        }

        if (str_starts_with($message, 'daftar')) {
            return $this->registerUser($number, $message);
        }


        return $this->defaultReply();
    }

    private function menu()
    {
        return "Halo 👋\n\n"
            . "Selamat datang di toko kami\n\n"
            . "1️⃣ Register MosherBlox\n"
            . "2️⃣ Lihat Game\n\n"
            . "Ketik angka menu.";
    }

    private function registerTemplate()
    {
        return "Untuk mendaftar kirim format:\n\n"
            . "DAFTAR#NAMA#EMAIL#PASSWORD\n\n"
            . "Contoh:\n"
            . "DAFTAR#Imam#Imam@gmail.com#Abc890123";
    }

    private function productList()
    {
        $games = Game::where('status', 1)
            ->select('id', 'name', 'slug')
            ->get();

        $text = "📦 Daftar Games\n\n";

        foreach ($games as $i => $p) {
            $text .= ($i + 1) . ". {$p->name}\n";
            $text .= "LINK : {$p->slug}\n\n";
        }

        return $text;
    }

    private function registerUser($number, $message)
    {
        $data = explode('#', $message);

        if (count($data) < 4) {
            return "Format salah.\nContoh:\nDAFTAR#NAMA#EMAIL#Password";
        }

        $name = $data[1];
        $email = $data[2];
        $Password = $data[3];

        User::updateOrCreate(
            ['phone' => $number],
            [
                'name' => $name,
                'email' => $email,
                'Password' => $Password
            ]
        );

        return "Registrasi berhasil ✅";
    }


    private function defaultReply()
    {
        return "Maaf saya tidak mengerti.\n\n"
            . "Ketik MENU untuk melihat daftar layanan.";
    }
}
