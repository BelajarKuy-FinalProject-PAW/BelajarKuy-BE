<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\SubMaterialResource; // Kita akan gunakan ini di show
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar semua materi.
     */
    public function index()
    {
        $materials = Material::orderBy('category')->orderBy('name')->get();
        return MaterialResource::collection($materials);
    }

    /**
     * Menampilkan detail satu materi beserta sub-materinya.
     */
    public function show(Material $material)
    {
        // Eager load relasi subMaterials
        $material->load('subMaterials');

        // Kita akan gabungkan response secara manual di sini
        return response()->json([
            'data' => [
                'id' => $material->id,
                'nama_materi' => $material->name,
                'kategori' => $material->category,
                'deskripsi_singkat' => $material->description,
                'sub_materi' => SubMaterialResource::collection($material->subMaterials),
            ]
        ]);
    }
}