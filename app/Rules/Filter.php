<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Filter implements ValidationRule
{

    public $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), $this->filters)) {
            $fail('This Name Is Forbidden');
        }
    }


    // public function message()
    // {
    //     return 'This name is invalid';
    // }
}
