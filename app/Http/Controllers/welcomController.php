<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PharIo\Manifest\Author;
use Inertia\Response;

class welcomController extends Controller
{
    public function index():Response
    {
        $posts = post::with('author')->latest()->get();

        return Inertia::render('Welcom',[
            'posts'=> $posts,
            'camRegister' => config('services.registration.enabled',true)
        ]);
    }
}
