<?php

use Carbon\Carbon;

if (!function_exists('carbon')) {
    function carbon($time = null, $tz = null): Carbon
    {
        return new Carbon($time, $tz);
    }
}
