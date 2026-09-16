<?php

namespace App\Support;

class OwnerHealthPdf
{
    public static function make(string $name, $records): string
    {
        $lines = ['FurShield - Pet Health History', 'Pet: '.$name, 'Exported: '.now()->format('d M Y H:i'), ''];
        foreach ($records as $r) {
            $lines[] = ($r->date?->format('d M Y') ?? '').' | '.ucwords(str_replace('_', ' ', $r->type)).' | '.$r->title;
            foreach ([$r->details, $r->vet ? ('Vet: '.$r->vet.($r->clinic ? ' / '.$r->clinic : '')) : '', $r->notes] as $text) {
                if ($text) {
                    foreach (explode("\n", wordwrap(strip_tags($text), 90, "\n", true)) as $line) {
                        $lines[] = $line;
                    }
                }
            }
            $lines[] = '';
        }
        if (! $records->count()) {
            $lines[] = 'No health records yet.';
        }
        $wrapped = [];
        foreach ($lines as $line) {
            foreach (explode("\n", wordwrap(str_replace("\r", '', $line), 90, "\n", true)) as $part) {
                $wrapped[] = $part;
            }
        }
        $pages = array_chunk($wrapped, 47);
        $objects = [1 => '<< /Type /Catalog /Pages 2 0 R >>', 3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>'];
        $kids = [];
        foreach ($pages as $i => $page) {
            $id = 4 + $i * 2;
            $streamId = $id + 1;
            $kids[] = "$id 0 R";
            $stream = "BT /F1 9 Tf 42 800 Td 15 TL\n";
            foreach ($page as $line) {
                $line = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $line);
                $line = str_replace(['\\', '(', ')', "\r"], ['\\\\', '\\(', '\\)', ''], $line);
                $stream .= '('.$line.") Tj T*\n";
            }
            $stream .= '(Page '.($i + 1).' of '.count($pages).") Tj\nET";
            $objects[$id] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents $streamId 0 R >>";
            $objects[$streamId] = '<< /Length '.strlen($stream).">>\nstream\n".$stream."\nendstream";
        }
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.count($pages).' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "$id 0 obj\n$body\nendobj\n";
        }
        $xref = strlen($pdf);
        $size = count($objects) + 1;
        $pdf .= "xref\n0 $size\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf."trailer\n<< /Size $size /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
    }
}
