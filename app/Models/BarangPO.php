<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangPO extends Model
{
    protected $table = 'barang_po';
    
    protected $fillable = [
        'po_id',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'total_harga',
    ];
}
