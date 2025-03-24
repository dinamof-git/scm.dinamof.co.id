<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterPoDo;

class PO extends Model
{
    use FilterPoDo;
    protected $table = 'purchase_order';

    protected $fillable = [
        'supplier_id',
        'user_id',
        'tanggal_po',
        'batas_pengiriman',
        'catatan',
        'status'
    ];

    public function barang()
    {
        return $this->hasMany(BarangPO::class, 'po_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function contracts() {
        return $this->hasMany(SupplierContract::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
