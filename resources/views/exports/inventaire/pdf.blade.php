<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche d'Inventaire - {{ $date_inventaire }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
            margin: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding: 10px 15px 15px 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .header .info {
            font-size: 10px;
            color: #374151;
        }
        
         
        .stats {
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        
        .stats-row {
            width: 100%;
            display: flex !important;
            flex-direction: row !important; 
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
            min-width: 0;
        }
        
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
            line-height: 1.2;
        }
        
        
        .instructions {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .instructions h3 {
            font-size: 11px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        
        .instructions p {
            font-size: 9px;
            color: #78350f;
            line-height: 1.3;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        th {
            background-color: #1e40af;
            color: white;
            padding: 10px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #1e40af;
        }
        
        td {
            padding: 8px 6px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .sku {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 8px;
        }
        
        .product-name {
            text-align: left;
            font-weight: 500;
            max-width: 120px;
            word-wrap: break-word;
            font-size: 8px;
        }
        
        .stock-system {
            font-weight: bold;
            color: #1e40af;
        }
        
        .input-field {
            width: 100%;
            height: 18px;
            border: 1px solid #9ca3af;
            background-color: white;
        }
        
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding: 8px 15px 5px 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Colonnes spécifiques pour format portrait */
        .col-sku { width: 12%; }
        .col-nom { width: 20%; }
        .col-prix { width: 10%; }
        .col-stock { width: 8%; }
        .col-physique { width: 12%; }
        .col-ecart { width: 12%; }
        .col-observation { width: 26%; }
        
        @media print {
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>FICHE D'INVENTAIRE</h1>
        <div class="subtitle">Contrôle physique des stocks</div>
        <div class="info">
            <strong>Date d'inventaire :</strong> {{ $date_inventaire }} | 
            <strong>Généré le :</strong> {{ $stats['date_generation'] }} | 
            <strong>Filtre :</strong> {{ $stats['recherche'] }}
        </div>
    </div>

    <!-- Statistiques
    <div class="stats">
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['total_produits'] }}</div>
                <div class="stat-label">Produits à inventorier</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['total_quantite'], 0, ',', ' ') }}</div>
                <div class="stat-label">Quantité système totale</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">_____</div>
                <div class="stat-label">Quantité physique totale</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">_____</div>
                <div class="stat-label">Écart total</div>
            </div>
        </div>
    </div> -->

    <!-- Instructions -->
    <div class="instructions">
        <h3>INSTRUCTIONS POUR L'INVENTAIRE :</h3>
        <p>
            1. <strong>Stock physique :</strong> Comptez et notez la quantité réellement présente en stock<br>
            2. <strong>Écart :</strong> Calculez la différence (Stock physique - Stock système)<br>
            3. <strong>Observation :</strong> Notez toute anomalie, produit endommagé, ou remarque particulière<br>
            4. <strong>Signature :</strong> L'inventaire doit être signé par le responsable à la fin
        </p>
    </div>

    <!-- Tableau d'inventaire -->
    <table>
        <thead>
            <tr>
                <th class="col-sku">SKU / Code</th>
                <th class="col-nom">Nom du Produit</th>
                <th class="col-prix">Prix Vente</th>
                <th class="col-stock">Stock Système</th>
                <th class="col-physique">Stock Physique</th>
                <th class="col-ecart">Écart</th>
                <th class="col-observation">Observations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $index => $stock)
                @if($index > 0 && $index % 20 == 0)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table>
                        <thead>
                            <tr>
                                <th class="col-sku">SKU / Code</th>
                                <th class="col-nom">Nom du Produit</th>
                                <th class="col-prix">Prix Vente</th>
                                <th class="col-stock">Stock Système</th>
                                <th class="col-physique">Stock Physique</th>
                                <th class="col-ecart">Écart</th>
                                <th class="col-observation">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                <tr>
                    <td class="sku">{{ $stock->product->sku }}</td>
                    <td class="product-name">{{ $stock->product->nom }}</td>
                    <td style="text-align: right; font-weight: 500;">{{ number_format($stock->product->prix_vente, 0, ',', ' ') }}</td>
                    <td class="stock-system">{{ $stock->quantite }}</td>
                    <td><div class="input-field"></div></td>
                    <td><div class="input-field"></div></td>
                    <td><div class="input-field"></div></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #6b7280;">
                        Aucun produit trouvé pour cet inventaire
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Zone de signature -->
    <div style="margin-top: 30px; border-top: 1px solid #d1d5db; padding: 20px 15px 15px 15px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; vertical-align: top; padding-right: 15px;">
                    <p style="font-weight: bold; margin-bottom: 10px;">Inventaire réalisé par :</p>
                    <p>Nom : ________________________</p>
                    <p>Date : ________________________</p>
                    <p>Signature : ________________________</p>
                </td>
                <td style="width: 50%; border: none; vertical-align: top; padding-left: 15px;">
                    <p style="font-weight: bold; margin-bottom: 10px;">Contrôlé par :</p>
                    <p>Nom : ________________________</p>
                    <p>Date : ________________________</p>
                    <p>Signature : ________________________</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Fiche d'inventaire générée automatiquement - {{ $stats['date_generation'] }} - Page <span class="pagenum"></span>
    </div>
</body>
</html>
