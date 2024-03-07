<?php

namespace Domain\QuestionManagement\Policies;

use Domain\Users\Models\User;
use Illuminate\Auth\Access\Response;
use Domain\Organization\Models\Employee;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;

class QuestionVariantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user)
    {
        return $user instanceof User || $user instanceof Employee;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, QuestionVariant $questionVariant)
    {
        return $user->organization_id === $questionVariant->organization_id || $user instanceof User || $questionVariant->status == QuestionVariantStatusEnum::Public;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create($user)
    {
        return $user instanceof User || $user instanceof Employee;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update($user, QuestionVariant $questionVariant): Response
    {
        return $this->isOwnerOfQuestionVariantOrAdmin($user, $questionVariant)
            ? Response::allow()
            : Response::deny('You do not own this question variant.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete($user, QuestionVariant $questionVariant): Response
    {
        return $this->isOwnerOfQuestionVariantOrAdmin($user, $questionVariant)
            ? Response::allow()
            : Response::deny('You do not own this question variant.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore($user, QuestionVariant $questionVariant): Response
    {
        return $this->isOwnerOfQuestionVariantOrAdmin($user, $questionVariant)
            ? Response::allow()
            : Response::deny('You do not own this question variant.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete($user, QuestionVariant $questionVariant): Response
    {
        return $this->isOwnerOfQuestionVariantOrAdmin($user, $questionVariant)
            ? Response::allow()
            : Response::deny('You do not own this question variant.');
    }

    private function isOwnerOfQuestionVariantOrAdmin($user, QuestionVariant $questionVariant): bool
    {
        return $user->organization_id === $questionVariant->organization_id || $user instanceof User;
    }
}
