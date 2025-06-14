<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\MaterialResource; // <-- Ganti dari TopicResource

class UserPreferenceController extends Controller
{
    /**
     * Display the authenticated user's current learning preferences.
     */
    public function index(Request $request)
    {
        // Mengambil materi-materi yang sudah dipilih oleh user
        $userPreferences = $request->user()->preferences()->orderBy('name')->get();
        return MaterialResource::collection($userPreferences); // <-- Ganti dari TopicResource
    }

    /**
     * Store or update the authenticated user's learning preferences.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'material_ids' => ['sometimes', 'array'],           // <-- Ganti dari topic_ids
            'material_ids.*' => ['integer', 'exists:materials,id'], // <-- Ganti dari topics
        ]);

        $materialIds = $validatedData['material_ids'] ?? [];
        $request->user()->preferences()->sync($materialIds); // Gunakan relasi preferences() yang baru

        $updatedPreferences = $request->user()->preferences()->orderBy('name')->get();

        return response()->json([
            'message' => 'Preferensi belajar berhasil diperbarui.',
            'preferences' => MaterialResource::collection($updatedPreferences) // <-- Ganti dari TopicResource
        ]);
    }
}