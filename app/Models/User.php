<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'kode_provinsi' => 'string',
        'kode_kabupaten' => 'string',
        'kode_kecamatan' => 'string',
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
        ];
    }
    
    // relasi
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'kode_provinsi', 'kode_provinsi');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kode_kabupaten', 'kode_kabupaten');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kode_kecamatan', 'kode_kecamatan');
    }

    // data disimpan formatnya adalah string mengunakan koma
    
    public function setKodeProvinsiAttribute($value)
    {
        $this->attributes['kode_provinsi'] = implode(',', array_map('strval', (array) $value));
    }

    public function setKodeKabupatenAttribute($value)
    {
        $this->attributes['kode_kabupaten'] = implode(',', array_map('strval', (array) $value));
    }

    public function setKodeKecamatanAttribute($value)
    {
        $this->attributes['kode_kecamatan'] = implode(',', array_map('strval', (array) $value));
    }

    // get data
    public function getKodeProvinsiAttribute($value)
    {
        return explode(',', $value);
    }

    public function getKodeKabupatenAttribute($value)
    {
        return explode(',', $value);
    }

    public function getKodeKecamatanAttribute($value)
    {
        return explode(',', $value);
    }
}
