<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
         // CATEGORIES
        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Programming',
                'slug' => 'programming',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Backend',
                'slug' => 'backend',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // TAGS
        DB::table('tags')->insert([
            [
                'id' => 1,
                'name' => 'Laravel',
                'slug' => 'laravel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'API',
                'slug' => 'api',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ARTICLES
        DB::table('articles')->insert([
            [
                'id' => 1,
                'title' => 'Belajar Laravel Dasar',
                'slug' => Str::slug('Belajar Laravel Dasar'),
                'content' => 'Ini adalah konten belajar Laravel...',
                'excerpt' => 'Belajar Laravel dari nol',
                'thumbnail' => null,
                'status' => 'published',
                'published_at' => now(),
                'author_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Tips Backend Developer',
                'slug' => Str::slug('Tips Backend Developer'),
                'content' => 'Tips menjadi backend developer...',
                'excerpt' => 'Tips backend',
                'thumbnail' => null,
                'status' => 'draft',
                'published_at' => null,
                'author_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // RELATION: article_category
        DB::table('article_category')->insert([
            ['article_id' => 1, 'category_id' => 1],
            ['article_id' => 1, 'category_id' => 2],
            ['article_id' => 2, 'category_id' => 2],
        ]);

        // RELATION: article_tag
        DB::table('article_tag')->insert([
            ['article_id' => 1, 'tag_id' => 1],
            ['article_id' => 1, 'tag_id' => 2],
            ['article_id' => 2, 'tag_id' => 2],
        ]);
    }
}
