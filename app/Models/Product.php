<?php

namespace App\Models;

use App\Models\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id', 'category_id', 'name', 'description',
        'slug', 'description', 'image', 'price', 'compare_price',
        'options', 'rating', 'featured', 'status'
    ];

    protected $hidden = [
        'image',
        'created_at', 'updated_at', 'deleted_at',
    ];

    // this property it use to make appends for image_url and property another in request in api.
    protected $appends = [
        'image_url'
    ];

    public static function booted()
    {
        // here 'store' it's consider the name of global scope.
        static::addGlobalScope('store', new StoreScope());
        // another way to identifier the scope
        // static::addGlobalScope(StoreScope::class);

        static::creating(function (Product $product) {
            $product->slug = Str::slug($product->name);
        });
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('status', '=', 'active');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'product_tag',
            'tag_id',
            'product_id',
            'id',
            'id'
        );
    }

    // Accessor

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://www.incathlab.com/images/products/default_product.png';
        }

        // ['http://', 'https://'] here mean if image start of any them will accept the condition.
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function getSalePercentAttribute()
    {
        if (!$this->compare_price) {
            return 0;
        }

        return round(100 - (100 * $this->price / $this->compare_price), 1);
    }

    public function scopeFilter(Builder $builder, $filters)
    {
        $options = array_merge([
            'store_id' => null,
            'category_id' => null,
            'tag_id' => null,
            'status' => 'active',
        ], $filters);

        $builder->when($options['status'], function ($builder, $value) {
            $builder->where('status', $value);
        });

        $builder->when($options['store_id'], function ($builder, $value) {
            $builder->where('store_id', '=', $value);
        });

        $builder->when($options['category_id'], function ($builder, $value) {
            $builder->where('category_id', '=', $value);
        });

        $builder->when($options['tag_id'], function ($builder, $value) {

            $builder->whereExists(function ($query) use ($value) {
                $query->select(1)
                    ->from('product_tag')
                    ->whereRaw('product_id = products.id')
                    ->where('tag_id', $value);
            });


            // $builder->whereRaw('id IN (SELECT product_id FROM product_tag WHERE tag_id =?)', $value);
            // this's best as performance, mean using EXISTS.
            //SELECT 1 it mean select anything.
            // $builder->whereRaw('EXISTS (SELECT 1 FROM product_tag WHERE tag_id =? AND product_id = products.id)', $value);


            //this's traditional way
            // $builder->whereHas('tags', function ($builder) use ($value) {
            //     $builder->where('id', '=', $value);
            // });
        });
    }
}
