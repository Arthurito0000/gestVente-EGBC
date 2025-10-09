<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
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
            font-size: 18px;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-info td {
            padding: 5px;
        }

        .products table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .products th,
        .products td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            word-wrap: break-word;
        }

        .products th {
            background: #f8f8f8;
            font-weight: bold;
            font-size: 11px;
        }

        .products td {
            font-size: 10px;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .total strong {
            font-size: 14px;
            color: #2c3e50;
        }

        footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        
        @media print {
            body {
                font-size: 10px;
                padding: 10px;
            }
            
            .header h1 {
                font-size: 16px;
            }
            
            .products th {
                font-size: 9px;
                padding: 4px;
            }
            
            .products td {
                font-size: 8px;
                padding: 3px;
            }
            
            .total strong {
                font-size: 12px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                font-size: 10px;
                padding: 10px;
            }
            
            .header h1 {
                font-size: 16px;
            }
            
            .products th,
            .products td {
                padding: 4px;
                font-size: 9px;
            }
            
            .invoice-info td {
                padding: 3px;
                font-size: 9px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                font-size: 9px;
                padding: 5px;
            }
            
            .header h1 {
                font-size: 14px;
            }
            
            .products th,
            .products td {
                padding: 3px;
                font-size: 8px;
            }
            
            .invoice-info td {
                padding: 2px;
                font-size: 8px;
            }
            
            .total strong {
                font-size: 11px;
            }
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
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Produit</th>
                    <th style="width: 15%;">Quantité</th>
                    <th style="width: 20%;">Prix Unitaire ({{ $invoice->currency }})</th>
                    <th style="width: 25%;">Total ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left;">{{ $product->nom }}</td>
                        <td>{{ rtrim(rtrim(number_format($product->pivot->quantity, 3, ',', ''), '0'), ',') }}</td>
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