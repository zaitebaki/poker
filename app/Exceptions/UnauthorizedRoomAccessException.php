<?php

namespace App\Exceptions;

use Exception;
use Log;
use Auth;

class UnauthorizedRoomAccessException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        $id = Auth::id();
        Log::critical("user_id = $id " . $this->message);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        abort('403');
    }
}
