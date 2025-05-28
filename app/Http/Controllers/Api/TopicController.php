<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic; // Import model Topic
use App\Http\Resources\TopicResource; // Import TopicResource
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the topics.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Mengambil semua topik, diurutkan berdasarkan nama, dan diformat oleh TopicResource
        $topics = Topic::orderBy('name')->get();
        return TopicResource::collection($topics);
    }

    // Method lain (store, show, update, destroy) untuk kedepannya
    // untuk CRUD topik oleh admin, tapi untuk preferensi user, 'index' sudah cukup.
}