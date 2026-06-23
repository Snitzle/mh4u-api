<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Base class for the imported MH4U reference data. The data is read-only and
 * carries no created_at/updated_at columns.
 */
abstract class BaseModel extends Model
{
    public $timestamps = false;
}
