<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #555;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2c3e50;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        .invoice-info table {
            width: 100%;
        }

        .invoice-info td {
            padding: 5px;
        }

        .products table {
            width: 100%;
            border-collapse: collapse;
        }

        .products th,
        .products td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .products th {
            background: #f8f8f8;
            font-weight: bold;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .total strong {
            font-size: 16px;
            color: #2c3e50;
        }

        footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Facture</h1>
        <p>{{ $invoice->code }}</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td>
                    <strong>Vendeur:</strong> {{ $invoice->vendor_name }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}<br>
                </td>
                <td style="text-align: right;">
                    <strong>Client:</strong> {{ $invoice->client_name }}<br>
                    <strong>Localisation:</strong> {{ $invoice->client_location }}
                </td>
            </tr>
        </table>
    </div>

    <div class="products">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire ({{ $invoice->currency }})</th>
                    <th>Total ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->nom }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>{{ number_format($product->pivot->unit_price, 2, ',', ' ') }}</td>
                        <td>{{ number_format($product->pivot->total_price, 2, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        <p><strong>Total général: {{ number_format($invoice->total_amount, 2, ',', ' ') }} {{ $invoice->currency }}</strong></p>
    </div>

    <footer>
        Généré le {{ now()->format('d/m/Y H:i') }} — {{ config('app.name') }}
    </footer>

</body>

</html>
