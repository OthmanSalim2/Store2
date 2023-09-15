<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy extends ModelPolicy
{

    public function view($user, Product $product)
    {
        // if there any exceptions will write them here.
        return $user->hasAbility('products.view');
    }
}
