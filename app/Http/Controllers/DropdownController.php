<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultas;
use App\Models\Prodi;

class DropdownController extends Controller
{
    public function getFakultas()
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        return response()->json($fakultas);
    }

    public function getProdi($id_fakultas)
    {
        $prodis = Prodi::where('id_fakultas', $id_fakultas)
                      ->orderBy('nama_prodi')
                      ->get();
        return response()->json($prodis);
    }
}
