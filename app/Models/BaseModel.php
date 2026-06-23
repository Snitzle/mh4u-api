<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Base class for the imported MH4U reference data. The data is read-only and
 * carries no created_at/updated_at columns. Models are unguarded because the
 * only writes are the trusted import seeder and test factories — never user input.
 */
abstract class BaseModel extends Model
{
    /** @use HasFactory<Factory<static>> */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];
}
