<?php
require __DIR__ . '/lib.php';

function back(string $e) { header('Location: ./?e=' . urlencode($e) . '#get', true, 303); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ./', true, 303); exit; }

// Spam traps: honeypot filled, or missing session token, are silently accepted but not processed
$isBot = !empty($_POST['website']) || empty($_POST['t']) || ($_COOKIE['hog_t'] ?? '') !== ($_POST['t'] ?? '');

$name    = hog_clean($_POST['name'] ?? '', 80);
$email   = strtolower(hog_clean($_POST['email'] ?? '', 120));
$company = hog_clean($_POST['company'] ?? '', 120);
$consent = !empty($_POST['consent']);
$optin   = !empty($_POST['optin']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) back('email');
if (!$consent) back('consent');

$first = $name !== '' ? explode(' ', $name)[0] : '';

if (!$isBot) {
    // Simple rate limit: max 5 submissions per IP per hour
    $dir = hog_data_dir();
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
    $rl  = $dir . '/rl_' . md5($ip . date('YmdH'));
    $cnt = (int)@file_get_contents($rl);
    if ($cnt >= 5) back('rate');
    @file_put_contents($rl, (string)($cnt + 1));

    $file = $dir . '/leads.csv';
    $new  = !file_exists($file);
    $fh   = fopen($file, 'a');
    if ($fh) {
        flock($fh, LOCK_EX);
        if ($new) fputcsv($fh, ['submitted_utc', 'name', 'email', 'company', 'guide_consent', 'marketing_optin', 'source', 'emailed']);
        $src = hog_clean($_SERVER['HTTP_REFERER'] ?? '', 200);
        $utm = hog_clean($_GET['utm_source'] ?? '', 40);
        $link = 'https://guide.harbour-one-group.co.uk/' . HOG_GUIDE_FILE;

        $text = "Dear " . ($first ?: 'there') . ",\n\nThank you for requesting The Owner's Guide to Selling Your Business.\n\nYou can download your copy here:\n$link\n\nInside you'll find a preparation checklist, a 10-question readiness check, guidance on choosing the right buyer and a plain-English glossary.\n\nIf you'd ever like a confidential, no-obligation conversation about your options, simply reply to this email. A director will respond within one working day.\n\nKind regards,\n\nSam\nDirector\nHarbour One Group\nwww.harbour-one-group.co.uk\n";
        $html = '<div style="font-family:Arial,sans-serif;font-size:15px;line-height:1.6;color:#07141F;max-width:560px">'
              . '<p>Dear ' . htmlspecialchars($first ?: 'there') . ',</p>'
              . '<p>Thank you for requesting <b>The Owner\'s Guide to Selling Your Business</b>.</p>'
              . '<p><a href="' . $link . '" style="display:inline-block;background:#C9A86A;color:#07141F;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:999px">Download your free guide (PDF)</a></p>'
              . '<p>Inside you\'ll find a preparation checklist, a 10-question readiness check, guidance on choosing the right buyer and a plain-English glossary.</p>'
              . '<p>If you\'d ever like a confidential, no-obligation conversation about your options, simply reply to this email. A director will respond within one working day.</p>'
              . '<p>Kind regards,<br><br>Sam<br>Director<br>Harbour One Group<br><a href="https://www.harbour-one-group.co.uk" style="color:#3E8A9A">www.harbour-one-group.co.uk</a></p></div>';
        $ok = hog_send_mail($email, "Your free guide: The Owner's Guide to Selling Your Business", $text, $html);

        fputcsv($fh, [gmdate('c'), $name, $email, $company, $consent ? 'yes' : 'no', $optin ? 'yes' : 'no', $utm ?: $src, $ok ? 'yes' : 'no']);
        flock($fh, LOCK_UN);
        fclose($fh);

        $nt = "New guide request\n\nName: $name\nEmail: $email\nBusiness: " . ($company ?: '-') . "\nMarketing opt-in: " . ($optin ? 'yes' : 'no') . "\nSource: " . ($utm ?: $src ?: '-') . "\nTime (UTC): " . gmdate('Y-m-d H:i') . "\n";
        hog_send_mail(HOG_NOTIFY_TO, 'New guide request: ' . ($name ?: $email), $nt, nl2br(htmlspecialchars($nt)));
    }
}

header('Location: ./?sent=1&n=' . urlencode($first) . '#get', true, 303);
exit;
