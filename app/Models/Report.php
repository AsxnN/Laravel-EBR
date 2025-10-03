<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description', 
        'slug',
        'metadata',
        'status',
        'published_at',
        'sent_at',
        'external_id',
        'created_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    // Relaciones
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function charts()
    {
        return $this->hasMany(ReportChart::class)->orderBy('order');
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value) . '-' . time();
        }
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // Helpers
    public function getTotalChartsAttribute()
    {
        return $this->charts()->count();
    }

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'sent' => 'Enviado'
        ][$this->status] ?? 'Desconocido';
    }
}