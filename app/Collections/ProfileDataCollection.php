<?php

declare(strict_types=1);

namespace App\Collections;


use App\Models\ProfileData;

class ProfileDataCollection
{
    private array $profiles;

    public function __construct(array $profiles = [])
    {
        foreach ($profiles as $profile) {
            if (!$profile instanceof ProfileData) {
                continue;
            }
            $this->add($profile);
        }

    }

    public function add(ProfileData $profile): void
    {
        $this->profiles[] = $profile;
    }

    public function getAll(): array
    {
        return $this->profiles;
    }
}