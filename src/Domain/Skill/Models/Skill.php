<?php

namespace Domain\Skill\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];


    protected static function newFactory()
    {
        return (new SkillFactory());
    }
}
