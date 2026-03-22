<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Facades\Auth;

class post extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'user_id',
    ];

    protected $appends = ['is_liked', 'likes_count'];

    protected $with = ['likedBy'];

   public function auther():BelongsTo
   {
    return $this->belongsTo(user::class, "user_id");
   }
    public function likeBy():BelongsToMany
   {
    return $this->belongsToMany(user::class, "post_like");
   }

   public function getIsLikedAttribute():bool
   {
    return Auth::check() && $this->likeBy->contains('id', Auth::id());
   }
    public function getIsLikesCountAttribute():int
   {
    return $this->likeBy()->count();
   }

}
