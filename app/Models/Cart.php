<?php

namespace App\Models;

use App\Observers\CartObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = [
        'cookie_id', 'user_id', 'product_id', 'quantity', 'options',
    ];

    protected $casts = [
        'options' => 'json',
    ];

    // Events (Observers)
    // creating, created, updating, updated, saving, saved
    // deleting, deleted, restoring, restored, retrieved
    public static function booted()
    {
        static::observe(CartObserver::class);
        // static::creating(function (Cart $cart) {
        //    $cart->id = Str::uuid();
        // });

        static::addGlobalScope('cookie_id', function (Builder $builder) {
            $builder->where('cookie_id', '=', Cart::getCookieId());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class)
            ->withDefault([
                'name' => 'Anonymous',
            ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function getCookieId()
    {
        $cookie_id = Cookie::get('cart_id');

        if ($cookie_id) {
            $cookie_id = Str::uuid();
            // addDays(30) == addMonths(1) the same output.
            Cookie::queue('cart_id', $cookie_id, 30 * 24 * 60);
        }

        return $cookie_id;
    }
}
