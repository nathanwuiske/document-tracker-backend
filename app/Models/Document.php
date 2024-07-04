<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Document
 *
 * @mixin Model
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read Carbon $expires_at
 * @property int $owner_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $owner
 *
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document query()
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereExpiresAt($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document whereOwnerId($value)
 * @method static Builder|Document whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Document extends Model
{
    use HasFactory;

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scopeOwnedByUser(Builder $query): Builder
    {
        return $query->where('owner_id', Auth::id());
    }

    public function scopeExpiringSoon(Builder $query): void
    {
        $query->whereBetween('expires_at', [Carbon::today(), Carbon::today()->addDays(7)]);
    }

    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<', Carbon::today());
    }

    protected function expiryForHumans(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Carbon::parse($attributes['expires_at'])->diffForHumans(),
        );
    }
}
