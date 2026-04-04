<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estimation #{{ $estimation->id }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2563eb; margin: 0; }
        .client-info { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .section-title { background: #2563eb; color: white; padding: 5px 10px; font-weight: bold; margin-top: 20px; }
        .total-box { margin-top: 40px; border-top: 2px solid #2563eb; padding-top: 20px; text-align: right; }
        .grand-total { font-size: 24px; font-weight: bold; color: #059669; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1>Estimation de Projet</h1>
                    <p style="color: #666; margin-top: 5px;">Référence: #{{ $estimation->id }} | Date: {{ date('d/m/Y') }}</p>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                    <div style="font-size: 24px; font-weight: bold; color: #2563eb;">ESTIMAT</div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border: none; margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; border: none; padding: 0; vertical-align: top;">
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <strong style="color: #2563eb; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 10px; letter-spacing: 1px;">Informations Client</strong>
                    <div style="font-size: 16px; font-weight: bold;">{{ $estimation->client_name }}</div>
                    @if($estimation->project_name)
                        <div style="color: #475569; margin-top: 5px;">Projet: {{ $estimation->project_name }}</div>
                    @endif
                </div>
            </td>
            <td style="width: 50%; border: none; padding: 0 0 0 20px; vertical-align: top;">
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <strong style="color: #2563eb; display: block; margin-bottom: 5px; text-transform: uppercase; font-size: 10px; letter-spacing: 1px;">Détails Estimation</strong>
                    <div style="font-weight: bold;">Mode: {{ $estimation->type == 'hour' ? 'À l\'heure' : 'Forfait' }}</div>
                    @if($estimation->projectType)
                        <div style="color: #475569; margin-top: 5px;">Technologie: {{ $estimation->projectType->name }}</div>
                    @endif
                    @if($estimation->hourly_rate)
                        <div style="color: #475569; margin-top: 5px;">Taux horaire: {{ $estimation->hourly_rate }} {{ $estimation->currency_symbol }}/h</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Base technique & Mise en place</div>
    <table>
        <tr>
            <td>Installation et configuration ({{ $estimation->setup ? $estimation->setup->type : 'Standard' }})</td>
            <td style="text-align: right;">{{ number_format($totals['setup'], 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Développement & Intégration</div>
    <table>
        <thead>
            <tr>
                <th>Composant</th>
                <th style="text-align: right;">Programmation</th>
                <th style="text-align: right;">Intégration</th>
            </tr>
        </thead>
        <tbody>
            @php $uniqueBlocks = $estimation->pages->flatMap->blocks->unique('id'); @endphp
            @foreach($uniqueBlocks as $block)
                <tr>
                    <td>{{ $block->name }}</td>
                    <td style="text-align: right;">{{ number_format($block->price_programming, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($block->price_integration, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Détail par Page</div>
    @php
        $allPages = collect();
        if($estimation->headerPage) $allPages->push($estimation->headerPage);
        $allPages = $allPages->concat($estimation->regularPages);
        if($estimation->footerPage) $allPages->push($estimation->footerPage);
    @endphp

    @foreach($allPages as $page)
        <h4 style="margin-bottom: 5px;">
            {{ $page->name }}
            @if($page->type === 'regular' && $page->quantity > 1)
                <small style="font-weight: normal; color: #666;">(x{{ $page->quantity }} pages similaires)</small>
            @endif
        </h4>
        <table>
            <thead>
                <tr>
                    <th>Bloc</th>
                    <th>Qté</th>
                    <th style="text-align: right;">Champs</th>
                    <th style="text-align: right;">Contenu</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($page->blocks as $block)
                    @php
                        $champs = $block->price_field_creation;
                        $contenu = $block->price_content_management;
                        $blockQty = $block->pivot->quantity;
                        $pageQty = $page->quantity ?? 1;

                        // Les champs sont créés une seule fois par instance sur la page (pour le gabarit)
                        // Le contenu est multiplié par le nombre de pages similaires
                        $subtotal = ($champs * $blockQty) + ($contenu * $blockQty * $pageQty);
                    @endphp
                    <tr>
                        <td>{{ $block->name }}</td>
                        <td>{{ $blockQty }}</td>
                        <td style="text-align: right;">{{ number_format($champs * $blockQty, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($contenu * $blockQty * $pageQty, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @if($estimation->translation_enabled || $estimation->addons->count() > 0)
        <div class="section-title">Options & Services</div>
        <table>
            @if($estimation->translation_enabled)
                <tr>
                    <td>Module de Traduction ({{ $estimation->type === 'hour' ? 'Horaire' : 'Forfait' }}) - {{ $estimation->translation_languages_count }} langue(s)</td>
                    <td style="text-align: right;">{{ number_format($totals['translation'], 2) }}</td>
                </tr>
            @endif
            @foreach($totals['addon_details'] as $addonDetail)
                <tr>
                    <td>
                        {{ $addonDetail['name'] }}
                        @if($addonDetail['type'] == 'fixed_price')
                            (Forfait)
                        @elseif($addonDetail['type'] == 'fixed_hours')
                            (Heures)
                        @else
                            ({{ $addonDetail['value'] }}% sur {{ $addonDetail['calculation_base'] }})
                        @endif
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($addonDetail['calculated_value'], 2) }}
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="total-box">
        @if($estimation->type == 'hour')
            <p style="margin: 0; font-size: 14px; color: #4b5563;">Total Temps estimé: <strong style="color: #111827;">{{ number_format($totals['total_time'], 2) }} h</strong></p>
            @if($estimation->hourly_rate)
                <div style="margin-top: 10px; background: #ecfdf5; padding: 15px; border-radius: 8px; display: inline-block; border: 1px solid #d1fae5;">
                    <span style="font-size: 12px; font-weight: bold; color: #059669; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px;">Montant Total H.T.</span>
                    <span class="grand-total">{{ number_format($totals['total_price'], 2) }} {{ $estimation->currency_symbol }}</span>
                </div>
            @endif
        @else
            <div style="margin-top: 10px; background: #ecfdf5; padding: 15px; border-radius: 8px; display: inline-block; border: 1px solid #d1fae5;">
                <span style="font-size: 12px; font-weight: bold; color: #059669; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px;">Total Forfait H.T.</span>
                <span class="grand-total">{{ number_format($totals['total_price'], 2) }} {{ $estimation->currency_symbol }}</span>
            </div>
        @endif
    </div>

    @php
        $user = $estimation->user;
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;
        $isWhiteLabel = $plan ? $plan->has_white_label_pdf : false;
    @endphp

    @if(!$isWhiteLabel)
        <div style="margin-top: 50px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; color: #94a3b8; font-size: 10px;">
            Estimation générée avec <strong style="color: #2563eb;">ESTIMAT</strong> - L'outil intelligent de chiffrage de projets web.
            <br>
            <span style="font-style: italic;">Passez au plan Pro pour retirer cette mention et bénéficier de l'export en marque blanche.</span>
        </div>
    @endif
</body>
</html>
