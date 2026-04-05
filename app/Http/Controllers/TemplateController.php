<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Models\Estimation;
use App\Models\Template;
use App\Models\TranslationConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $templates = Template::query()
            ->with(['projectType', 'pages'])
            ->latest()
            ->get();

        return view('templates.index', compact('templates'));
    }

    public function create(): View
    {
        $projectTypes = \App\Models\ProjectType::all();
        $currencies = Currency::cases();
        $defaultCurrency = Auth::user()?->default_currency ?? 'EUR';

        return view('templates.create', compact('projectTypes', 'currencies', 'defaultCurrency'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'type' => 'required|in:hour,fixed',
            'currency' => 'required|in:' . implode(',', array_column(Currency::cases(), 'value')),
        ]);

        $projectTypeId = $request->project_type_id;
        $config = TranslationConfig::where('project_type_id', $projectTypeId)
            ->first();

        if (! $config && $projectTypeId) {
            $config = TranslationConfig::whereNull('project_type_id')
                ->first();
        }

        $template = Template::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'project_type_id' => $projectTypeId,
            'type' => $request->type,
            'currency' => $request->currency,
            'translation_fixed_price' => $config?->default_fixed_price ?? 0,
            'translation_fixed_hours' => $config?->default_fixed_hours ?? 0,
            'translation_percentage' => $config?->default_percentage ?? 0,
            'translation_type' => $request->type,
        ]);

        $template->pages()->create(['name' => 'Site Header', 'type' => 'header', 'order' => 0, 'quantity' => 1]);
        $template->pages()->create(['name' => 'Site Footer', 'type' => 'footer', 'order' => 99, 'quantity' => 1]);

        return redirect()->route('templates.builder', $template);
    }

    public function builder(Template $template): View
    {
        return view('templates.builder', compact('template'));
    }

    public function destroy(Template $template): RedirectResponse
    {
        $template->delete();

        return redirect()->route('templates.index')->with('message', 'Gabarit supprimé avec succès.');
    }

    public function duplicate(Template $template): RedirectResponse
    {
        $newTemplate = $template->replicate();
        $newTemplate->name .= ' (Copie)';
        $newTemplate->save();

        foreach ($template->pages as $page) {
            $newPage = $page->replicate();
            $newPage->template_id = $newTemplate->id;
            $newPage->save();

            foreach ($page->blocks as $block) {
                $newPage->blocks()->create([
                    'block_id' => $block->block_id,
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
            $newTemplate->addons()->attach($addon->id);
        }

        return redirect()->route('templates.index')->with('message', 'Gabarit dupliqué avec succès.');
    }

    public function createEstimation(Template $template): RedirectResponse
    {
        $user = Auth::user();

        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;

        if ($plan && $plan->max_estimations !== -1) {
            $count = Estimation::count();
            if ($count >= $plan->max_estimations) {
                return redirect()->route('estimations.create')->with('error', 'Vous avez atteint la limite d\'estimations de votre plan.');
            }
        }

        $estimation = Estimation::create([
            'user_id' => $user->id,
            'client_name' => 'Client',
            'project_name' => $template->name,
            'project_type_id' => $template->project_type_id,
            'type' => $template->type,
            'currency' => $template->currency,
            'setup_id' => $template->setup_id,
            'translation_enabled' => $template->translation_enabled,
            'translation_type' => $template->translation_type,
            'translation_fixed_price' => $template->translation_fixed_price ?? 0,
            'translation_fixed_hours' => $template->translation_fixed_hours ?? 0,
            'translation_percentage' => $template->translation_percentage ?? 0,
            'translation_languages_count' => $template->translation_languages_count,
        ]);

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

        return redirect()->route('estimations.builder', $estimation)->with('message', 'Estimation créée depuis le gabarit « ' . $template->name . ' ».');
    }
}
