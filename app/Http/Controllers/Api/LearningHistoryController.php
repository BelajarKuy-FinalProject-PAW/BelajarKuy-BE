<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningHistory;
use App\Models\Material; // Untuk menandai materi selesai
use App\Http\Resources\LearningHistoryResource;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth; // Tidak perlu jika menggunakan $request->user()

class LearningHistoryController extends Controller
{
    /**
     * Display a listing of the authenticated user's learning history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Mengambil riwayat belajar user dengan eager loading relasi 'material'
        // dan juga 'material.topic' jika Anda ingin menampilkan nama topik
        $learningHistories = $user->learningHistories()
                                  ->with(['material' => function ($query) {
                                      $query->with('topic'); // Eager load topic dari material
                                  }])
                                  ->orderBy('completed_at', 'desc') // Urutkan berdasarkan yang terbaru diselesaikan
                                  ->orderBy('created_at', 'desc')   // Atau berdasarkan kapan record dibuat
                                  ->paginate(10); // Menggunakan paginasi

        return LearningHistoryResource::collection($learningHistories);
    }

    /**
     * Mark a material as completed by the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Material  $material // Menggunakan Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Material $material)
    {
        $user = $request->user();

        // Menggunakan updateOrCreate untuk menangani jika user sudah pernah menandai materi ini selesai
        // atau jika ingin menandainya lagi (mungkin untuk update completed_at).
        // Kunci ['user_id', 'material_id'] memastikan keunikan per user per materi.
        $learningHistory = LearningHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'material_id' => $material->id,
            ],
            [
                'completed_at' => now(), // Selalu set/update completed_at ke waktu sekarang
            ]
        );

        // Memuat relasi material (dan topic jika ada) untuk respons
        $learningHistory->load(['material' => function ($query) {
            $query->with('topic');
        }]);

        $message = $learningHistory->wasRecentlyCreated ?
                   'Materi berhasil ditandai sebagai selesai.' :
                   'Waktu penyelesaian materi diperbarui.';

        return response()->json([
            'message' => $message,
            'history' => new LearningHistoryResource($learningHistory)
        ], $learningHistory->wasRecentlyCreated ? 201 : 200); // 201 jika baru dibuat, 200 jika diupdate
    }
}