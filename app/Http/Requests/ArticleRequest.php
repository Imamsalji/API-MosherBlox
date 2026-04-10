<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //Rules REQUEST ARTICLE
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'min:10', 'max:20'],
            'content' => ['required', 'string'],
            'excerpt' => ['required', 'string', 'min:10', 'max:500'],
            'thumbnail' => [
                'file',
                'mimes:jpg,jpeg,png',
                'max:2048', // 2MB per file
            ],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'published_at' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }
}
