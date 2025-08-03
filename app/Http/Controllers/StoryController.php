<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;

class StoryController  extends Controller
{
    public function index()
    {
        $stories = Story::latest()->get();
        return view('home', compact('stories'));
    }
 public function show($id)
    {
         $story = Story::findOrFail($id);

    if (request()->ajax()) {
        return response()->json([
            'title' => $story->title,
            'image' => $story->image,
            'content' => $story->content,
        ]);
    }

    return view('show', compact('story'));
    }
    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
             $image = $request->file('image');
    $path = $image->store('/storage', 'public'); 
   
    $validated['image'] = $path;
        }

        Story::create($validated);

        return redirect()->route('home')->with('success', 'Story added successfully!');
    }
}