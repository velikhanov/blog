<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('auth.user.post', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // $request->validate([
      //       'title' => '|required|unique:posts|max:255',
      //       'content' => 'required',
      //   ]);

      $data = $request->all();
      $data['user_id'] = Auth::user()->id;

      if ($request->hasFile('img')){
        // unset($post['img']);
        $data['img'] = 'img_'.Auth::user()->id.time().'.'.$request->file('img')->getClientOriginalExtension();
        // $request->file('img')->storeAs('posts', $post['img']);
        $request->file('img')->storeAs('posts', $data['img']);
        }

        $post = Post::create($data);

        return redirect()->route('post', ['category' => $post->category->code, 'id' => $post->id])->with('success', 'Your post added successfuly!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    // public function show(Post $post)
    // {
    //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
      if(Auth::user()->id !== $post->user_id) {
         return redirect()->route('index')->with('warning', 'You do not have permission to edit the post!');
      } else {
         $categories = Category::all();
         return view('auth.user.post', compact('categories', 'post'));
      }
        // $categories = Category::all();
        // return view('auth.user.post', compact('categories', 'post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
      $data = $request->all();
      $data['user_id'] = Auth::user()->id;


      if ($request->hasFile('img')){
        Storage::disk('public')->exists('posts/'.$post->img)?Storage::disk('public')->delete('posts/'.$post->img):NULL;
        unset($data['img']);
        $data['img'] = 'img_'.Auth::user()->id.time().'.'.$request->file('img')->getClientOriginalExtension();
        $request->file('img')->storeAs('posts', $data['img']);
        }

        $post->update($data);
        return redirect()->route('post', ['category' => $post->category->code, 'id' => $post->id])->with('success', 'Your post updated successfuly!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
      Storage::disk('public')->exists('posts/'.$post->img)?Storage::disk('public')->delete('posts/'.$post->img):NULL;
      $post->delete();
      return redirect()->route('index')->with('danger','Your post deleted successfuly!');
    }
}