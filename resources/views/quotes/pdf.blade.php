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
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .logo {
            max-width: 100px;
            height: auto;
            margin: 0 auto 8px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .company-info {
            font-size: 9px;
            line-height: 1.5;
        }
        
        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 12px;
            font-size: 10px;
        }
        
        .info-row {
            margin-bottom: 4px;
        }
        
        .info-label {
            font-weight: bold;
        }
        
        .objet {
            margin: 12px 0;
            padding: 8px;
            background-color: #f5f5f5;
            border-left: 3px solid #333;
            font-size: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th {
            background-color: #e0e0e0;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        td {
            padding: 5px 4px;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9px;
            font-style: italic;
        }
        
        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        
        .signatures table {
            border: none;
            margin: 0;
        }
        
        .signatures td {
            border: none;
            text-align: center;
            width: 50%;
            padding: 10px;
            font-size: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">
        <div class="company-name">ETS GLASS LE BIEN CONSTRUCTION</div>
        <div class="company-info">
            <strong>VITRERIE-MENUISERIE- ALUMINIUM</strong><br>
            Vente des Vitres - aluminium -accessoires<br>
            FABRICATION ET POSE DES OUVERTURES EN ALUMINIUM ET GRILLE DE PROTECTION ROULANTE<br>
            SITUE A DOUALA (LENDI QUARTIER GENERAL)<br>
            TEL: 670 -51 -71 -34 / 690 -27 - 56 -50
        </div>
    </div>

    <div style="text-align: right; margin-bottom: 8px; font-size: 10px;">
        Douala, le {{ $quote->date_devis->format('d F Y') }}
    </div>

    <!-- Titre du document -->
    <div class="document-title">
        DEVIS ESTIMATIF MATERIEL ET MAIN D'ŒUVRE
    </div>

    <!-- Informations du devis -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">N° Devis:</span> {{ $quote->numero_devis }}
        </div>
        <div class="info-row">
            <span class="info-label">Client:</span> {{ $quote->client_nom }}
        </div>
        @if($quote->date_validite)
        <div class="info-row">
            <span class="info-label">Valide jusqu'au:</span> {{ $quote->date_validite->format('d/m/Y') }}
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
                <th style="width: 45%;">DESIGNATIONS</th>
                <th style="width: 10%;" class="text-center">QTES</th>
                <th style="width: 18%;" class="text-right">P.U</th>
                <th style="width: 22%;" class="text-right">P.T</th>
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
                <td colspan="4" style="text-align: right; font-weight: bold; background-color: #e0e0e0; font-size: 12px;">TOTAL</td>
                <td class="text-right" style="font-weight: bold; background-color: #e0e0e0; font-size: 12px;">{{ number_format($quote->total_general, 0, ',', ' ') }}</td>
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
        <table>
            <tr>
                <td>
                    <div>Mme CHRISTINE (Lendi)</div>
                    <div class="signature-line"></div>
                </td>
                <td>
                    <div>Mr JOEL</div>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
