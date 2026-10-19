<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'roles', 'nik', 'department_id', 'phone'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    /**
     * Helper to check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        $all = $this->roles;
        if (empty($all) && ! empty($this->role)) {
            $all = [$this->role];
        }

        return is_array($all) && in_array($role, $all, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isJudge(): bool
    {
        return $this->hasRole('judge');
    }

    public function isFacilitator(): bool
    {
        return $this->hasRole('facilitator');
    }

    public function isCircleLeader(): bool
    {
        return $this->hasRole('circle_leader') || $this->hasRole('leader');
    }

    /**
     * Scope to find users having a specific role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereJsonContains('roles', $role)
                ->orWhere('role', $role);
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function circlesLed(): HasMany
    {
        return $this->hasMany(Circle::class, 'leader_id');
    }

    public function circlesFacilitated(): HasMany
    {
        return $this->hasMany(Circle::class, 'facilitator_id');
    }

    public function circleMemberships(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function judgeAssignments(): HasMany
    {
        return $this->hasMany(JudgeAssignment::class, 'judge_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'judge_id');
    }
}
