<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierContract extends Model
{
    protected $table = 'supplier_contract';
    
    protected $fillable = [
        'supplier_id',
        'nomor_kontrak',
        'tanggal_kontrak',
        'tanggal_mulai_kontrak',
        'tanggal_berakhir_kontrak',
        'jenis_barang',
        'sanksi_penalti',
        'ketentuan_pembayaran',
        'hak_kewajiban',
        'copy_kontrak_path',
        'photo_kantor_path',
        'photo_gudang_path',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
