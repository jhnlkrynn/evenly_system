<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>@yield('title')</title>
<style>
    @page { margin: 34px 38px 46px; }
    * { box-sizing: border-box; }
    body { margin: 0; color: #17100c; font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.45; }
    h1, h2, .serif { font-family: 'DejaVu Serif', serif; }
    .masthead { width: 100%; border-collapse: collapse; background: #17100c; color: #fff; }
    .masthead td { padding: 18px 20px; vertical-align: middle; }
    .masthead img { width: 46px; height: 46px; }
    .brand { color: #fff; font-family: 'DejaVu Serif', serif; font-size: 18px; font-weight: bold; }
    .brand small { display: block; color: #c69a3a; font-family: 'DejaVu Sans', sans-serif; font-size: 8px; letter-spacing: 2px; text-transform: uppercase; }
    .doc-title { text-align: right; }
    .doc-title h1 { margin: 0; color: #c69a3a; font-size: 18px; }
    .doc-title p { margin: 2px 0 0; color: #cfc4b5; font-size: 9px; }
    .gold-rule { height: 3px; background: #c69a3a; }
    .section { margin-top: 18px; }
    .section h2 { margin: 0 0 8px; padding-bottom: 5px; border-bottom: 1px solid #e4dccf; font-size: 12px; }
    .grid { width: 100%; border-collapse: collapse; }
    .grid td { width: 50%; padding: 5px 10px 5px 0; vertical-align: top; }
    .label { display: block; color: #8a7f75; font-size: 7.5px; letter-spacing: 1px; text-transform: uppercase; }
    .value { font-size: 10.5px; font-weight: bold; }
    .pill { display: inline-block; padding: 3px 9px; border-radius: 9px; font-size: 8.5px; font-weight: bold; }
    .pill-pending, .pill-partial { background: #fff3d6; color: #8a6418; }
    .pill-confirmed, .pill-paid { background: #e3f2e6; color: #2e7d32; }
    .pill-completed { background: #e3ebfb; color: #3b5fc4; }
    .pill-cancelled, .pill-unpaid { background: #fbe1e6; color: #b3261e; }
    table.lines { width: 100%; border-collapse: collapse; }
    table.lines th { padding: 7px 8px; background: #f5f1e8; color: #6f655c; font-size: 7.5px; letter-spacing: 1px; text-align: left; text-transform: uppercase; }
    table.lines td { padding: 8px; border-bottom: 1px solid #eee7dd; }
    .right { text-align: right; }
    .muted { color: #776d64; }
    .totals { width: 46%; margin-left: 54%; border-collapse: collapse; margin-top: 10px; }
    .totals td { padding: 5px 8px; }
    .totals .grand td { border-top: 2px solid #17100c; font-size: 12px; font-weight: bold; }
    .totals .balance td { background: #17100c; color: #fff; font-size: 12px; font-weight: bold; }
    .totals .balance td.right { color: #c69a3a; }
    .note { margin-top: 18px; padding: 10px 12px; background: #f5f1e8; border-left: 3px solid #c69a3a; color: #5f554d; font-size: 8.5px; }
    .footer { position: fixed; bottom: -28px; left: 0; right: 0; color: #9a8f84; font-size: 7.5px; text-align: center; }
</style>
</head>
<body>
    <div class="footer">Eventlynk · Nasugbu, Batangas · Generated {{ now()->format('M j, Y g:i A') }} · {{ $booking->booking_code }}</div>

    <table class="masthead">
        <tr>
            <td style="width: 60px;"><img src="{{ public_path('images/eventlynk-logo-sm.png') }}" alt=""></td>
            <td><span class="brand">Eventlynk<small>Event services · Nasugbu, Batangas</small></span></td>
            <td class="doc-title">
                <h1>@yield('heading')</h1>
                <p>@yield('subheading')</p>
            </td>
        </tr>
    </table>
    <div class="gold-rule"></div>

    @yield('content')
</body>
</html>
