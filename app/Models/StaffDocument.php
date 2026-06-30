<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffDocument extends Model
{
    protected $fillable = [
        'staff_id', 'uploaded_by', 'title', 'category',
        'file_path', 'original_name', 'mime_type', 'size',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'cv'          => 'CV / Resume',
            'id'          => 'ID Document',
            'contract'    => 'Contract',
            'certificate' => 'Certificate',
            default       => 'Other',
        };
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
