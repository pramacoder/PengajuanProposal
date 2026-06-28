<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';
    protected $primaryKey = 'id_fakultas';
    protected $fillable = ['nama_fakultas', 'kode_fakultas'];

    public function prodis()
    {
        return $this->hasMany(Prodi::class, 'id_fakultas', 'id_fakultas');
    }

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class, 'fakultas_mhs', 'nama_fakultas');
    }
}
