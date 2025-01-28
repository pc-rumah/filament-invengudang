<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeluar extends Model
{

    protected $guarded = ['id'];
    /**
     * Get the barang that owns the BarangKeluar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $barang = Barang::find($model->barang_id);
            if ($model->jumlah_barang_keluar > $barang->stok) {
                throw ValidationException::withMessages([
                    'jumlah_barang_keluar' => 'Jumlah barang keluar tidak boleh melebihi stok barang yang tersedia.',
                ]);
            }

            // Kurangi stok barang
            $barang->stok -= $model->jumlah_barang_keluar;
            $barang->save();
        });
    }
}
