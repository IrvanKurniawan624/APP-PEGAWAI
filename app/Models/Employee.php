<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [
        "id"
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'departmen_id');
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'jabatan_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
