<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TopicResource; 

class UserPreferenceController extends Controller
{
    /**
     * Display the authenticated user's current learning preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Mengambil topik-topik yang sudah dipilih oleh user yang sedang login
        // melalui relasi 'topics' yang sudah kita definisikan di model User.
        $userPreferences = $request->user()->topics()->orderBy('name')->get();
        return TopicResource::collection($userPreferences);
    }

    /**
     * Store or update the authenticated user's learning preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
{
    // Validasi input: kita mengharapkan array dari topic_ids
    $validatedData = $request->validate([
        'topic_ids' => ['sometimes', 'array'],
        'topic_ids.*' => ['integer', 'exists:topics,id'], 
    ]);

    $topicIds = $validatedData['topic_ids'] ?? [];
    $request->user()->topics()->sync($topicIds);

    $updatedPreferences = $request->user()->topics()->orderBy('name')->get();

    return response()->json([
        'message' => 'Preferensi belajar berhasil diperbarui.',
        'preferences' => TopicResource::collection($updatedPreferences)
    ]);
}
}