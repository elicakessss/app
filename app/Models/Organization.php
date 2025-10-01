<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    /**
     * Get the students that belong to this organization.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'organization_student')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}
