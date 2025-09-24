<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: monospace, sans-serif;
            font-size: 10px;
            width: 58mm;
            margin: 0 auto;
            padding: 0;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .border-bottom {
            border-bottom: 1px dashed #000;
            margin: 4px 0;
        }

        .line {
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 2px 0;
        }

        .total {
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 4px;
        }
        
        .visa { margin-top: 10px; padding-top: 6px; }
        .visa-table { width: 100%; border-collapse: collapse; }
        .visa-table td { padding-top: 6px; }
        .visa-table td.right { text-align: right; }
    </style>
</head>
<body>
    <div class="center bold">
        ETS GLASS LE BIEN CONSTRUCTION (EGBC)
    </div>
    <div class="center">
        Agence PK19<br>
        VITRERIE - MENUSERIE - ALUMINIUM<br>
        Tél: 657 91 9 30 / 670 51 71 34
    </div>
    <div class="center border-bottom">
        FACTURE N° {{ $invoice->invoice_number ?? 'N/A' }}<br>
        Date: {{ $invoice->invoice_date ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : 'N/A' }}
    </div>

    <div class="bold">Client:</div>
    <div>{{ $invoice->client_name ?? 'N/A' }}</div>
    @if(!empty($invoice->client_location))
    <div>Adresse: {{ $invoice->client_location }}</div>
    @endif
    @if(!empty($invoice->vendor_name))
    <div class="bold" style="margin-top:4px;">Vendeur: <span class="normal">{{ $invoice->vendor_name }}</span></div>
    @endif

    <div class="border-top"></div>
    <table>
        <thead>
            <tr>
                <th>Ref.</th>
                <th>Désignation</th>
                <th>Qté</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach($invoice->products as $product)
                @php
                    $lineTotal = (float) ($product->pivot->total_price ?? 0);
                    $subtotal += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->nom }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td class="text-right">{{ number_format($lineTotal, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-top"></div>

    @php
        // Totals based on persisted invoice amount and currency
        $currency = $invoice->currency ?? 'FCFA';
        $total = (float) ($invoice->total_amount ?? $subtotal);
    @endphp

    <div class="line">
        <span>Sous-total:</span>
        <span>{{ number_format($subtotal, 0, ',', ' ') }} {{ $currency }}</span>
    </div>
    <div class="line total">
        <span>TOTAL:</span>
        <span>{{ number_format($total, 0, ',', ' ') }} {{ $currency }}</span>
    </div>

    <div class="footer">
        Les articles vendus ne sont ni repris ni échangés.<br>
        Merci pour votre confiance !
    </div>

    <div class="visa">
        <table class="visa-table">
            <tr>
                <td>VISA Vendeur</td>
                <td class="right">VISA Client</td>
            </tr>
        </table>
    </div>
</body>
</html>


