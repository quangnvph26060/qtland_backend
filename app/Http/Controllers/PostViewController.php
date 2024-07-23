<?php

namespace App\Http\Controllers;

use App\Models\PostView;
use Illuminate\Http\Request;

class PostViewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);

        PostView::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'viewed_at' => now(),
        ]);

        return response()->json(['message' => 'View recorded'], 200);
    }

    public function getViews($postId)
    {
        $allTimeViews = PostView::where('post_id', $postId)->count();
        $monthlyViews = PostView::where('post_id', $postId)
            ->whereMonth('viewed_at', now()->month)
            ->count();

        return response()->json([
            'all_time_views' => $allTimeViews,
            'monthly_views' => $monthlyViews
        ]);
    }
}
