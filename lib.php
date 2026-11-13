<?php
// Shared helpers for guide.harbour-one-group.co.uk
const HOG_GUIDE_FILE  = 'files/Owners-Guide-to-Selling-Your-Business.pdf';
const HOG_NOTIFY_TO   = 'info@harbouronegroup.co.uk';
const HOG_FROM        = 'guide@harbour-one-group.co.uk';
const HOG_FROM_NAME   = 'Harbour One Group';
const HOG_REPLY_TO    = 'info@harbouronegroup.co.uk';

function hog_data_dir(): string {
    $d = __DIR__ . '/data';
    if (!is_dir($d)) { @mkdir($d, 0750, true); }
    if (!file_exists($d . '/.htaccess')) { @file_put_contents($d . '/.htaccess', "Require all denied\nDeny from all\n"); }
    return $d;
}

/** Locate the Harbour One logo: cached copy, else extract from the main site's index.html, else favicon. */
function hog_logo_path(): ?string {
    $cache = hog_data_dir() . '/logo.png';
    if (is_file($cache) && filesize($cache) > 0) return $cache;
    $candidates = [
        dirname(__DIR__) . '/index.html',
        dirname(__DIR__, 2) . '/public_html/index.html',
        '/home/u675334077/domains/harbour-one-group.co.uk/public_html/index.html',
    ];
    foreach ($candidates as $idx) {
        if (!is_file($idx)) continue;
        $html = file_get_contents($idx);
        if (preg_match('~class="brand"[^>]*>\s*<img src="data:image/(webp|png);base64,([A-Za-z0-9+/=]+)"~', $html, $m)) {
            $raw = base64_decode($m[2]);
            $img = @imagecreatefromstring($raw);
            if ($img) {
                imagesavealpha($img, true);
                imagepng($img, $cache);
                imagedestroy($img);
                return $cache;
            }
        }
        $fav = dirname($idx) . '/favicon.png';
        if (is_file($fav)) { copy($fav, $cache); return $cache; }
    }
    return null;
}

function hog_clean(string $s, int $max = 200): string {
    $s = trim(preg_replace('/[\r\n\t]+/', ' ', $s));
    return mb_substr($s, 0, $max);
}

function hog_send_mail(string $to, string $subject, string $text, string $html): bool {
    $boundary = 'hog_' . bin2hex(random_bytes(8));
    $headers  = [];
    $headers[] = 'From: ' . HOG_FROM_NAME . ' <' . HOG_FROM . '>';
    $headers[] = 'Reply-To: ' . HOG_REPLY_TO;
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
    $body  = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n$text\r\n";
    $body .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n$html\r\n";
    $body .= "--$boundary--\r\n";
    $encSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    return @mail($to, $encSubject, $body, implode("\r\n", $headers), '-f' . HOG_FROM);
}
