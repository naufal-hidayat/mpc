<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Harga Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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

        /* INFO SECTION */
        .info {
            margin-bottom: 20px;
            font-size: 11px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
        }
        table td {
            padding: 8px;
            border: 1px solid #000;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-kg {
            background-color: #17a2b8;
            color: white;
        }
        .badge-pcs {
            background-color: #ffc107;
            color: #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .highlight {
            background-color: #fff3cd !important;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body { margin: 0; padding: 15px; }
            .footer { page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <!-- HEADER SAMA DENGAN NOTA -->
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
                    <strong>Alamat:</strong> Jl. Raya Kigesang Kaliwedi, Ds. Prajawinangun Wetan, Kec. Kaliwedi, Kab. Cirebon, <br>Jawa Barat 45165
                    <strong>HP:</strong> 083822150448 <br> <strong>Email:</strong> info@mitrapanelcherbond.com | <strong>Website:</strong> www.mitrapanelcherbond.com
                </p>
            </div>
        </div>
    </div>

    <!-- DOCUMENT TITLE -->
    <div class="document-title">
        <h2>DAFTAR HARGA BARANG</h2>
    </div>

    <!-- INFO -->
    <div class="info">
        <strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i') }} WIB<br>
        <strong>Total Barang:</strong> {{ $items->count() }} item<br>
        <strong>Keterangan:</strong> Daftar harga terbaru untuk semua barang
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 10%;" class="text-center">Satuan</th>
                <th style="width: 25%;" class="text-right">Harga</th>
                <th style="width: 25%;" class="text-center">Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr @if($item->price_updated_at && $item->price_updated_at->isToday()) class="highlight" @endif>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $item->name }}</strong></td>
                <td class="text-center">
                    <span class="badge badge-{{ strtolower($item->unit) }}">
                        {{ strtoupper($item->unit) }}
                    </span>
                </td>
                <td class="text-right">
                    <strong>Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                </td>
                <td class="text-center">
                    {{ $item->price_updated_at ? $item->price_updated_at->format('d/m/Y') : '-' }}
                    @if($item->price_updated_at && $item->price_updated_at->isToday())
                        <br><span style="color: #28a745; font-weight: bold; font-size: 9px;">(Update Hari Ini)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>Catatan:</strong></p>
        <p>- Harga sewaktu-waktu dapat berubah tanpa pemberitahuan terlebih dahulu</p>
        <p>- Untuk informasi lebih lanjut, hubungi kami di nomor telepon yang tertera</p>
        <p>- Dokumen ini dicetak secara otomatis dari sistem</p>
        <br>
        <p style="margin-top: 20px;">© {{ date('Y') }} - PT MITRA PANEL CHERBOND</p>
    </div>
</body>
</html>
