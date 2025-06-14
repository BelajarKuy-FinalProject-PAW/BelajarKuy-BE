<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\SubMaterial;
use App\Http\Requests\StoreSubMaterialRequest;
use App\Http\Requests\UpdateSubMaterialRequest;
use App\Http\Resources\SubMaterialResource;
use Illuminate\Support\Facades\Storage; // Untuk menghapus file jika perlu

class SubMaterialController extends Controller
{
    /**
     * Menampilkan detail satu sub-materi.
     */
    public function show(SubMaterial $subMaterial)
    {
        return new SubMaterialResource($subMaterial);
    }

    /**
     * Membuat sub-materi baru yang terkait dengan sebuah materi utama.
     */
    public function store(StoreSubMaterialRequest $request, Material $material)
    {
        // Logika untuk upload file akan lebih kompleks. Untuk saat ini, kita asumsikan
        // 'file_path' dan 'video_path' adalah string path yang dikirim dari client.
        $subMaterial = $material->subMaterials()->create($request->validated());

        return new SubMaterialResource($subMaterial);
    }

    /**
     * Memperbarui sub-materi yang ada.
     */
    public function update(UpdateSubMaterialRequest $request, SubMaterial $subMaterial)
    {
        $subMaterial->update($request->validated());

        return new SubMaterialResource($subMaterial->fresh());
    }

    /**
     * Menghapus sub-materi yang ada.
     */
    public function destroy(SubMaterial $subMaterial)
    {
        // Opsional: Hapus juga file fisik dari storage jika ada
        if ($subMaterial->file_path) {
            Storage::disk('public')->delete($subMaterial->file_path);
        }
        if ($subMaterial->video_path) {
            Storage::disk('public')->delete($subMaterial->video_path);
        }

        $subMaterial->delete();

        return response()->json(['message' => 'Sub-materi berhasil dihapus.'], 200);
    }
}