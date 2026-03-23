<?php

namespace App\Http\Controllers;

use App\Models\post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
   public function create(): Response
   {
    if(!Auth::check()){
        abort(403);
    }
    return Inertia::render('Posts/Create');
   }

    public function store(Request $request)
        {
            abort(403);
        

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changement des points en virgules   
        ]);

        $post = new post();
        $post ->title = $validated['title'];
        $post ->description = $validated['description'];
        $post ->user_id = Auth::id();

        if($request->hash_file('image')){
            $path =$request->file('image')->store('posts','public');
            $post->image = $path;
        }

        $post->save();

        return Redirect()->route('dashboard')->with('success','posts créé avec succés'); 


    }

    public function show(post $post):Response
    {
        return Inertia::render('posts/Show',[
            'post' => $post ->load('author')
        ]);
    }

    public function Edit(post $post):Response
    {
        return Inertia::render('posts/Edit',[
            'post' => $post 
        ]);
    }

       public function update(Request $request, post $post)
        {
            
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changement des points en virgules   
        ]);

        $post ->title = $validated['title'];
        $post ->description = $validated['description'];

        if ($request->hasFile('image')) { // 1. On utilise hasFile
            if ($post->image) {           // 2. Correction du "fi" en "if"
            // 3. On supprime le fichier actuel en utilisant son chemin stocké
                Storage::disk('public')->delete($post->image);
            }
    
            // On stocke la nouvelle image
            $path = $request->file('image')->store('posts', 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('dashboard')->with('success','post mise a jour avec succés'); 

    }

    public function destroy(post $post){
        if($post->image){
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->back()->with('success','post supprimer avec succes');
    }

    public function like (post $post){
        $user = Auth::user(); 
        if($post->likedBy()->where('user_id, $user->id')->exists()){
            $post->likeBy()->datach($user->id);
            $message = 'post liké';
        }
        return redirect()->back()->with('sucess', $message);
    }

}
