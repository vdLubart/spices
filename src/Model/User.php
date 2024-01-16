<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends Model implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected $fillable = ['login'];

    public function getPassword(): ?string {
        return $this->password;
    }

    public function getRoles(): array {
        return ['ROLE_OAUTH2_SPICE', 'ROLE_USER'];
    }

    public function eraseCredentials() {
        return;
    }

    public function getUserIdentifier(): string {
        return $this->login;
    }

    public function spices(): HasMany {
        return $this->hasMany(Spice::class);
    }
}
