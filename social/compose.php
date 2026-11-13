<?php
// Adds the Harbour One logo to each base design in raw/ and saves a JPEG next to this script.
// Web: compose.php?n=001 (used by .htaccess when a JPEG doesn't exist yet). CLI: php compose.php all
require dirname(__DIR__) . '/lib.php';

// name => [logo width, right margin, bottom margin, mode]
function hog_spec(string $n): array {
    switch ($n) {
        case 'og':        return [120, 60, 50, 'corner'];
        case 'profile':   return [250, 0, 0, 'centre'];
        case 'fb_cover':  return [230, 150, 0, 'right-middle'];
        case 'li_banner': return [150, 110, 0, 'right-middle'];
        default:          return [150, 84, 70, 'corner'];
    }
}

function hog_compose(string $n, bool $force = false): ?string {
    if (!preg_match('/^(\d{3}|og|profile|fb_cover|li_banner)$/', $n)) return null;
    $raw = __DIR__ . "/raw/$n.jpg";
    $out = __DIR__ . "/$n.jpg";
    if (!is_file($raw)) return null;
    if (!$force && is_file($out) && filemtime($out) >= filemtime($raw)) return $out;
    $base = imagecreatefromjpeg($raw);
    if (!$base) return null;
    $W = imagesx($base); $H = imagesy($base);
    $lp = hog_logo_path();
    if ($lp) {
        $logo = imagecreatefromstring(file_get_contents($lp));
        if ($logo) {
            imagealphablending($logo, true);
            [$lw, $mr, $mb, $mode] = hog_spec($n);
            $ow = imagesx($logo); $oh = imagesy($logo);
            $lh = (int)round($lw * $oh / $ow);
            if ($mode === 'centre')            { $x = (int)(($W - $lw) / 2); $y = (int)(($H - $lh) / 2); }
            elseif ($mode === 'right-middle')  { $x = $W - $mr - $lw;        $y = (int)(($H - $lh) / 2); }
            else                               { $x = $W - $mr - $lw;        $y = $H - $mb - $lh; }
            imagealphablending($base, true);
            imagecopyresampled($base, $logo, $x, $y, 0, 0, $lw, $lh, $ow, $oh);
            imagedestroy($logo);
        }
    }
    imageinterlace($base, true);
    imagejpeg($base, $out, 92);
    imagedestroy($base);
    return $out;
}

if (PHP_SAPI === 'cli') {
    $force = in_array('force', $argv, true);
    $names = [];
    foreach (glob(__DIR__ . '/raw/*.jpg') as $f) $names[] = basename($f, '.jpg');
    $done = 0; $fail = [];
    foreach ($names as $n) { hog_compose($n, $force) ? $done++ : $fail[] = $n; }
    $lp = hog_logo_path();
    $info = $lp ? getimagesize($lp) : null;
    $report = "composed=$done failed=" . implode(',', $fail) . "\nlogo=" . ($lp ?: 'NONE') . ' ' . ($info ? $info[0] . 'x' . $info[1] : '') .
              "\ngd=" . json_encode(function_exists('gd_info') ? gd_info() : null) . "\ntime=" . gmdate('c') . "\n";
    file_put_contents(hog_data_dir() . '/compose-report.txt', $report);
    echo $report;
    exit;
}

$n = $_GET['n'] ?? '';
$out = hog_compose($n);
if (!$out) { http_response_code(404); exit('Not found'); }
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
readfile($out);
