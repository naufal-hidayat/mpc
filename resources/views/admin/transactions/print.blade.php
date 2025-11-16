<!DOCTYPE html>
<html>
<head>
    <title>Nota - {{ $transaction->transaction_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .logo-section {
            display: table-cell;
            width: 120px;
            vertical-align: middle;
        }
        .logo {
            width: 100px;
            height: 100px;
            border: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }
        .company-section {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
        .company-info {
            font-size: 11px;
            margin: 5px 0 0 0;
            line-height: 1.6;
            color: #666;
        }
        .document-title {
            text-align: center;
            margin: 20px 0;
        }
        .document-title h2 {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
            padding: 5px 0;
            display: block;
            width: 100%;
            background-color: #0633b0;
            color: white;
            text-align: center;
        }
        .info-section {
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 150px;
            font-weight: bold;
        }

        /* TABEL BARANG */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            table-layout: fixed;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .items-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }

        .items-table th:nth-child(1),
        .items-table td:nth-child(1) { width: 5%; text-align: center; }

        .items-table th:nth-child(2),
        .items-table td:nth-child(2) { width: 30%; text-align: left; }

        .items-table th:nth-child(3),
        .items-table td:nth-child(3) { width: 12%; text-align: right; }

        .items-table th:nth-child(4),
        .items-table td:nth-child(4) { width: 8%; text-align: center; }

        .items-table th:nth-child(5),
        .items-table td:nth-child(5) { width: 15%; text-align: right; }

        .items-table th:nth-child(6),
        .items-table td:nth-child(6) { width: 15%; text-align: right; }

        .items-table th:nth-child(7),
        .items-table td:nth-child(7) { width: 15%; text-align: center; }

        .items-table .returned-row {
            background-color: #ffebee;
            color: #c62828;
            text-decoration: line-through;
        }
        .items-table .returned-badge {
            background-color: #c62828;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        /* BAGIAN RETUR */
        .returns-section {
            margin: 30px 0;
            padding: 15px;
            background-color: #fff3e0;
            border: 2px solid #ff9800;
            border-radius: 5px;
        }
        .returns-section h3 {
            margin: 0 0 15px 0;
            color: #e65100;
            font-size: 14px;
            text-transform: uppercase;
        }
        .returns-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        .returns-table th,
        .returns-table td {
            border: 1px solid #ff9800;
            padding: 8px;
        }
        .returns-table th {
            background-color: #ffe0b2;
            font-weight: bold;
            text-align: center;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* BAGIAN BAWAH */
        .bottom-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .qty-section {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }

        .qty-by-unit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .qty-by-unit-table th,
        .qty-by-unit-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .qty-by-unit-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .qty-by-unit-table .unit-label {
            width: 60%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .qty-by-unit-table .value-col {
            width: 80%;
            text-align: left;
        }

        .summary-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px;
            border: 1px solid #000;
        }
        .summary-table .label-col {
            width: 60%;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .summary-table .value-col {
            width: 80%;
            text-align: left;
        }
        .summary-table .total-row {
            background-color: #d0d0d0;
            font-weight: bold;
            font-size: 14px;
        }
        .summary-table .return-row {
            background-color: #ffebee;
            color: #c62828;
        }
        .summary-table .final-row {
            background-color: #c8e6c9;
            font-weight: bold;
            font-size: 14px;
        }
        .summary-table .paid-row {
            background-color: #e8f5e9;
        }
        .summary-table .remaining-row {
            background-color: #fff3e0;
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            clear: both;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-section {
            display: table;
            width: 100%;
        }
        .signature {
            display: table-cell;
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 80px;
            border-bottom: 1px solid #000;
            margin: 10px 20px;
        }
        .signature-name {
            margin-top: 5px;
            font-weight: bold;
        }
        .notes-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            clear: both;
        }
        .notes-section strong {
            display: block;
            margin-bottom: 5px;
        }
        .print-info {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .print-info { page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    @if(file_exists(public_path('images/logompc.jpeg')))
                        <img src="{{ public_path('images/logompc.jpeg') }}" alt="Logo PT" style="max-width: 100px;">
                    @else
                        LOGO
                    @endif
                </div>
            </div>
            <div class="company-section">
                <h1 class="company-name">PT MITRA PANEL CHERBOND</h1>
                <p class="company-info">
                    <strong>Alamat:</strong> Jl. Raya Kigesang Kaliwedi, Ds. Prajawinangun Wetan, Kec. Kaliwedi, Kab. Cirebon, <br>Jawa Barat 45165<br>
                    <strong>HP:</strong> 083822150448 | <strong>Email:</strong> info@mitrapanelcherbond.com | <strong>Website:</strong> www.mitrapanelcherbond.com
                </p>
            </div>
        </div>
    </div>

    <div class="document-title">
        <h2>NOTA {{ $transaction->getTypeLabel() }}</h2>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr><td class="label">Tanggal</td><td>: {{ $transaction->transaction_date->format('d F Y') }}</td></tr>
            <tr><td class="label">No. Transaksi</td><td>: <strong>{{ $transaction->transaction_code }}</strong></td></tr>
            <tr><td class="label">{{ $transaction->getPartnerLabel() }}</td><td>: {{ $transaction->partner->name }}</td></tr>
            <tr><td class="label">Alamat</td><td>: {{ $transaction->partner->address ?? '-' }}</td></tr>
            <tr><td class="label">Telp/HP</td><td>: {{ $transaction->partner->phone ?? '-' }}</td></tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; $totalAmount = 0; @endphp
            @foreach($transaction->details as $detail)
                @php
                    $isReturned = $detail->isFullyReturned();
                    $effectiveQty = $detail->getEffectiveQuantity();
                    $effectiveSubtotal = $detail->getEffectiveSubtotal();
                    if (!$isReturned) {
                        $totalQty += $effectiveQty;
                        $totalAmount += $effectiveSubtotal;
                    }
                @endphp
                <tr class="{{ $isReturned ? 'returned-row' : '' }}">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->item->name }}</td>
                    <td class="text-right">
                        @if($isReturned)
                            <s>{{ number_format($detail->quantity, 2) }}</s> → 0
                        @else
                            {{ number_format($effectiveQty, 2) }}
                            @if($detail->quantity_returned > 0)
                                <br><small>(Awal: {{ number_format($detail->quantity, 2) }})</small>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->item->unit }}</td>
                    <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($isReturned)
                            <s>{{ number_format($detail->subtotal, 0, ',', '.') }}</s> → 0
                        @else
                            {{ number_format($effectiveSubtotal, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isReturned)
                            <span class="returned-badge">RETUR PENUH</span>
                        @elseif($detail->quantity_returned > 0)
                            <span style="background-color: #ff9800; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">RETUR SEBAGIAN</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($transaction->notes)
    <div class="notes-section">
        <strong>Catatan:</strong>
        {{ $transaction->notes }}
    </div>
    @endif

    <div class="bottom-section">
        <div class="qty-section">
            @php
                $qtyByUnit = [
                    'kg' => 0,
                    'pcs' => 0
                ];

                foreach($transaction->details as $detail) {
                    $unit = $detail->item->unit;
                    $effectiveQty = $detail->getEffectiveQuantity();
                    if (isset($qtyByUnit[$unit])) {
                        $qtyByUnit[$unit] += $effectiveQty;
                    }
                }
            @endphp

            <table class="qty-by-unit-table">
                <tbody>
                    <tr>
                        <td class="unit-label">Total Qty (kg)</td>
                        <td class="text-right">{{ number_format($qtyByUnit['kg'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="unit-label">Total Qty (pcs)</td>
                        <td class="text-right">{{ number_format($qtyByUnit['pcs'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="unit-label">Total Qty (Semua)</td>
                        <td class="text-right">{{ number_format($totalQty, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="summary-section">
            <table class="summary-table">
                <tr class="total-row">
                    <td class="label-col">TOTAL AWAL</td>
                    <td class="value-col">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
                @if($transaction->hasReturns())
                <tr class="return-row">
                    <td class="label-col">RETUR</td>
                    <td class="value-col">- Rp {{ number_format($transaction->total_amount - $totalAmount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="final-row">
                    <td class="label-col">TOTAL {{ $transaction->hasReturns() ? 'AKHIR' : '' }}</td>
                    <td class="value-col">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                </tr>
                @if($transaction->deposit > 0)
                <tr>
                    <td class="label-col">DEPOSIT</td>
                    <td class="value-col">- Rp {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="paid-row">
                    <td class="label-col">PEMBAYARAN</td>
                    <td class="value-col">
                        @if($transaction->isVerified())
                            Rp {{ number_format($totalAmount - $transaction->deposit, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </td>
                </tr>
                <tr class="remaining-row">
                    <td class="label-col">SISA</td>
                    <td class="value-col">
                        @if($transaction->isVerified())
                            Rp 0
                        @else
                            Rp {{ number_format($totalAmount - $transaction->deposit, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    @if($transaction->hasReturns())
    <div class="returns-section">
        <h3>📋 DAFTAR BARANG YANG DIRETUR</h3>
        <table class="returns-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama Barang</th>
                    <th width="15%">Qty Diretur</th>
                    <th width="10%">Satuan</th>
                    <th width="35%">Alasan Retur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->returns()->where('status', 'approved')->get() as $return)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $return->transactionDetail->item->name }}</td>
                    <td class="text-right">{{ number_format($return->quantity_returned, 2) }}</td>
                    <td class="text-center">{{ $return->transactionDetail->item->unit }}</td>
                    <td>{{ $return->reason }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="clear: both;"></div>

    @if($transaction->payment_method)
    <div class="notes-section">
        <strong>Informasi Pembayaran:</strong>
        Metode: {{ $transaction->getPaymentMethodLabel() }}
        @if($transaction->paid_at)
            | Dibayar: {{ $transaction->paid_at->format('d F Y, H:i') }}
        @endif
        @if($transaction->payment_notes)
            <br>Catatan: {{ $transaction->payment_notes }}
        @endif
    </div>
    @endif

    <div class="footer">
        <div class="signature-section">
            <div class="signature">
                <p><strong>{{ $transaction->getPartnerLabel() }}</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $transaction->partner->name }}</p>
            </div>
            <div style="display: table-cell; width: 4%;"></div>
            <div class="signature">
                <p><strong>Penerima / Admin</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $transaction->creator ? $transaction->creator->name : 'Admin' }}</p>
            </div>
        </div>
    </div>

    <div class="print-info">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>
