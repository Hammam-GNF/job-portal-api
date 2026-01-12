<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class JobListing extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = ['company_id', 'category_id', 'title', 'description', 'salary_min', 'salary_max', 'location', 'type', 'status'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
