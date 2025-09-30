<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Produits</title>
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
        .stats {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
        .category-badge {
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Produits</h1>
        <p>Exporté le {{ $stats['export_date'] }}</p>
        <p>Total : {{ $stats['total_products'] }} produit(s)</p>
        @if($stats['search_term'])
            <p><strong>Recherche :</strong> "{{ $stats['search_term'] }}"</p>
        @endif
    </div>

    @if(isset($stats['price_range']))
    <div class="stats">
        <strong>Statistiques :</strong>
        Prix minimum : {{ number_format($stats['price_range']['min'], 2, ',', ' ') }} Fcfa |
        Prix maximum : {{ number_format($stats['price_range']['max'], 2, ',', ' ') }} Fcfa |
        Prix moyen : {{ number_format($stats['price_range']['avg'], 2, ',', ' ') }} Fcfa
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nom du Produit</th>
                <th class="text-right">Prix d'Achat</th>
                <th class="text-center">Catégorie</th>
                <th class="text-center">Seuil Stock</th>
                <th class="text-center">Date Création</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td style="font-family: monospace;">{{ $product->sku }}</td>
                <td>{{ $product->nom }}</td>
                <td class="text-right">{{ number_format($product->prix_achat, 2, ',', ' ') }} Fcfa</td>
                <td class="text-center">
                    @if($product->categorie)
                        <span class="category-badge">{{ ucfirst($product->categorie) }}</span>
                    @else
                        <span style="color: #9ca3af;">Non définie</span>
                    @endif
                </td>
                <td class="text-center">{{ $product->seuil_stock }}</td>
                <td class="text-center">{{ $product->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #666;">
                    Aucun produit trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion des ventes</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>
