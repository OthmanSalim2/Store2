<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class InvalidOrderException extends Exception
{

    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // this function it use hoe to return the exception.
    public function render(Request $request)
    {
        return Redirect::route('home')
            ->withInput()
            ->withErrors([
                'message' => $this->getMessage()
            ])
            ->with('info', $this->getMessage());
    }
}
