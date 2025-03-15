<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\BankAccount\UpdateBankAccount;
use App\Concerns\HasRedirectWithFlashMessage;
use App\Data\App\BankAccount\BankAccountData;
use App\Data\App\BankAccount\BankInstitutionData;
use App\Data\App\BankAccount\UserBankAccountData;
use App\Enums\FlashMessageAction;
use App\Enums\FlashMessageType;
use App\Exceptions\AbstractAppException;
use App\Models\BankInstitution;
use App\Models\UserBankAccount;
use App\Models\UserBankSession;
use App\Services\BankService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
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
        $user = $request->user();

        if ($user === null) {
            return redirect(route('login'));
        }

        try {
            $this->authorize('viewAny', UserBankAccount::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

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
            'banks' => Inertia::optional(fn (): Collection => $this->search($request)),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, UserBankAccount $bankAccount, UpdateBankAccount $updateBankAccount): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('update', $bankAccount);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::UPDATE);
        }

        $updateBankAccount->handle($bankAccount, BankAccountData::from($request));

        return $this->success(FlashMessageAction::UPDATE);
    }

    public function destroy(UserBankAccount $bankAccount): RedirectResponse
    {
        $user = auth()->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('delete', $bankAccount);
        } catch (AuthorizationException) {
            return $this->error(FlashMessageAction::DELETE);
        }

        $bankAccount->delete();

        return $this->success(FlashMessageAction::DELETE);
    }

    public function connect(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $ref = $request->string('ref');

        try {
            $this->bankService->create($user, $ref->toString());

            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::CREATE),
            );
        } catch (AbstractAppException) {
            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::DANGER, FlashMessageAction::CREATE),
            );
        }
    }

    public function connectRedirect(BankInstitution $bankInstitution): SymfonyResponse|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('create', UserBankAccount::class);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $redirectLink = $this->bankService->connect($bankInstitution, $user);

        return Inertia::location($redirectLink);
    }

    public function renew(Request $request, UserBankSession $userBankSession): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $ref = $request->string('ref');

        try {
            $this->bankService->create($user, $ref->toString(), $userBankSession);

            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::SUCCESS, FlashMessageAction::RENEW),
            );
        } catch (AbstractAppException) {
            return redirect()->route('bank-account.index')->with(
                'flash',
                $this->flashMessage(FlashMessageType::DANGER, FlashMessageAction::RENEW),
            );
        }
    }

    public function renewRedirect(UserBankAccount $userBankAccount): SymfonyResponse|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        try {
            $this->authorize('renew', $userBankAccount);
        } catch (AuthorizationException) {
            return $this->errorSubscription();
        }

        $session = $userBankAccount->userBankSession;
        $institution = $session?->bankInstitution;

        abort_if($institution === null, 500);

        $redirectLink = $this->bankService->connect($institution, $user, $session);

        return Inertia::location($redirectLink);
    }

    /**
     * @return Collection<int, BankInstitutionData>
     */
    private function search(Request $request): Collection
    {
        return BankInstitution::whereLike(
            'name',
            '%'.$request->str('q')->value().'%',
        )->limit(25)
            ->get()
            ->map(
                fn (BankInstitution $bankInstitution): BankInstitutionData => new BankInstitutionData(
                    $bankInstitution->id,
                    $bankInstitution->name,
                    $bankInstitution->logo_url,
                    $bankInstitution->countries === null
                        ? null
                        : implode(', ', $bankInstitution->countries),
                ),
            );
    }
}
