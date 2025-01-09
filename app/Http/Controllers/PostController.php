<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $posts = Post::when($request->category_id, function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })->paginate(12);
        
        return view('pages.works.index', [
            'posts' => $posts,
            'request' => $request 
        ]);
    }

    public function search(Request $request){
        
        if($request->q) {
            $posts = Post::when($request->q, function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . strval($request->q) . '%');
            })->paginate(100);
        } else {
            $posts = Post::whereNull('title')->paginate(10);
        }
        
        return view('pages.works.search', [
            'posts' => $posts,
            'request' => $request 
        ]);
    }

   
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $post = Post::where('id', $id)
        ->when($request->category_id, function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })->first();

        $previousPost = Post::selectRaw('id')->where('id', '<', $id)
        ->when($request->category_id, function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })
        ->orderBy('id', 'desc')->first();
        $nextPost = Post::selectRaw('id')->where('id', '>', $id)
        ->when($request->category_id, function ($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })
        ->orderBy('id', 'asc')->first();


        return view('pages.works.show', [
            'post' => $post,
            'prev' => $previousPost,
            'next' => $nextPost,
            'request' => $request 
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
