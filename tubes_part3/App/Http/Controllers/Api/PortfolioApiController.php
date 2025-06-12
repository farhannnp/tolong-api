<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Portfolio;

class PortfolioApiController extends Controller
{
    public function index()
    {
        return response()->json(Portfolio::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'link' => 'nullable|string',
            'file_path' => 'nullable|string'
        ]);

        $portfolio = Portfolio::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'file_path' => $request->file_path,
        ]);

        return response()->json($portfolio, 201);
    }

    public function show($id)
    {
        try {
            $portfolio = Portfolio::where('user_id', Auth::id())
                                ->with('skills')
                                ->find($id);

            if (!$portfolio) {
                return response()->json(['message' => 'Portfolio not found or unauthorized.'], 404);
            }

            return response()->json($portfolio, 200);

        } catch (\Exception $e) {
            Log::error('Error fetching single portfolio item via API:', [
                'user_id' => Auth::id(),
                'portfolio_id' => $id,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['message' => 'Failed to fetch portfolio item due to a server error.'], 500);
        }
    }
}