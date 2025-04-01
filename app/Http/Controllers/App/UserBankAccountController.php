<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\BankAccount\UpdateBankAccount;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\BankAccount\BankAccountData;
use App\Data\App\BankAccount\UserBankAccountData;
use App\Enums\FlashMessageAction;
use App\Enums\FlashMessageType;
use App\Models\BankInstitution;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Services\BankService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

final readonly class UserBankAccountController
{
    use AuthorizesRequests;
    use HasRedirectWithFlashMessage;

    public function __construct(
        private BankService $bankService,
    ) {}

    public function index(Request $request): Response|RedirectResponse
    {
        try {
            $this->authorize('viewAny', UserBankAccount::class);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $bankAccounts = UserBankAccount::get();

        $rows = [];
        foreach ($bankAccounts as $bankAccount) {
            $rows[] = new UserBankAccountData(
                $bankAccount->id,
                $bankAccount->name,
                $bankAccount->balance,
                $bankAccount->access_expires_at < CarbonImmutable::now(),
                $bankAccount->status,
            );
        }

        return Inertia::render('bank-account/index', [
            'columns' => [
                'Id',
                'Name',
                'Balance',
                'Status',
            ],
            'rows' => $rows,
            // @codeCoverageIgnoreStart
            'banks' => Inertia::optional(fn (): Collection => $this->bankService->searchActiveBankInstitutions(
                $request->string('q')->value(),
            )),
            // @codeCoverageIgnoreEnd
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(BankAccountData $data, UserBankAccount $bankAccount, UpdateBankAccount $updateBankAccount): RedirectResponse
    {
        try {
            $this->authorize('update', $bankAccount);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }
        // @codeCoverageIgnoreEnd

        $updateBankAccount->handle($bankAccount, $data);

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserBankAccount $bankAccount): RedirectResponse
    {
        try {
            $this->authorize('delete', $bankAccount);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }
        // @codeCoverageIgnoreEnd

        $bankAccount->delete();

        return $this->success(FlashMessageAction::DELETE);
    }

    public function connect(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $ref = $request->string('ref');

        try {
            $this->bankService->create($user, $ref->toString());

            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::CREATE),
            );
        } catch (ModelNotFoundException) {
            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::DANGER, FlashMessageAction::CREATE),
            );
        }
    }

    public function connectRedirect(BankInstitution $bankInstitution): SymfonyResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('create', UserBankAccount::class);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $redirectLink = $this->bankService->connect($bankInstitution, $user);

        return Inertia::location($redirectLink);
    }

    public function renew(Request $request, UserBankSession $userBankSession): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $ref = $request->string('ref');

        try {
            $this->bankService->create($user, $ref->toString(), $userBankSession);

            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::RENEW),
            );
        } catch (ModelNotFoundException) {
            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::DANGER, FlashMessageAction::RENEW),
            );
        }
    }

    public function renewRedirect(UserBankAccount $userBankAccount): SymfonyResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $this->authorize('renew', $userBankAccount);
            // @codeCoverageIgnoreStart
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }
        // @codeCoverageIgnoreEnd

        $session = $userBankAccount->userBankSession;
        $institution = $session?->bankInstitution;

        abort_if($institution === null, 500);

        $redirectLink = $this->bankService->connect($institution, $user, $session);

        return Inertia::location($redirectLink);
    }
}
