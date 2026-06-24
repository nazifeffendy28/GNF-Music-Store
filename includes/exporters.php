<?php
/**
 * includes/exporters.php
 * Fungsi pembuat file ekspor tanpa library eksternal:
 *  - build_pdf()  : PDF sederhana (teks monospace) untuk struk
 *  - build_xlsx() : file Excel .xlsx (via ZipArchive)
 * CSV ditangani langsung di export.php karena sangat singkat.
 */

/** Membuat PDF satu halaman berisi baris-baris teks. */
function build_pdf(array $lines) {
    $fs = 10; $lead = 15; $x = 40; $y = 800;
    $stream = "BT /F1 $fs Tf $lead TL $x $y Td\n";
    foreach ($lines as $i => $ln) {
        $t = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ln);
        $stream .= ($i === 0 ? '' : 'T* ') . "($t) Tj\n";
    }
    $stream .= "ET";

    $objs = [];
    $objs[1] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objs[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objs[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>";
    $objs[4] = "<< /Length " . strlen($stream) . " >>\nstream\n$stream\nendstream";
    $objs[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>";

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    for ($i = 1; $i <= 5; $i++) {
        $offsets[$i] = strlen($pdf);
        $pdf .= "$i 0 obj\n{$objs[$i]}\nendobj\n";
    }
    $xref = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";
    for ($i = 1; $i <= 5; $i++) $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
    return $pdf;
}

/** Nama kolom Excel dari indeks (0=A, 1=B, ...). */
function xlsx_col($i) {
    $s = '';
    $i++;
    while ($i > 0) { $m = ($i - 1) % 26; $s = chr(65 + $m) . $s; $i = (int)(($i - 1) / 26); }
    return $s;
}

/** Membuat file .xlsx dari array baris (tiap baris = array sel). Mengembalikan byte string. */
function build_xlsx(array $rows) {
    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
    $r = 1;
    foreach ($rows as $row) {
        $sheet .= '<row r="' . $r . '">';
        $c = 0;
        foreach ($row as $cell) {
            $ref = xlsx_col($c) . $r;
            if (is_int($cell) || (is_string($cell) && $cell !== '' && ctype_digit($cell))) {
                $sheet .= '<c r="' . $ref . '"><v>' . $cell . '</v></c>';
            } else {
                $v = htmlspecialchars((string)$cell, ENT_QUOTES, 'UTF-8');
                $sheet .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">' . $v . '</t></is></c>';
            }
            $c++;
        }
        $sheet .= '</row>';
        $r++;
    }
    $sheet .= '</sheetData></worksheet>';

    $ct = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '</Types>';
    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';
    $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Struk" sheetId="1" r:id="rId1"/></sheets></workbook>';
    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '</Relationships>';

    $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);
    $zip->addFromString('[Content_Types].xml', $ct);
    $zip->addFromString('_rels/.rels', $rels);
    $zip->addFromString('xl/workbook.xml', $wb);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);
    $zip->close();
    $data = file_get_contents($tmp);
    @unlink($tmp);
    return $data;
}
