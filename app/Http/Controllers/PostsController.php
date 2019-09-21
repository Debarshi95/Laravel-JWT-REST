<?php

namespace App\Http\Controllers;

use App\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    public function __construct()
    {
        return $this->middleware('jwt.verify')->except(['index', 'show']);
    }
    public function index()
    {
        return response()->json([
            'posts' => Posts::all()
        ], 200);
    }

    public function show($id)
    {
        $post = Posts::find($id);
        if ($post) {
            return response()->json([
                'post' => $post
            ], 200);
        }
        return response()->json([
            'message' => 'Post doesnot exist'
        ], 403);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'body' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are required',
                'error' => $validator->errors()
            ], 422);
        }
        $post = Posts::create([
            'user_id' => Auth::user()->id,
            'title' => $request->get('title'),
            'body' => $request->get('body')
        ]);
        return response()->json([
            'message' => 'Post created',
            'post' => $post
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'body' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'All fields are required',
                'error' => $validator->errors()
            ], 422);
        }
        $post = Posts::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }
        if (Auth::user()->id == $post->user_id) {
            $post->update($request->all());
            return response()->json([
                'message' => 'Post updated',
                'post' => $post
            ], 201);
        }
        return response()->json([
            'message' => 'You can only update posts you have created'
        ], 401);
    }

    public function delete($id)
    {
        $post = Posts::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post doesnot exist'
            ], 404);
        }
        if (Auth::user()->id == $post->user_id) {
            Posts::destroy($id);
            return response()->json([
                'message' => 'Post deleted'
            ], 200);
        }
        return response()->json([
            'message' => 'You can only delete posts that you have created'
        ], 401);
    }
}
