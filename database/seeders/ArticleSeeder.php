<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Articles\Article;
use App\Models\Articles\Category;
use App\Models\Articles\Tag;
use App\Models\Articles\Comment;
use App\Models\Articles\ArticleView;
use App\Models\Articles\ArticleMeta;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. User get
        $users = User::all();

        // 2. Categories
        $categories = collect([
            ['name' => 'Programming'],
            ['name' => 'Backend'],
            ['name' => 'Tutorial'],
            ['name' => 'Tips'],
        ])->map(function ($cat) {
            return Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
            ]);
        });

        // 3. Tags
        $tags = collect([
            'Laravel',
            'PHP',
            'API',
            'JavaScript',
            'Coding',
            'Web'
        ])->map(function ($tag) {
            return Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        });

        // 4. Articles
        Article::factory(20)->create()->each(function ($article) use ($categories, $tags, $users) {

            // assign author random
            $article->update([
                'author_id' => $users->random()->id,
                'status' => collect(['draft', 'published'])->random(),
                'published_at' => now(),
            ]);

            // attach category (1-3)
            $article->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')
            );

            // attach tags (2-4)
            $article->tags()->attach(
                $tags->random(rand(2, 4))->pluck('id')
            );

            // 5. Comments (0-5)
            Comment::factory(rand(0, 5))->create([
                'article_id' => $article->id,
            ]);

            // 6. Views (1-10)
            for ($i = 0; $i < rand(1, 10); $i++) {
                ArticleView::create([
                    'article_id' => $article->id,
                    'ip_address' => fake()->ipv4(),
                ]);
            }

            // 7. Meta SEO
            ArticleMeta::create([
                'article_id' => $article->id,
                'meta_key' => 'seo_title',
                'meta_value' => $article->title,
            ]);
        });
    }
}
