<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root {
            --accent: #059669;
            --muted: #6b7280;
            --paper-width: 210mm;
        }

        body {
            font-family: monospace, sans-serif;
            font-size: 12px;
            max-width: var(--paper-width);
            margin: 0 auto;
            padding: 10mm;
            color: #000;
            background: white;
        }

        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px;
            }

            .invoice-container {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
                max-width: var(--paper-width);
                margin: 0 auto;
            }
        }

        @media print {
            body {
                background: white;
                padding: 10mm;
            }

            .invoice-container {
                background: white;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        /* -------- HEADER -------- */
        .invoice-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            width: 100%;
            gap: 10px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            min-width: 0;
            max-width: 60%;
        }

        .header-logo img {
            display: block;
            max-height: 40px;
            max-width: 100px;
            flex-shrink: 0;
        }

        .header-text {
            display: flex;
            flex-direction: column;
            line-height: 1.04;
            min-width: 0;
            flex: 1;
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            margin: 0;
        }

        .subtitle {
            font-size: 9px;
            margin-top: 2px;
        }

        .description {
            font-size: 7px;
            margin-top: 4px;
            line-height: 1.2;
        }

        .header-center {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            flex: 0 0 auto;
            padding: 0 10px;
            min-width: 120px;
        }

        .header-right {
            text-align: right;
            font-size: 11px;
            flex: 0 0 auto;
            white-space: nowrap;
        }

        /* -------- TABLE / CONTENT -------- */
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
            flex-wrap: wrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 11px;
        }

        th,
        td {
            text-align: left;
            padding: 3px 2px;
            vertical-align: top;
        }

        th {
            font-weight: bold;
            font-size: 11px;
        }

        td.text-right,
        .visa-table td.right,
        .line span:last-child {
            text-align: right;
        }

        .total {
            font-weight: bold;
            margin-top: 8px;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 6px;
        }

        .visa {
            margin-top: 10px;
            padding-top: 6px;
            font-size: 10px;
        }

        .visa-table {
            width: 100%;
            border-collapse: collapse;
        }

        .visa-table td {
            padding-top: 6px;
        }

        .button-container {
            text-align: center;
            margin: 15px 0;
        }

        .print-button {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            margin: 5px;
        }

        .print-button:hover {
            background: #047857;
        }

        .back-button {
            background: var(--muted);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background: #4b5563;
        }

        @media print {
            .description {
                font-size: 8px;
            }

            th,
            td {
                padding: 2px 1px;
                font-size: 10px;
            }
            
            .header-center {
                font-size: 11px;
            }
            
            .header-right {
                font-size: 10px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 5mm;
            }
            
            .invoice-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .header-left {
                max-width: 100%;
            }
            
            .header-center,
            .header-right {
                text-align: left;
            }
            
            table {
                font-size: 10px;
            }
            
            th,
            td {
                padding: 2px 1px;
            }
            
            .button-container {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .print-button,
            .back-button {
                width: 100%;
                margin: 0;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 3mm;
                font-size: 10px;
            }
            
            .company-name {
                font-size: 11px;
            }
            
            .subtitle {
                font-size: 8px;
            }
            
            .description {
                font-size: 6px;
            }
            
            table {
                font-size: 9px;
            }
            
            th,
            td {
                padding: 1px;
            }
            
            .header-center {
                font-size: 10px;
                min-width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">

        <!-- HEADER -->
        <div class="invoice-header">
            <!-- Logo + texte -->
            <div class="header-left">
                <div class="header-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo EGBC">
                </div>
                <div class="header-text">
                    <div class="company-name">ETS GLASS LE BIEN CONSTRUCTION (EGBC)</div>
                    <div class="subtitle">VITRERIE - MENUISERIE - ALUMINIUM</div>
                    <div class="description">
                        FABRICATION ET POSE DES OUVERTURES EN ALUMINIUM ET GRILLE DE PROTECTION ROULANTE.<br>
                        SITUÉ A DOUALA PK 19 ( ENTRE L'ENTRÉE DÉCHARGE ET ENTRÉ MAADI )<br>
                        Tél: 657 91 91 30 / 670 51 71 34
                    </div>
                </div>
            </div>

            <!-- Facture N° -->
            <div class="header-center">
                FACTURE N° {{ $invoice->invoice_number ?? 'N/A' }}
            </div>

            <!-- Date -->
            <div class="header-right">
                DATE<br>
                {{ $invoice->invoice_date ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : 'N/A' }}
            </div>
        </div>

        <!-- CLIENT / VENDEUR -->
        <div style="margin-top:8px; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 5px;">
            <div class="bold">
                Client: {{ $invoice->client_name ?? 'N/A' }}
                @if (!empty($invoice->client_phone))
                    <span style="margin-left: 10px;">Tél: {{ $invoice->client_phone }}</span>
                @endif
            </div>
            @if (!empty($invoice->vendor_name))
                <div class="bold">Vendeur: <span style="font-weight: normal;">{{ $invoice->vendor_name }}</span></div>
            @endif
        </div>

        <div class="border-top"></div>

        <!-- TABLEAU DES PRODUITS -->
        <table>
            <thead>
                <tr>
                    <th style="width:10%;">Ref.</th>
                    <th style="width:54%;">Désignation</th>
                    <th style="width:12%;">Prix unitaire</th>
                    <th style="width:8%;">Qté</th>
                    <th style="width:16%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach ($invoice->products as $product)
                    @php
                        $lineTotal = (float) ($product->pivot->total_price ?? 0);
                        $subtotal += $lineTotal;
                    @endphp
                    <tr>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->nom }}</td>
                        <td>{{ $product->prix_vente }}</td>
                        <td>{{ rtrim(rtrim(number_format($product->pivot->quantity, 3, ',', ''), '0'), ',') }}</td>
                        <td>{{ number_format($lineTotal, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-top"></div>

        @php
            $currency = $invoice->currency ?? 'FCFA';
            $total = (float) ($invoice->total_amount ?? $subtotal);
        @endphp

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

        <div class="button-container no-print">
            <button class="print-button" onclick="window.print()">🖨️ Imprimer la facture</button>
            <a href="{{ route('invoices.index') }}" class="back-button">← Retour à la liste</a>
        </div>
    </div>
</body>
</html>