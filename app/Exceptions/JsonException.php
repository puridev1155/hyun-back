<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonException extends Exception
{
    //
    /**
     * report the exception
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception as an GTTP response
     *
     * @param \Illuminate\Http\Request  $request
     */
    public function render($equest)
    {
        return new JsonResponse([
            'errors' => [
                'message' => $this->getMessage(),
            ]
            ], $this->code);
    }
}
