<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        /* 🖨️ Style pour imprimante matricielle Epson LQ-350 */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            color: #000;
            background: white;
            line-height: 1.3;
        }
        
        @media screen {
            body {
                background: #f5f5f5;
                padding: 40px;
            }
            
            .invoice-container {
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-width: 210mm;
                margin: 0 auto;
            }
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                font-size: 9pt;
                max-width: 72mm;
            }
            
            .invoice-container {
                background: white;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
                max-width: 72mm;
            }
            
            .no-print { display: none !important; }
            
            /* Forcer police monospace pour matricielle */
            * {
                font-family: 'Courier New', Courier, monospace !important;
            }
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

        th,
        td {
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

        .visa {
            margin-top: 10px;
            padding-top: 6px;
        }

        .visa-table {
            width: 100%;
            border-collapse: collapse;
        }

        .visa-table td {
            padding-top: 6px;
        }

        .visa-table td.right {
            text-align: right;
        }
        
        .print-button {
            background: #059669;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin: 10px 5px;
            transition: background 0.3s;
        }
        
        .print-button:hover {
            background: #047857;
        }
        
        .back-button {
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .back-button:hover {
            background: #4b5563;
        }
        
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .header-section {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }
        
        .header-logo {
            flex-shrink: 0;
        }
        
        .header-logo img {
            max-height: 40px;
            max-width: 80px;
            height: auto;
            width: auto;
        }
        
        .header-text {
            flex-grow: 1;
            text-align: left;
        }
        
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Logo à gauche de l'en-tête -->
        <div class="header-section">
            <div class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo EGBC">
            </div>
            <div class="header-text">
                <div class="bold">ETS GLASS LE BIEN CONSTRUCTION (EGBC)</div>
                <div style="font-size: 9px;">VITRERIE-MENUISERIE- ALUMINIUM</div>
                <div style="font-size: 9px;">FABRICATION ET POSE DES OUVERTURES EN ALUMINIUM ET GRILLE DE PROTECTION ROULANTE <br>
                                                                                                                SITUE A DOUALA (LENDI QUARTIER GENERAL )
                                                                                                                </div>
                <div style="font-size: 9px;">Tél: 657 91 9 30 / 670 51 71 34</div>
            </div>
        </div>
    <div class="center border-bottom">
        FACTURE N° {{ $invoice->invoice_number ?? 'N/A' }}<br>
        {{ $invoice->invoice_date ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : 'N/A' }}
    </div>

    <div class="bold">Client: {{ $invoice->client_name ?? 'N/A' }}</div>
    @if (!empty($invoice->client_phone))
        <div>Tél: {{ $invoice->client_phone }}</div>
    @endif
    @if (!empty($invoice->vendor_name))
        <div class="bold" style="margin-top:4px;">Vendeur: <span class="normal">{{ $invoice->vendor_name }}</span></div>
    @endif

    <div class="border-top"></div>
    <table>
        <thead>
            <tr>
                <th>Ref.</th>
                <th>Désignation</th>
                <th>prix unitaire</th>
                <th>Qté</th>
                <th class="text-right">Total</th>
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
                    <td>{{ $product->prix_vente}}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td class="text-right">{{ number_format($lineTotal, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-top"></div>

    @php
        // Total basé sur le montant persisté de la facture
        $currency = $invoice->currency ?? 'FCFA';
        $total = (float) ($invoice->total_amount ?? $subtotal);
    @endphp

    <div class="line total">
        <span>TOTAL:</span>
        <span>{{ number_format($total, 0, ',', ' ') }} {{ $currency }}</span>
    </div>

    <div class="footer">
        Les articles vendus ne sont ni repris ni échangés.<br>
        Merci pour votre confiance !<br><br>
       
    </div>

    <div class="visa">
        <table class="visa-table">
            <tr>
                <td>VISA Vendeur</td>
                <td class="right">VISA Client</td>
            </tr>
        </table>
    </div>
    
        <!-- Boutons d'action (masqués à l'impression) -->
        <div class="button-container no-print">
            <button class="print-button" onclick="window.print()">
                🖨️ Imprimer la facture
            </button>
            <a href="{{ route('invoices.index') }}" class="back-button">
                ← Retour à la liste
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Optionnel: ouvrir automatiquement la boîte d'impression
            // window.print();
        });
    </script>
</body>

</html>
