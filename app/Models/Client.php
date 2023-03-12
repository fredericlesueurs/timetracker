<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    protected $fillable = [
        'name',
        'contact_name',
        'description',
        'comments',
        'address',
        'zip_code',
        'city',
        'phone',
        'email',
    ];

    protected array $cascadeDeletes = ['projects'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function delete(): ?bool
    {
        $this->comments()->delete();

        return parent::delete();
    }
}
