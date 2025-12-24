<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'user_message',
        'bot_response',
        'intent',
        'sentiment',
        'confidence',
        'entities',
        'context',
        'language',
    ];

    protected $casts = [
        'entities' => 'array',
        'context' => 'array',
        'confidence' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get conversations for a specific session.
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get conversations for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get recent conversations to maintain context.
     */
    public function scopeRecent($query, int $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get conversations by sentiment.
     */
    public function scopeBySentiment($query, string $sentiment)
    {
        return $query->where('sentiment', $sentiment);
    }

    /**
     * Get conversations by intent.
     */
    public function scopeByIntent($query, string $intent)
    {
        return $query->where('intent', $intent);
    }
}
