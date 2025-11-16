<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Per Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        /* HEADER - SAMA DENGAN NOTA */
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

        /* DOCUMENT TITLE */
        .document-title {
            text-align: center;
            margin: 20px 0;
        }
        .document-title h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
            padding: 8px 0;
            display: block;
            width: 100%;
            background-color: #0633b0;
            color: white;
            text-align: center;
        }

        /* INFO CETAK */
        .print-date {
            text-align: center;
            margin: 10px 0 20px 0;
            font-size: 10px;
            color: #666;
        }

        /* STATS BOX */
        .stats {
            margin: 20px 0;
            border: 2px solid #0633b0;
            padding: 15px;
            background-color: #f0f7ff;
            border-radius: 5px;
        }
        .stats h3 {
            margin: 0 0 10px 0;
            color: #0633b0;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 2px solid #0633b0;
            padding-bottom: 5px;
        }
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        .stats-table td {
            padding: 5px 10px;
            border: none;
        }
        .stats-table .label {
            font-weight: bold;
            color: #333;
            width: 25%;
        }
        .stats-table .value {
            width: 25%;
        }
        .stats-table .highlight {
            background-color: #fff;
            font-weight: bold;
            font-size: 12px;
            color: #0633b0;
            padding: 8px 10px;
            border-radius: 3px;
        }

        /* TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table.data-table tfoot {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        table.data-table tfoot td {
            background-color: #e0e0e0;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* TYPE BADGE */
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .type-purchase {
            background-color: #28a745;
            color: white;
        }
        .type-sale {
            background-color: #007bff;
            color: white;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .signature {
            display: table-cell;
            width: 48%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
            border-bottom: 1px solid #000;
            margin: 10px 40px;
        }
        .signature-name {
            margin-top: 5px;
            font-weight: bold;
            font-size: 11px;
        }
        .print-info {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
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
    <!-- HEADER - SAMA DENGAN NOTA -->
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

    <!-- DOCUMENT TITLE -->
    <div class="document-title">
        <h2>Laporan Transaksi Per Supplier</h2>
    </div>

    <p class="print-date"><strong>Dicetak:</strong> {{ now()->format('d F Y, H:i') }} WIB</p>

    <!-- STATISTICS BOX -->
    <div class="stats">
        <h3>📊 Ringkasan Laporan</h3>
        <table class="stats-table">
            <tr>
                <td class="label">Total Transaksi:</td>
                <td class="value">{{ $stats['total_transactions'] }} transaksi</td>
                <td class="label">Total Pembelian:</td>
                <td class="value highlight">Rp {{ number_format($stats['total_purchase'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Keseluruhan:</td>
                <td class="value highlight">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</td>
                <td class="label">Total Penjualan:</td>
                <td class="value highlight">Rp {{ number_format($stats['total_sale'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- DATA TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Transaksi</th>
                <th width="10%">Tanggal</th>
                <th width="10%">Tipe</th>
                <th width="20%">Supplier</th>
                <th width="13%">Total</th>
                <th width="12%">Deposit</th>
                <th width="15%">Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td><strong>{{ $transaction->transaction_code }}</strong></td>
                <td class="text-center">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                <td class="text-center">
                    <span class="type-badge type-{{ $transaction->type }}">
                        {{ $transaction->getTypeLabel() }}
                    </span>
                </td>
                <td>{{ $transaction->supplier->name }}</td>
                <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #999;">
                    Tidak ada data transaksi
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>GRAND TOTAL:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transactions->sum('deposit'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transactions->sum('final_amount'), 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <div style="margin-bottom: 20px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <strong>Catatan:</strong><br>
            <small>
                - Laporan ini mencakup semua transaksi per supplier<br>
                - Total Akhir = Total - Deposit<br>
                - Untuk informasi lebih lanjut, hubungi bagian administrasi
            </small>
        </div>

        <div class="signature-section">
            <div class="signature">
                <p><strong>Dibuat Oleh</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">Admin</p>
            </div>
            <div style="display: table-cell; width: 4%;"></div>
            <div class="signature">
                <p><strong>Disetujui Oleh</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">Manajer</p>
            </div>
        </div>
    </div>

    <div class="print-info">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }} WIB | Sistem Manajemen Inventory PT Mitra Panel Cherbond</p>
        <p>© {{ date('Y') }} - PT MITRA PANEL CHERBOND</p>
    </div>
</body>
</html>
