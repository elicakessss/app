<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'name',
        'logo',
        'description',
        'year',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    /**
     * Get the user who created this organization.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that owns this organization.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

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
     * Get the ranks for this organization.
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}
