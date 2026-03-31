<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'service_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    public function formulairesCrees(): HasMany
    {
        return $this->hasMany(Formulaire::class, 'created_by_user_id');
    }

    /** Accès transversal (ex. admin secrétariat) : pas de filtre par service. */
    public function hasUnscopedServiceAccess(): bool
    {
        return $this->service_code === null || $this->service_code === '';
    }

    public function administrationServiceLabel(): ?string
    {
        if ($this->hasUnscopedServiceAccess()) {
            return null;
        }

        return config('administration.services')[$this->service_code] ?? $this->service_code;
    }

    public function sameServiceAsFormulaire(Formulaire $formulaire): bool
    {
        if ($this->hasUnscopedServiceAccess()) {
            return true;
        }

        return $this->service_code === $formulaire->service_code;
    }

    /**
     * Chefs à notifier pour un dossier (même code service ; obligatoire côté comptes).
     *
     * @return Collection<int, User>
     */
    public static function chefsNotifiablesPourService(string $serviceCode): Collection
    {
        return static::role('chef_de_service')
            ->where('service_code', $serviceCode)
            ->get();
    }

    /**
     * Secrétaires à notifier pour un service donné.
     *
     * @return Collection<int, User>
     */
    public static function secretairesNotifiablesPourService(string $serviceCode): Collection
    {
        return static::role('secretaire')
            ->where('service_code', $serviceCode)
            ->get();
    }

    /**
     * Vérifie si un mot de passe en clair est déjà utilisé par un autre utilisateur.
     */
    public static function isPasswordUsedByAnotherUser(string $plainPassword, ?int $exceptUserId = null): bool
    {
        $query = static::query()->select(['id', 'password']);

        if ($exceptUserId !== null) {
            $query->where('id', '!=', $exceptUserId);
        }

        foreach ($query->cursor() as $user) {
            if (is_string($user->password) && Hash::check($plainPassword, $user->password)) {
                return true;
            }
        }

        return false;
    }

    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            if (! $user->role) {
                return;
            }

            try {
                if (Role::where('name', $user->role)->exists()) {
                    $user->syncRoles([$user->role]);
                }
            } catch (\Throwable) {
                // Tables Spatie non prêtes (ex. migrations en cours)
            }
        });
    }
}
