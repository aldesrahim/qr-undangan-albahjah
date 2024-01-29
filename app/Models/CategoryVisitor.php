<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryVisitor extends Pivot
{
    use HasTimestamps;
}
