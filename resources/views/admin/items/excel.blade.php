<table>
    <!-- HEADER PERUSAHAAN -->
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size: 16px;">
            PT MITRA PANEL CHERBOND
        </td>
    </tr>
    <tr>
        <td colspan="5" style="text-align: center; font-size: 10px;">
            Jl. Raya Kigesang Kaliwedi, Ds. Prajawinangun Wetan, Kec. Kaliwedi, Kab. Cirebon, Jawa Barat 45165
        </td>
    </tr>
    <tr>
        <td colspan="5" style="text-align: center; font-size: 10px;">
            HP: 083822150448 | Email: info@mitrapanelcherbond.com | Website: www.mitrapanelcherbond.com
        </td>
    </tr>

    <!-- BORDER LINE -->
    <tr>
        <td colspan="5"></td>
    </tr>

    <!-- EMPTY ROW -->
    <tr>
        <td colspan="5"></td>
    </tr>

    <!-- DOCUMENT TITLE -->
    <tr>
        <td colspan="5" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #0633b0; color: white;">
            DAFTAR HARGA BARANG
        </td>
    </tr>

    <!-- EMPTY ROW -->
    <tr>
        <td colspan="5"></td>
    </tr>

    <!-- INFO SECTION -->
    <tr>
        <td colspan="5" style="font-size: 10px;">
            <strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i') }} WIB
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 10px;">
            <strong>Total Barang:</strong> {{ $items->count() }} item
        </td>
    </tr>
    <tr>
        <td colspan="5" style="font-size: 10px;">
            <strong>Keterangan:</strong> Daftar harga terbaru untuk semua barang
        </td>
    </tr>

    <!-- EMPTY ROW -->
    <tr>
        <td colspan="5"></td>
    </tr>

    <!-- TABLE HEADER -->
    <tr>
        <th style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center;">No</th>
        <th style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center;">Nama Barang</th>
        <th style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center;">Satuan</th>
        <th style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center;">Harga</th>
        <th style="background-color: #4CAF50; color: white; font-weight: bold; text-align: center;">Terakhir Update</th>
    </tr>

    <!-- TABLE BODY -->
    @foreach($items as $index => $item)
    <tr>
        <td style="text-align: center;">{{ $index + 1 }}</td>
        <td style="font-weight: bold;">{{ $item->name }}</td>
        <td style="text-align: center; font-weight: bold;">{{ strtoupper($item->unit) }}</td>
        <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
        <td style="text-align: center;">
            {{ $item->price_updated_at ? $item->price_updated_at->format('d/m/Y') : '-' }}
            @if($item->price_updated_at && $item->price_updated_at->isToday())
                (Update Hari Ini)
            @endif
        </td>
    </tr>
    @endforeach
</table>
