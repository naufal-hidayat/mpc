<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ItemsExport implements FromView, ShouldAutoSize, WithEvents
{
    public function view(): View
    {
        return view('admin.items.excel', [
            'items' => Item::latest()->get()
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get highest row and column
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // === HEADER PERUSAHAAN (Row 1-5) ===

                // Merge cells untuk header perusahaan
                $sheet->mergeCells('A1:E1'); // Nama Perusahaan
                $sheet->mergeCells('A2:E2'); // Alamat
                $sheet->mergeCells('A3:E3'); // Kontak

                // Style untuk nama perusahaan
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '333333']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Style untuk alamat dan kontak
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font' => ['size' => 10],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Border bawah header
                $sheet->getStyle('A4:E4')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // === DOCUMENT TITLE (Row 6) ===
                $sheet->mergeCells('A6:E6');
                $sheet->getStyle('A6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0633b0']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(6)->setRowHeight(25);

                // === INFO SECTION (Row 8-10) ===
                $sheet->getStyle('A8:A10')->applyFromArray([
                    'font' => ['size' => 10]
                ]);

                // === TABLE HEADER (Row 12) ===
                $sheet->getStyle('A12:E12')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4CAF50']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(12)->setRowHeight(20);

                // === TABLE BODY (Row 13 onwards) ===
                if ($highestRow > 12) {
                    // Border untuk semua cell data
                    $sheet->getStyle('A13:E' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);

                    // Alignment untuk kolom-kolom
                    // Kolom No (A) - Center
                    $sheet->getStyle('A13:A' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);

                    // Kolom Nama Barang (B) - Left
                    $sheet->getStyle('B13:B' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'font' => ['bold' => true]
                    ]);

                    // Kolom Satuan (C) - Center
                    $sheet->getStyle('C13:C' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'font' => ['bold' => true]
                    ]);

                    // Kolom Harga (D) - Right
                    $sheet->getStyle('D13:D' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'font' => ['bold' => true]
                    ]);

                    // Kolom Terakhir Update (E) - Center
                    $sheet->getStyle('E13:E' . $highestRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);

                    // Zebra striping (alternate row colors)
                    for ($row = 13; $row <= $highestRow; $row++) {
                        if (($row - 13) % 2 == 0) {
                            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F9F9F9']
                                ]
                            ]);
                        }
                    }
                }

                // === COLUMN WIDTHS ===
                $sheet->getColumnDimension('A')->setWidth(8);   // No
                $sheet->getColumnDimension('B')->setWidth(35);  // Nama Barang
                $sheet->getColumnDimension('C')->setWidth(12);  // Satuan
                $sheet->getColumnDimension('D')->setWidth(20);  // Harga
                $sheet->getColumnDimension('E')->setWidth(20);  // Terakhir Update

                // === FOOTER (After last data row) ===
                $footerRow = $highestRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Catatan:');
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10]
                ]);

                $noteRow1 = $footerRow + 1;
                $sheet->mergeCells('A' . $noteRow1 . ':E' . $noteRow1);
                $sheet->setCellValue('A' . $noteRow1, '- Harga sewaktu-waktu dapat berubah tanpa pemberitahuan terlebih dahulu');

                $noteRow2 = $footerRow + 2;
                $sheet->mergeCells('A' . $noteRow2 . ':E' . $noteRow2);
                $sheet->setCellValue('A' . $noteRow2, '- Untuk informasi lebih lanjut, hubungi kami di nomor telepon yang tertera');

                $noteRow3 = $footerRow + 3;
                $sheet->mergeCells('A' . $noteRow3 . ':E' . $noteRow3);
                $sheet->setCellValue('A' . $noteRow3, '- Dokumen ini dicetak secara otomatis dari sistem');

                $sheet->getStyle('A' . $noteRow1 . ':A' . $noteRow3)->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['rgb' => '666666']]
                ]);

                $companyRow = $footerRow + 5;
                $sheet->mergeCells('A' . $companyRow . ':E' . $companyRow);
                $sheet->setCellValue('A' . $companyRow, '© ' . date('Y') . ' - PT MITRA PANEL CHERBOND');
                $sheet->getStyle('A' . $companyRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
            }
        ];
    }
}
