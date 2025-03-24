<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterWilayah;

class Supplier extends Model
{
    use FilterWilayah;
    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'nama_kontak',
        'nomor_telepon',
        'email',
        'nomor_izin_usaha',
        'kode_provinsi',
        'kode_kabupaten',
        'kode_kecamatan',
        'kode_desa',
        'alamat',
        'lokasi_supplier',
        'alamat_gudang',
        'lokasi_gudang',
        'kabupaten_koordinator',
        'wilayah_koordinator',
        'nama_koordinator_wilayah',
        'kabupaten_sppg',
        'wilayah_koordinator_sppg',
        'sppg',
        'sppg2',
        'sppg3',
        'sppg4',
        'sppg5',
        'sppg6',
        'sppg7',
        'sppg8',
        'sppg9',
    ];

    protected $casts = [
        'kode_provinsi' => 'string',
        'kode_kabupaten' => 'string',
        'kode_kecamatan' => 'string',
    ];

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

    public function contracts() {
        return $this->hasMany(SupplierContract::class);
    }

    // untuk memastikan data yang disimpan formatnya adalah string mengunakan koma
    
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
}
