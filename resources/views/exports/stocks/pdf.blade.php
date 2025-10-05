<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>État des Stocks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
       
        .header h1 {
            color: #3b82f6;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #374151;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
        /* Statuts de stock */
        .status-normal {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .category-badge {
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .summary-section {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Logo de l'entreprise -->
        <h1>État des Stocks</h1>
        <p>Exporté le {{ $stats['export_date'] }}</p>
        <p>Total : {{ $stats['total_stocks'] }} produit(s) en stock</p>
        @if($stats['search_term'])
            <p><strong>Recherche :</strong> "{{ $stats['search_term'] }}"</p>
        @endif
    </div>


    <!-- Résumé par statut -->
    @if($stats['ruptures_stock'] > 0)
    <div class="summary-section">
        <div class="summary-title">⚠️ Produits en Rupture de Stock ({{ $stats['ruptures_stock'] }})</div>
        @foreach($stocks_by_status['rupture'] as $stock)
            <span style="display: inline-block; margin: 2px 5px; padding: 2px 6px; background-color: #fee2e2; color: #991b1b; border-radius: 3px; font-size: 8px;">
                {{ $stock->product->sku }} - {{ $stock->product->nom }}
            </span>
        @endforeach
    </div>
    @endif

    @if($stats['stocks_faibles'] > 0)
    <div class="summary-section">
        <div class="summary-title">⚡ Produits en Stock Faible ({{ $stats['stocks_faibles'] }})</div>
        @foreach($stocks_by_status['faible'] as $stock)
            <span style="display: inline-block; margin: 2px 5px; padding: 2px 6px; background-color: #fef3c7; color: #92400e; border-radius: 3px; font-size: 8px;">
                {{ $stock->product->sku }} ({{ $stock->quantite }}/{{ $stock->seuil }})
            </span>
        @endforeach
    </div>
    @endif

    <!-- Tableau détaillé -->
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nom du Produit</th>
                <th class="text-center">Catégorie</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Seuil</th>
                <th class="text-center">Statut</th>
                <th class="text-right">Valeur Stock</th>
                <th class="text-center">MAJ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            <tr>
                <td style="font-family: monospace;">{{ $stock->product->sku }}</td>
                <td>{{ $stock->product->nom }}</td>
                <td class="text-center">
                    @if($stock->product->categorie)
                        <span class="category-badge">{{ ucfirst($stock->product->categorie) }}</span>
                    @else
                        <span style="color: #9ca3af;">Non définie</span>
                    @endif
                </td>
                <td class="text-right">{{ $stock->quantite }}</td>
                <td class="text-right">{{ $stock->seuil }}</td>
                <td class="text-center">
                    @if($stock->quantite == 0)
                        <span class="status-danger">Rupture</span>
                    @elseif($stock->quantite <= $stock->seuil)
                        <span class="status-warning">Stock faible</span>
                    @else
                        <span class="status-normal">Normal</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($stock->quantite * $stock->product->prix_achat, 0, ',', ' ') }} Fcfa</td>
                <td class="text-center">{{ $stock->updated_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #666;">
                    Aucun stock trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p><strong>ETS GLASS LE BIEN CONSTRUCTION (EGBC)</strong></p>
        <p>Agence PK19 - Vente des Vitres - aluminium -accessoires</p>
        <p>Tél: 657 91 9 30 / 670 51 71 34</p>
        <p>Document généré automatiquement le {{ date('d/m/Y à H:i') }}</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>
