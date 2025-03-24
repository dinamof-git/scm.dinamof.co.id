<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterPoDo
{
    protected static function bootFilterPoDo()
    {
        static::addGlobalScope('userPO', function (Builder $builder) {
            $user = Auth::user();
            $roles = $user->roles->pluck('name')->toArray();
            // dd($roles);

            if (in_array('SPPG', $roles)) {
                // SPPG hanya bisa melihat data dirinya sendiri
                $builder->where('user_id', $user->id);
            } elseif (in_array('Staff Wilayah', $roles)) {
                $kodeKecamatan = $user->kode_kecamatan; // Jika string, ubah ke array

                $builder->whereHas('user', function ($query) use ($kodeKecamatan) {
                    $query->where(function ($q) use ($kodeKecamatan) {
                        foreach ($kodeKecamatan as $kode) {
                            $q->orWhereRaw("FIND_IN_SET(?, kode_kecamatan)", [$kode]);
                        }
                    });
                });
            }
        });
    }
}
