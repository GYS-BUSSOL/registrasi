<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = "trn_registration";
    protected $fillable = ["reg_id", "employee_id", "is_flag", "created_at", "updated_at", "updated_by"];
}
