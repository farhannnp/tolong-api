<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Skill; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{

    public function index()
    {
        $portfolios = Portfolio::where('user_id', Auth::id())
                                ->with('skills') 
                                ->latest()
                                ->paginate(10); 
        return view('portfolio.index', compact('portfolios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,id',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('portfolios', 'public');
        }

        $portfolio = Portfolio::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'file_path' => $filePath,
        ]);

        $portfolio->skills()->attach($request->skills);

        return redirect()->route('profile')->with('success', 'Portfolio added successfully!');
    }

    public function edit(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit portfolio ini.');
        }

        dd($portfolio);

        $skills = Skill::orderBy('name')->get();
        $portfolioSkills = $portfolio->skills->pluck('id')->toArray();

        return view('portfolio.edit', compact('portfolio', 'skills', 'portfolioSkills'));
    }


    public function update(Request $request, Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,id',
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
        ];

        if ($request->hasFile('file')) {
            $updateData['file_path'] = $request->file('file')->store('portfolios', 'public');
        }

        $portfolio->update($updateData);
        $portfolio->skills()->sync($request->skills);

        return redirect()->route('profile')->with('success', 'Portfolio updated successfully!');
    }

    public function destroy(Portfolio $portfolio)
    {
        // Check if user owns the portfolio
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $portfolio->delete();

        return redirect()->route('profile')->with('success', 'Portfolio deleted successfully!');
    }
}