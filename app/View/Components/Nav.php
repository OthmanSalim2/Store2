<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Nav extends Component
{

    public $items;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->items = $this->prepareItems(config('nav'));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // here if was the property public will pass to component
        return view('components.nav');
    }

    protected function prepareItems($items)
    {
        $user = Auth::user();

        foreach ($items as $key => $item) {
            if (isset($item['ability']) && !$user->can($item['ability'])) {
                unset($items[$key]);
            }
        }

        return $items;
    }
}
