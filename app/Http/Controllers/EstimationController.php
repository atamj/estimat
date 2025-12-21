<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use App\Models\TranslationConfig;
use App\Services\EstimationCalculator;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class EstimationController extends Controller
{
    public function index()
    {
        $estimations = Estimation::all();
        return view('estimations.index', compact('estimations'));
    }

    public function create()
    {
        $projectTypes = \App\Models\ProjectType::all();
        return view('estimations.create', compact('projectTypes'));
    }

    public function createStep2(Request $request)
    {
        $request->validate([
            'project_type_id' => 'nullable|exists:project_types,id',
            'type' => 'required|in:hour,fixed',
        ]);

        $project_type_id = $request->project_type_id;
        $type = $request->type;

        return view('estimations.create_step2', compact('project_type_id', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'type' => 'required|in:hour,fixed',
        ]);

        $projectTypeId = $request->project_type_id;
        $config = TranslationConfig::where('project_type_id', $projectTypeId)->first();

        if (!$config && $projectTypeId) {
            $config = TranslationConfig::whereNull('project_type_id')->first();
        }

        $estimation = Estimation::create([
            'client_name' => $request->client_name,
            'project_name' => $request->project_name,
            'project_type_id' => $projectTypeId,
            'type' => $request->type,
            'translation_fixed_price' => $config?->default_fixed_price ?? 0,
            'translation_fixed_hours' => $config?->default_fixed_hours ?? 0,
            'translation_percentage' => $config?->default_percentage ?? 0,
            'translation_type' => $request->type,
        ]);

        return redirect()->route('estimations.builder', $estimation);
    }

    public function builder(Estimation $estimation)
    {
        return view('estimations.builder', compact('estimation'));
    }

    public function show(Estimation $estimation)
    {
        $calculator = new EstimationCalculator();
        $totals = $calculator->calculateTotals($estimation);
        return view('estimations.show', compact('estimation', 'totals'));
    }

    public function destroy(Estimation $estimation)
    {
        $estimation->delete();
        return redirect()->route('estimations.index')->with('message', 'Estimation supprimée avec succès.');
    }

    public function duplicate(Estimation $estimation)
    {
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
                    // Note: on ignore les overrides car ils sont dépréciés selon les instructions précédentes
                ]);
            }
        }

        // Dupliquer les add-ons
        foreach ($estimation->addons as $addon) {
            $newEstimation->addons()->attach($addon->id);
        }

        return redirect()->route('estimations.index')->with('message', 'Estimation dupliquée avec succès.');
    }

    public function exportPdf(Estimation $estimation)
    {
        $calculator = new EstimationCalculator();
        $totals = $calculator->calculateTotals($estimation);

        $html = view('estimations.pdf', compact('estimation', 'totals'))->render();

        $options = new Options();
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
