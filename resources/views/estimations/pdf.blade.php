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
        <h1>Estimation de Projet</h1>
        <p>Référence: #{{ $estimation->id }} | Date: {{ date('d/m/Y') }}</p>
    </div>

    <div class="client-info">
        <strong>Client:</strong> {{ $estimation->client_name }}<br>
        @if($estimation->project_name)
            <strong>Projet:</strong> {{ $estimation->project_name }}<br>
        @endif
        <strong>Type:</strong> {{ $estimation->type == 'hour' ? 'À l\'heure' : 'Forfait' }}
        @if($estimation->hourly_rate)
            <br><strong>Taux horaire:</strong> {{ $estimation->hourly_rate }} €/h
        @endif
    </div>

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
    @foreach($estimation->pages as $page)
        <h4 style="margin-bottom: 5px;">{{ $page->name }}</h4>
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
                        $subtotal = ($champs + $contenu) * $block->pivot->quantity;
                    @endphp
                    <tr>
                        <td>{{ $block->name }}</td>
                        <td>{{ $block->pivot->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($champs, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($contenu, 2) }}</td>
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
            <p>Total Temps estimé: <strong>{{ number_format($totals['total_time'], 2) }} h</strong></p>
            @if($estimation->hourly_rate)
                <p class="grand-total">TOTAL PRIX: {{ number_format($totals['total_price'], 2) }} €</p>
            @endif
        @else
            <p class="grand-total">TOTAL FORFAIT: {{ number_format($totals['total_price'], 2) }} €</p>
        @endif
    </div>
</body>
</html>
