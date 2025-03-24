<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterWilayah
{
    protected static function bootFilterWilayah()
    {
        static::addGlobalScope('wilayah', function (Builder $builder) {
            $user = Auth::user();
            $roles = $user->roles->pluck('name')->toArray();

            if (in_array('Admin Sistem', $roles)) {
                return;
            }

            $builder->where(function ($query) use ($user) {
                if (!empty($user->kode_provinsi)) {
                    $kodeProvinsi = is_array($user->kode_provinsi) ? $user->kode_provinsi : explode(',', $user->kode_provinsi);
                    $query->where(function ($q) use ($kodeProvinsi) {
                        foreach ($kodeProvinsi as $kode) {
                            $q->orWhereRaw("FIND_IN_SET(?, kode_provinsi)", [$kode]);
                        }
                    });
                }

                if (!empty($user->kode_kabupaten)) {
                    $kodeKabupaten = is_array($user->kode_kabupaten) ? $user->kode_kabupaten : explode(',', $user->kode_kabupaten);
                    $query->where(function ($q) use ($kodeKabupaten) {
                        foreach ($kodeKabupaten as $kode) {
                            $q->orWhereRaw("FIND_IN_SET(?, kode_kabupaten)", [$kode]);
                        }
                    });
                }

                if (!empty($user->kode_kecamatan)) {
                    $kodeKecamatan = is_array($user->kode_kecamatan) ? $user->kode_kecamatan : explode(',', $user->kode_kecamatan);
                    $query->where(function ($q) use ($kodeKecamatan) {
                        foreach ($kodeKecamatan as $kode) {
                            $q->orWhereRaw("FIND_IN_SET(?, kode_kecamatan)", [$kode]);
                        }
                    });
                }
            });
        });
    }
}
