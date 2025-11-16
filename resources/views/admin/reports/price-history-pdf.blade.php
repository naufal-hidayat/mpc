<!DOCTYPE html>
<html>
<head>
    <title>Histori Harga Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>PT MITRA PANEL CHERBOND</h2>
        <h3>HISTORI PERUBAHAN HARGA BARANG</h3>
        <p>Dicetak: {{ now()->format('d F Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="30%">Nama Barang</th>
                <th width="18%">Harga Lama</th>
                <th width="18%">Harga Baru</th>
                <th width="17%">Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
            @php
                $diff = $history->new_price - $history->old_price;
                $percent = $history->old_price > 0 ? ($diff / $history->old_price * 100) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center">{{ $history->changed_at->format('d/m/Y') }}</td>
                <td>{{ $history->item->name }}</td>
                <td class="text-right">{{ number_format($history->old_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($history->new_price, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($diff > 0)
                        ↑ {{ number_format($diff, 0, ',', '.') }} ({{ number_format($percent, 1) }}%)
                    @elseif($diff < 0)
                        ↓ {{ number_format(abs($diff), 0, ',', '.') }} ({{ number_format($percent, 1) }}%)
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
