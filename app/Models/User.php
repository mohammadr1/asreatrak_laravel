<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
        protected $fillable = [
            'first_name',
            'last_name',
            'slug',
            'email',
            'phone',
            'password',
            'profile_image',
            'is_active',
            'status',
            'bio',
            'social_media',
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'social_media' => 'array',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->hasRole(['Admin', 'Editor', 'Reporter']);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function reportedNews()
    {
        return $this->hasMany(News::class, 'reporter_id');
    }

    public function createdNews()
    {
        return $this->hasMany(News::class, 'created_by');
    }

    public function approvedNews()
    {
        return $this->hasMany(News::class, 'approved_by');
    }
}
