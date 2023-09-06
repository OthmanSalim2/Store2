<?php

namespace App\Models;

use App\Rules\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'parent_id', 'slug', 'status', 'description', 'image'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id')
            ->withDefault([
                'name' => '-'
            ]);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('status', 'active');
    }

    public function scopeStatus(Builder $builder, $status)
    {
        return $builder->where('status', $status);
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $builder->when($filters['name'] ?? false, function ($builder, $value) {
            // categories.name here mean the name from categories table
            $builder->where('categories.name', 'LIKE', "%{$value}%");
        });

        $builder->when($filters['status'] ?? false, function ($builder, $value) {
            $builder->where('categories.status', '=', $value);
        });
    }

    public static function rules($id = 0)
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'filter:laravel,php,html',
                // function ($attribute, $value, $fail) {
                //     if (strtolower($value) == 'laravel') {
                //         $fail('This name id forbidden');
                //     }
                // },
                // new Filter(['php', 'laravel', 'html']),
                //  "unique:categories,name,{$id}"
                // Rule::unique('categories', 'name')->ignore($id),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,archived'],
            // size:1,048,576 this mean 1MB size it
            'image' => ['nullable', 'image', 'dimensions:min_width=100,min_height=200'], //'size:1048576',
            'parent_id' => ['nullable', 'int', 'exists:categories,id']
        ];
    }
}
