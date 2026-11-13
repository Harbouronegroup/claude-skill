<?php
require __DIR__ . '/lib.php';
$sent  = isset($_GET['sent']);
$error = $_GET['e'] ?? '';
$token = bin2hex(random_bytes(16));
setcookie('hog_t', $token, ['expires' => time() + 7200, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax']);
$name = isset($_GET['n']) ? htmlspecialchars(hog_clean($_GET['n'], 60), ENT_QUOTES) : '';
?><!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Free Guide: The Owner's Guide to Selling Your Business | Harbour One Group</title>
<meta name="description" content="A free, plain-English guide for UK business owners: how to prepare, choose the right buyer and protect the team, customers and reputation you built.">
<link rel="canonical" href="https://guide.harbour-one-group.co.uk/">
<meta property="og:title" content="Free guide: The Owner's Guide to Selling Your Business">
<meta property="og:description" content="How to prepare, choose the right buyer and protect your legacy. Free from Harbour One Group.">
<meta property="og:image" content="https://guide.harbour-one-group.co.uk/social/og.jpg">
<meta property="og:url" content="https://guide.harbour-one-group.co.uk/">
<meta name="theme-color" content="#07141F">
<link rel="icon" href="logo.php">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
<style>
:root{--ink:#07141F;--ink2:#0B1D2C;--ink3:#10283B;--line:rgba(226,214,190,.14);--ivory:#F3EDE2;--mute:#A9B3BC;--dim:#6E7C88;--brass:#C9A86A;--brass2:#E6CC92;--sea:#3E8A9A;--glow:rgba(201,168,106,.35);
--serif:"Instrument Serif",Georgia,serif;--sans:"Manrope",system-ui,sans-serif;--mono:"JetBrains Mono",ui-monospace,monospace}
*{box-sizing:border-box;margin:0;padding:0}
body{background:var(--ink);color:var(--ivory);font-family:var(--sans);font-size:17px;line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
a{color:inherit}
.bg{position:fixed;inset:0;pointer-events:none;background:radial-gradient(60% 50% at 80% 15%,rgba(62,138,154,.20),transparent 70%),radial-gradient(50% 45% at 5% 95%,rgba(201,168,106,.14),transparent 70%)}
.wrap{max-width:1180px;margin:0 auto;padding:0 24px;position:relative}
@media(max-width:600px){.wrap{padding:0 16px}}
header{padding:22px 0;display:flex;align-items:center;justify-content:space-between}
.brand{display:flex;align-items:center;gap:12px;text-decoration:none}
.brand img{height:40px;width:auto}
.wm{display:flex;flex-direction:column;line-height:1}
.wm1{font-size:13.5px;font-weight:500;letter-spacing:.24em;text-transform:uppercase}
.wm1 em{font-style:normal;color:var(--brass2)}
.wm2{font-size:9.5px;letter-spacing:.42em;text-transform:uppercase;color:var(--mute);margin-top:5px;display:flex;align-items:center;gap:8px}
.wm2::before,.wm2::after{content:"";height:1px;width:14px;background:var(--brass);opacity:.7}
.back{font-size:14px;color:var(--mute);text-decoration:none}
.back:hover{color:var(--ivory)}
.hero{display:grid;grid-template-columns:1.1fr .9fr;gap:56px;align-items:start;padding:48px 0 80px}
@media(max-width:900px){.hero{grid-template-columns:1fr;gap:36px;padding-top:24px}}
.eyebrow{font-family:var(--mono);font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:var(--brass);display:inline-flex;align-items:center;gap:10px}
.eyebrow::before{content:"";width:22px;height:1px;background:var(--brass)}
h1{font-family:var(--serif);font-weight:400;font-size:clamp(46px,7vw,88px);line-height:1.02;letter-spacing:-.01em;margin:20px 0 22px}
h1 em{color:var(--brass2)}
.lead{color:var(--mute);font-size:clamp(17px,1.6vw,20px);max-width:580px}
.inside{margin-top:36px;display:grid;gap:14px;max-width:560px}
.inside div{display:flex;gap:14px;align-items:flex-start;color:#D6DBDF;font-size:16px}
.inside svg{flex:none;width:20px;height:20px;color:var(--brass);margin-top:3px}
.trust{display:flex;flex-wrap:wrap;gap:10px 26px;margin-top:36px;font-size:14px;color:var(--mute)}
.card{background:linear-gradient(180deg,rgba(16,40,59,.85),rgba(11,29,44,.9));border:1px solid var(--line);border-radius:22px;padding:34px;box-shadow:0 30px 80px -30px rgba(0,0,0,.6);position:sticky;top:24px}
@media(max-width:600px){.card{padding:24px}}
.book{display:flex;gap:18px;align-items:center;margin-bottom:24px}
.cover{width:74px;height:104px;border-radius:4px 8px 8px 4px;background:linear-gradient(135deg,#0B1D2C,#07141F);border:1px solid rgba(201,168,106,.45);box-shadow:6px 8px 20px rgba(0,0,0,.45);position:relative;flex:none;padding:10px 8px;font-family:var(--serif);font-size:11px;line-height:1.1}
.cover em{color:var(--brass2)}
.cover::after{content:"";position:absolute;left:4px;top:0;bottom:0;width:1px;background:rgba(201,168,106,.35)}
.card h2{font-family:var(--serif);font-weight:400;font-size:30px;line-height:1.1}
.card h2 em{color:var(--brass2)}
.card .sub{color:var(--mute);font-size:14px;margin-top:4px}
label{display:block;font-size:13px;font-weight:600;letter-spacing:.02em;margin:16px 0 7px}
input[type=text],input[type=email]{width:100%;padding:14px 16px;border-radius:12px;border:1px solid rgba(243,237,226,.18);background:rgba(7,20,31,.6);color:var(--ivory);font:inherit;font-size:16px;outline:none;transition:border .25s}
input[type=text]:focus,input[type=email]:focus{border-color:var(--brass)}
.chk{display:flex;gap:11px;align-items:flex-start;font-size:13.5px;color:var(--mute);margin-top:14px;font-weight:400;letter-spacing:0;line-height:1.5}
.chk input{margin-top:3px;accent-color:var(--brass);width:17px;height:17px;flex:none}
.chk a{color:var(--brass2)}
.btn{margin-top:22px;width:100%;display:inline-flex;justify-content:center;align-items:center;gap:12px;padding:17px 26px;border-radius:999px;border:0;cursor:pointer;font:inherit;font-weight:700;font-size:16px;background:linear-gradient(135deg,var(--brass2),var(--brass));color:var(--ink);box-shadow:0 10px 40px -10px var(--glow),inset 0 1px 0 rgba(255,255,255,.5);transition:transform .3s}
.btn:hover{transform:translateY(-2px)}
.small{font-size:12.5px;color:var(--dim);margin-top:14px;text-align:center}
.err{background:rgba(190,80,60,.15);border:1px solid rgba(220,120,100,.4);color:#F1C9BE;border-radius:12px;padding:12px 14px;font-size:14px;margin-top:16px}
.hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}
.done h2{font-size:40px}
.done p{color:var(--mute);margin-top:12px}
.done .btn{text-decoration:none}
.mcta{display:none}
@media(max-width:900px){.mcta{display:inline-flex;width:auto;margin-top:26px;text-decoration:none}}
.charter{border-top:1px solid var(--line);padding:70px 0 40px}
.charter h3{font-family:var(--serif);font-weight:400;font-size:clamp(32px,4vw,48px);line-height:1.1;margin:16px 0 34px}
.charter h3 em{color:var(--brass2)}
.grid{display:grid;grid-template-columns:repeat(5,1fr);gap:18px}
@media(max-width:900px){.grid{grid-template-columns:1fr 1fr}}
@media(max-width:520px){.grid{grid-template-columns:1fr}}
.grid div{border:1px solid var(--line);border-radius:16px;padding:20px;background:rgba(243,237,226,.02)}
.grid b{display:block;font-size:15px;margin:8px 0 6px}
.grid span{font-family:var(--mono);font-size:11px;color:var(--brass)}
.grid p{font-size:14px;color:var(--mute);line-height:1.5}
footer{border-top:1px solid var(--line);padding:28px 0 40px;font-size:13px;color:var(--dim);display:flex;flex-wrap:wrap;justify-content:space-between;gap:12px}
footer a{color:var(--mute);text-decoration:none}
</style>
</head>
<body>
<div class="bg"></div>
<div class="wrap">
<header>
  <a class="brand" href="https://www.harbour-one-group.co.uk/" aria-label="Harbour One Group home"><img src="logo.php" alt=""><div class="wm"><span class="wm1">Harbour <em>One</em></span><span class="wm2">Group</span></div></a>
  <a class="back" href="https://www.harbour-one-group.co.uk/">Visit our website →</a>
</header>

<section class="hero">
  <div>
    <span class="eyebrow">Free guide for UK business owners</span>
    <h1>The Owner's Guide to <em>Selling Your Business</em></h1>
    <p class="lead">Thinking about the next chapter? This free, plain-English guide shows you how to prepare, how to choose the right buyer, and how to protect the team, the customers and the reputation you spent years building.</p>
    <a class="btn mcta" href="#get">Get your free copy <span>↓</span></a>
    <div class="inside">
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>When to start planning</b>, and why selling from strength matters</span></div>
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>What buyers really look at</b>, beyond the balance sheet</span></div>
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>A preparation checklist</b> and a 10-question readiness check</span></div>
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>How to choose the right buyer</b>: questions to ask and red flags</span></div>
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>The sale process step by step</b>, and how value really works</span></div>
      <div><svg viewBox="0 0 20 20"><path d="M4 10.5l4 4 8-9" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>A plain-English glossary</b> of 30 terms you will hear</span></div>
    </div>
    <div class="trust"><span>✦ 14 pages, free PDF</span><span>✦ No obligation</span><span>✦ Your details are never shared</span></div>
  </div>

  <div class="card" id="get">
  <?php if ($sent): ?>
    <div class="done">
      <span class="eyebrow">Thank you<?= $name ? ', ' . $name : '' ?></span>
      <h2 style="margin-top:14px">Your copy is <em>on its way.</em></h2>
      <p>We've emailed the guide to you. If it hasn't arrived in a few minutes, please check your junk folder. You can also download it straight away:</p>
      <a class="btn" href="<?= HOG_GUIDE_FILE ?>" download>Download the guide (PDF) <span>↓</span></a>
      <p style="font-size:14px">Whenever you're ready for a confidential, no-obligation conversation, a director will reply within one working day: <a href="mailto:info@harbouronegroup.co.uk" style="color:var(--brass2)">info@harbouronegroup.co.uk</a></p>
    </div>
  <?php else: ?>
    <div class="book"><div class="cover">The Owner's Guide to <em>Selling Your Business</em></div><div><h2>Get your <em>free copy</em></h2><div class="sub">We'll email it to you straight away.</div></div></div>
    <?php if ($error): ?><div class="err"><?= $error === 'email' ? 'Please enter a valid email address.' : ($error === 'consent' ? 'Please tick the box so we can send you the guide.' : 'Something went wrong. Please try again.') ?></div><?php endif; ?>
    <form method="post" action="submit.php" novalidate>
      <input type="hidden" name="t" value="<?= $token ?>">
      <div class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
      <label for="name">Your name</label>
      <input id="name" type="text" name="name" required maxlength="80" autocomplete="name" placeholder="First and last name">
      <label for="email">Email address</label>
      <input id="email" type="email" name="email" required maxlength="120" autocomplete="email" placeholder="you@yourbusiness.co.uk">
      <label for="company">Business name <span style="font-weight:400;color:var(--dim)">(optional)</span></label>
      <input id="company" type="text" name="company" maxlength="120" autocomplete="organization">
      <label class="chk"><input type="checkbox" name="consent" value="1" required> <span>Please email me the guide. I have read the <a href="https://www.harbour-one-group.co.uk/privacy-policy.html" target="_blank" rel="noopener">Privacy Policy</a>.</span></label>
      <label class="chk"><input type="checkbox" name="optin" value="1"> <span>Occasionally send me practical insights for business owners. I can unsubscribe at any time.</span></label>
      <button class="btn" type="submit">Send me the free guide <span>→</span></button>
      <p class="small">Strictly confidential. We never share or sell your details.</p>
    </form>
  <?php endif; ?>
  </div>
</section>

<section class="charter">
  <span class="eyebrow">Who we are</span>
  <h3>Harbour One Group acquires established UK businesses <em>to own for the long term.</em></h3>
  <div class="grid">
    <div><span>01</span><b>Total confidentiality</b><p>No one hears about a conversation unless you decide they should.</p></div>
    <div><span>02</span><b>Your team is the asset</b><p>Our plans are built around keeping and developing your people.</p></div>
    <div><span>03</span><b>Clear terms, in writing</b><p>Every payment, date and obligation set out plainly.</p></div>
    <div><span>04</span><b>A handover at your pace</b><p>Step back gradually or move on quickly. Your choice.</p></div>
    <div><span>05</span><b>Investment, not extraction</b><p>Stronger in five years than the day we arrived.</p></div>
  </div>
</section>

<footer>
  <span>© Harbour One Group <?= date('Y') ?>. Confidential conversations. No obligation.</span>
  <span><a href="https://www.harbour-one-group.co.uk/privacy-policy.html">Privacy Policy</a> &nbsp;·&nbsp; <a href="https://www.harbour-one-group.co.uk/cookie-policy.html">Cookie Policy</a> &nbsp;·&nbsp; <a href="mailto:info@harbouronegroup.co.uk">info@harbouronegroup.co.uk</a></span>
</footer>
</div>
</body>
</html>
