<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Estimation;
use App\Services\EstimationCalculator;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $calculator = new EstimationCalculator;

        $estimations = Estimation::query()
            ->with([
                'projectType',
                'setup.prices',
                'pages.blocks.priceSets',
                'addons',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEstimations = $estimations->count();
        $thisMonthCount = $estimations->filter(fn ($e) => $e->created_at->isCurrentMonth())->count();

        $totalFixedRevenue = 0;
        $totalHours = 0;
        $thisMonthRevenue = 0;
        $thisMonthHours = 0;

        foreach ($estimations as $estimation) {
            $totals = $calculator->calculateTotals($estimation);
            $estimation->_total_price = $totals['total_price'];
            $estimation->_total_time = $totals['total_time'];

            if ($estimation->type === 'hour') {
                $totalHours += $totals['total_time'];
                if ($estimation->created_at->isCurrentMonth()) {
                    $thisMonthHours += $totals['total_time'];
                }
            } else {
                $totalFixedRevenue += $totals['total_price'];
                if ($estimation->created_at->isCurrentMonth()) {
                    $thisMonthRevenue += $totals['total_price'];
                }
            }
        }

        $recentEstimations = $estimations->take(5);

        $byProjectType = $estimations
            ->groupBy(fn ($e) => $e->projectType?->name ?? 'Sans type')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'icon' => $group->first()->projectType?->icon,
            ])
            ->sortByDesc(fn ($item) => $item['count']);

        $subscription = $user->activeSubscription;
        $plan = $subscription?->plan;

        $customBlocksCount = Block::query()->count();

        return view('dashboard', compact(
            'user',
            'totalEstimations',
            'thisMonthCount',
            'totalFixedRevenue',
            'totalHours',
            'thisMonthRevenue',
            'thisMonthHours',
            'recentEstimations',
            'byProjectType',
            'plan',
            'subscription',
            'customBlocksCount',
        ));
    }
}
