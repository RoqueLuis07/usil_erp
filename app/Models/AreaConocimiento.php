<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class AreaConocimiento extends Model implements Auditable
{
    protected $table = "areas_conocimientos";// <-- El nombre personalizado

    use HasFactory, \OwenIt\Auditing\Auditable;

}
