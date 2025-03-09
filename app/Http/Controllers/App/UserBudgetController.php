<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Dashboard\CreateUserBudget;
use App\Actions\Dashboard\UpdateUserBudget;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\Dashboard\BudgetData;
use App\Enums\FlashMessageAction;
use App\Models\UserBudget;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class UserBudgetController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function store(BudgetData $data, CreateUserBudget $createUserBudget): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect(route('login'));
        }

        $this->authorize('create', UserBudget::class);
        $createUserBudget->handle($user, $data);

        return $this->success(FlashMessageAction::CREATE);
    }

    public function update(BudgetData $data, UserBudget $budget, UpdateUserBudget $updateUserBudget): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect(route('login'));
        }

        try {
            $this->authorize('update', $budget);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }

        $updateUserBudget->handle($budget, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(Request $request, UserBudget $budget): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect(route('login'));
        }

        try {
            $this->authorize('delete', $budget);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        $budget->delete();

        return $this->success(FlashMessageAction::DELETE);
    }
}
