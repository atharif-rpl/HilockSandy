<?php

// app/Models/User.php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Jika ingin verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // HAPUS ATAU KOMENTARI BARIS INI

class User extends Authenticatable // implements MustVerifyEmail (opsional)
{
    // use HasApiTokens, HasFactory, Notifiable; // HAPUS 'HasApiTokens' DARI SINI
    use HasFactory, Notifiable; // Modifikasi menjadi seperti ini

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

}