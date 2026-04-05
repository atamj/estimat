<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\Estimation;
use App\Models\Template;
use App\Models\TranslationConfig;
use App\Services\EstimationCalculator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EstimationController extends Controller
{
    public function index()
    {
        return view('estimations.index');
    }

    public function create()
    {
        $projectTypes = \App\Models\ProjectType::all();
        $currencies = Currency::cases();
        $defaultCurrency = auth()->user()?->default_currency ?? 'EUR';
        $templates = Template::where('user_id', auth()->id())
            ->with(['projectType', 'pages.blocks'])
            ->latest()
            ->get();

        return view('estimations.create', compact('projectTypes', 'currencies', 'defaultCurrency', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'type' => 'required|in:hour,fixed',
            'currency' => 'required|in:'.implode(',', array_column(Currency::cases(), 'value')),
            'template_id' => 'nullable|exists:templates,id',
        ]);

        $projectTypeId = $request->project_type_id;
        $user = $request->user();
        $config = null;
        if ($user) {
            $config = TranslationConfig::where('user_id', $user->id)
                ->where('project_type_id', $projectTypeId)
                ->first();

            if (! $config && $projectTypeId) {
                $config = TranslationConfig::where('user_id', $user->id)
                    ->whereNull('project_type_id')
                    ->first();
            }
        }
        if ($user) {
            $subscription = $user->activeSubscription;
            $plan = $subscription?->plan;

            if ($plan && $plan->max_estimations !== -1) {
                $count = \App\Models\Estimation::where('user_id', $user->id)->count();
                if ($count >= $plan->max_estimations) {
                    return redirect()->route('estimations.index')->with('error', 'Vous avez atteint la limite d\'estimations de votre plan.');
                }
            }
        }

        $estimation = Estimation::create([
            'user_id' => $user?->id,
            'client_name' => $request->client_name,
            'project_name' => $request->project_name,
            'project_type_id' => $projectTypeId,
            'type' => $request->type,
            'currency' => $request->currency,
            'translation_fixed_price' => $config?->default_fixed_price ?? 0,
            'translation_fixed_hours' => $config?->default_fixed_hours ?? 0,
            'translation_percentage' => $config?->default_percentage ?? 0,
            'translation_type' => $request->type,
        ]);

        $template = $request->template_id
            ? Template::where('id', $request->template_id)->where('user_id', $user?->id)->first()
            : null;

        if ($template) {
            foreach ($template->pages as $page) {
                $newPage = $estimation->pages()->create([
                    'name' => $page->name,
                    'type' => $page->type,
                    'order' => $page->order,
                    'quantity' => $page->quantity,
                ]);

                foreach ($page->blocks as $block) {
                    $newPage->blocks()->attach($block->block_id, [
                        'quantity' => $block->quantity,
                        'order' => $block->order,
                        'price_programming' => $block->price_programming,
                        'price_integration' => $block->price_integration,
                        'price_field_creation' => $block->price_field_creation,
                        'price_content_management' => $block->price_content_management,
                    ]);
                }
            }

            foreach ($template->addons as $addon) {
                $estimation->addons()->attach($addon->id);
            }
        } else {
            // Création automatique du Header et du Footer
            $estimation->pages()->create(['name' => 'Site Header', 'type' => 'header', 'order' => 0, 'quantity' => 1]);
            $estimation->pages()->create(['name' => 'Site Footer', 'type' => 'footer', 'order' => 99, 'quantity' => 1]);
        }

        $message = $template ? 'Estimation créée depuis le gabarit « '.$template->name.' ».' : null;

        return redirect()->route('estimations.builder', $estimation)->with('message', $message);
    }

    public function builder(Estimation $estimation)
    {
        $user = auth()->user();
        if ($user && $estimation->user_id !== $user->id) {
            abort(403);
        }

        return view('estimations.builder', compact('estimation'));
    }

    public function show(Estimation $estimation)
    {
        $calculator = new EstimationCalculator;
        $totals = $calculator->calculateTotals($estimation);

        return view('estimations.show', compact('estimation', 'totals'));
    }

    public function destroy(Estimation $estimation)
    {
        $user = auth()->user();
        if ($user && $estimation->user_id !== $user->id) {
            abort(403);
        }
        $estimation->delete();

        return redirect()->route('estimations.index')->with('message', 'Estimation supprimée avec succès.');
    }

    public function duplicate(Estimation $estimation)
    {
        $user = auth()->user();
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;

        if ($plan && $plan->max_estimations !== -1) {
            $count = \App\Models\Estimation::where('user_id', $user->id)->count();
            if ($count >= $plan->max_estimations) {
                return redirect()->route('estimations.index')->with('error', 'Vous avez atteint la limite d\'estimations de votre plan.');
            }
        }

        $newEstimation = $estimation->replicate();
        $newEstimation->client_name .= ' (Copie)';
        $newEstimation->save();

        // Dupliquer les pages et leurs blocs
        foreach ($estimation->pages as $page) {
            $newPage = $page->replicate();
            $newPage->estimation_id = $newEstimation->id;
            $newPage->save();

            foreach ($page->blocks as $block) {
                $newPage->blocks()->attach($block->id, [
                    'quantity' => $block->pivot->quantity,
                    'order' => $block->pivot->order,
                    'price_programming' => $block->pivot->price_programming,
                    'price_integration' => $block->pivot->price_integration,
                    'price_field_creation' => $block->pivot->price_field_creation,
                    'price_content_management' => $block->pivot->price_content_management,
                ]);
            }
        }

        // Dupliquer les add-ons
        foreach ($estimation->addons as $addon) {
            $newEstimation->addons()->attach($addon->id);
        }

        return redirect()->route('estimations.index')->with('message', 'Estimation dupliquée avec succès.');
    }

    public function saveAsTemplate(Request $request, Estimation $estimation): RedirectResponse
    {
        $user = auth()->user();
        if ($user && $estimation->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $template = Template::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'project_type_id' => $estimation->project_type_id,
            'type' => $estimation->type,
            'currency' => $estimation->currency,
            'setup_id' => $estimation->setup_id,
            'translation_enabled' => $estimation->translation_enabled,
            'translation_type' => $estimation->translation_type,
            'translation_fixed_price' => $estimation->translation_fixed_price ?? 0,
            'translation_fixed_hours' => $estimation->translation_fixed_hours ?? 0,
            'translation_percentage' => $estimation->translation_percentage ?? 0,
            'translation_languages_count' => $estimation->translation_languages_count ?? 1,
        ]);

        foreach ($estimation->pages as $page) {
            $newPage = $template->pages()->create([
                'name' => $page->name,
                'type' => $page->type,
                'order' => $page->order,
                'quantity' => $page->quantity,
            ]);

            foreach ($page->blocks as $block) {
                $newPage->blocks()->create([
                    'block_id' => $block->id,
                    'quantity' => $block->pivot->quantity,
                    'order' => $block->pivot->order,
                    'price_programming' => $block->pivot->price_programming,
                    'price_integration' => $block->pivot->price_integration,
                    'price_field_creation' => $block->pivot->price_field_creation,
                    'price_content_management' => $block->pivot->price_content_management,
                ]);
            }
        }

        foreach ($estimation->addons as $addon) {
            $template->addons()->attach($addon->id);
        }

        return redirect()->route('estimations.builder', $estimation)
            ->with('message', 'Gabarit « '.$template->name.' » créé avec succès.');
    }

    public function exportPdf(Estimation $estimation)
    {
        $user = auth()->user();
        if ($user && $estimation->user_id !== $user->id) {
            abort(403);
        }

        $calculator = new EstimationCalculator;
        $totals = $calculator->calculateTotals($estimation);

        $html = view('estimations.pdf', compact('estimation', 'totals'))->render();

        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, "estimation-{$estimation->id}.pdf");
    }
}
