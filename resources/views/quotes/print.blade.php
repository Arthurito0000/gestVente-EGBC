<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $quote->numero_devis }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .logo {
            max-width: 120px;
            height: auto;
            margin: 0 auto 10px;
            display: block;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 10px;
            line-height: 1.6;
        }
        
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .objet {
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 3px solid #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th {
            background-color: #e0e0e0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 11px;
        }
        
        td {
            padding: 6px 8px;
            border: 1px solid #000;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row.final {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #000;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 80px;
            clear: both;
            text-align: center;
            font-size: 10px;
            font-style: italic;
        }
        
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Bouton d'impression -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Imprimer
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Fermer
        </button>
    </div>

    <!-- En-tête -->
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <div class="company-name">ETS GLASS LE BIEN</div>
        <div class="company-info">
            <strong>VITRERIE-MENUISERIE- ALUMINIUM</strong><br>
            Vente des Vitres - aluminium -accessoires<br>
            FABRICATION ET POSE DES OUVERTURES EN ALUMINIUM ET GRILLE DE PROTECTION ROULANTE<br>
            SITUE A DOUALA (LENDI QUARTIER GENERAL)<br>
            TEL: 670 -51 -71 -34 / 690 -27 - 56 -50
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 10px;">
        Douala, le {{ $quote->date_devis->format('d F Y') }}
    </div>

    <!-- Titre du document -->
    <div class="document-title">
        DEVIS ESTIMATIF MATERIEL ET MAIN D'ŒUVRE
    </div>

    <!-- Informations du devis -->
    <div class="info-section">
        <div class="info-row">
            <div><span class="info-label">N° Devis:</span> {{ $quote->numero_devis }}</div>
            <div><span class="info-label">Client:</span> {{ $quote->client_nom }}</div>
        </div>
        @if($quote->date_validite)
        <div class="info-row">
            <div><span class="info-label">Valide jusqu'au:</span> {{ $quote->date_validite->format('d/m/Y') }}</div>
        </div>
        @endif
    </div>

    <!-- Objet -->
    @if($quote->objet)
    <div class="objet">
        <strong>Objet :</strong> {{ $quote->objet }}
    </div>
    @endif

    <!-- Tableau des articles -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">N°</th>
                <th style="width: 40%;">DESIGNATIONS</th>
                <th style="width: 10%;" class="text-center">QTES</th>
                <th style="width: 15%;" class="text-right">P.U</th>
                <th style="width: 15%;" class="text-right">P.T</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items->where('type', 'materiel') as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->designation }}</td>
                <td class="text-center">{{ $item->quantite }}</td>
                <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($item->prix_total, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f0f0f0;">TOTAL MATERIEL</td>
                <td class="text-right" style="font-weight: bold; background-color: #f0f0f0;">{{ number_format($quote->total_materiel, 0, ',', ' ') }}</td>
            </tr>
            @if($quote->items->where('type', 'main_oeuvre')->count() > 0)
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f0f0f0;">MAIN D'ŒUVRE</td>
                <td class="text-right" style="font-weight: bold; background-color: #f0f0f0;">{{ number_format($quote->total_main_oeuvre, 0, ',', ' ') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold; background-color: #e0e0e0; font-size: 13px;">TOTAL</td>
                <td class="text-right" style="font-weight: bold; background-color: #e0e0e0; font-size: 13px;">{{ number_format($quote->total_general, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Note de bas de page -->
    <div class="footer">
        Arrêté le présent Devis à la somme de {{ number_format($quote->total_general, 0, ',', ' ') }} FCFA 
        ({{ ucfirst(\NumberFormatter::create('fr', \NumberFormatter::SPELLOUT)->format($quote->total_general)) }} francs CFA)
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-box">
            <div>Mme CHRISTINE (Lendi)</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <div>Mr JOEL</div>
            <div class="signature-line"></div>
        </div>
    </div>
</body>
</html>
