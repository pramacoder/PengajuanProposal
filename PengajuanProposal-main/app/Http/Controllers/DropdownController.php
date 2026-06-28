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
        // Check if parameter is ID or nama_fakultas
        if (is_numeric($id_fakultas)) {
            // If numeric, treat as ID
            $prodis = Prodi::where('id_fakultas', $id_fakultas)
                          ->orderBy('nama_prodi')
                          ->get();
        } else {
            // If not numeric, treat as nama_fakultas
            $fakultas = Fakultas::where('nama_fakultas', $id_fakultas)->first();
            if ($fakultas) {
                $prodis = Prodi::where('id_fakultas', $fakultas->id_fakultas)
                              ->orderBy('nama_prodi')
                              ->get();
            } else {
                $prodis = collect();
            }
        }
        
        return response()->json($prodis);
    }
}
