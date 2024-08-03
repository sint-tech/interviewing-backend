<?php

namespace Domain\Vacancy\Builders;

use Domain\Organization\Models\Employee;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Support\Traits\Builder\InteractWithUser;

class VacancyBuilder extends Builder
{
    use InteractWithUser;

    public function forUser(Authenticatable $authenticated): self
    {
        if ($authenticated instanceof Employee) {
            return $this->where('organization_id', $authenticated->organization_id);
        }

        return $this;
    }

    public function whereEnded(): self
    {
        return $this->where('ended_at', '<=', now());
    }

    public function whereRunning(): self
    {
        return $this->where('started_at', '<=', now())
            ->where('ended_at', '>=', now());
    }

    public function whereSlugLike(?string $slug): self
    {
        return $this->where('slug', 'like', "%$slug%");
    }

    public function wherePublic(): self
    {
        return $this->where('is_public', true);
    }
}
