<?php

namespace App\Http\Controllers;

use App\Models\CbtModule;
use App\Models\UserCbtModule;
use App\ProgressStatus;
use App\Services\EClassPurchaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EClassController extends Controller
{
    public function index(): View
    {
        $modules = CbtModule::query()
            ->published()
            ->withCount('chapters')
            ->latest('published_at')
            ->paginate(12);

        return view('e-class.index', compact('modules'));
    }

    public function show(CbtModule $module): View
    {
        abort_unless($module->is_published, 404);

        $module->load([
            'chapters' => fn ($query) => $query->select([
                'id',
                'cbt_module_id',
                'title',
                'type',
                'order_number',
            ]),
        ]);

        $hasAccess = Auth::check() && $module->isOwnedBy(Auth::user());

        return view('e-class.show', compact('module', 'hasAccess'));
    }

    public function checkout(Request $request, CbtModule $module, EClassPurchaseService $purchase): RedirectResponse
    {
        $order = $purchase->create($request->user(), $module);

        return redirect()->route('order.show', $order);
    }

    public function library(Request $request): View
    {
        $user = $request->user();
        $entitlements = UserCbtModule::query()
            ->active()
            ->whereBelongsTo($user)
            ->whereHas('module', fn (Builder $query): Builder => $query->published())
            ->with([
                'module' => function (BelongsTo $query) use ($user): void {
                    $query->published()
                        ->withCount([
                            'chapters',
                            'chapters as completed_chapters_count' => fn (Builder $chapterQuery): Builder => $chapterQuery
                                ->whereHas('chapterProgresses', fn (Builder $progressQuery): Builder => $progressQuery
                                    ->whereBelongsTo($user)
                                    ->where('status', ProgressStatus::Completed->value)),
                        ])
                        ->with([
                            'chapters' => function (HasMany $chapterQuery): void {
                                $chapterQuery->select(['id', 'cbt_module_id', 'title', 'type', 'order_number']);
                            },
                            'moduleProgresses' => function (HasMany $progressQuery) use ($user): void {
                                $progressQuery->whereBelongsTo($user);
                            },
                        ]);
                },
            ])
            ->latest('granted_at')
            ->get();

        return view('e-class.library', compact('entitlements'));
    }
}
