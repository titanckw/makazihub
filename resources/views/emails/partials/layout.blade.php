<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'MakaziHub' }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #F8FAFC; color: #111827; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  .header { background: #0F172A; padding: 28px 40px; }
  .header-logo { display: flex; align-items: center; gap: 10px; }
  .header-logo-icon { width: 36px; height: 36px; background: #059669; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
  .logo-text { color: #ffffff; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
  .logo-sub { color: #94A3B8; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; }
  .body { padding: 40px; }
  .greeting { font-size: 15px; color: #475569; margin-bottom: 20px; }
  h2 { font-size: 22px; font-weight: 700; color: #0F172A; margin-bottom: 8px; }
  .subtitle { font-size: 14px; color: #475569; margin-bottom: 28px; }
  .card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
  .card-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #E2E8F0; font-size: 14px; }
  .card-row:last-child { border-bottom: none; }
  .card-label { color: #475569; }
  .card-value { font-weight: 600; color: #0F172A; }
  .amount-highlight { font-size: 28px; font-weight: 800; color: #059669; margin: 8px 0; }
  .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .badge-paid    { background: #DCFCE7; color: #16A34A; }
  .badge-unpaid  { background: #FEF3C7; color: #D97706; }
  .badge-overdue { background: #FEE2E2; color: #DC2626; }
  .badge-partial { background: #DBEAFE; color: #2563EB; }
  .btn { display: inline-block; padding: 12px 28px; background: #059669; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 8px; }
  .btn:hover { background: #10B981; }
  .divider { border: none; border-top: 1px solid #E2E8F0; margin: 24px 0; }
  .footer { background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 24px 40px; text-align: center; }
  .footer p { font-size: 12px; color: #94A3B8; line-height: 1.6; }
  .footer a { color: #475569; text-decoration: none; }
  .warning-box { background: #FEF3C7; border-left: 4px solid #D97706; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; color: #92400E; }
  .danger-box { background: #FEE2E2; border-left: 4px solid #DC2626; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; color: #991B1B; }
  .success-box { background: #DCFCE7; border-left: 4px solid #16A34A; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; color: #14532D; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div class="header-logo">
      <div class="header-logo-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div>
        <div class="logo-text">MakaziHub</div>
        <div class="logo-sub">Property Management</div>
      </div>
    </div>
  </div>
  <div class="body">
    {{ $slot }}
  </div>
  <div class="footer">
    <p>
      This email was sent by <strong>MakaziHub</strong> on behalf of your property manager.<br>
      If you have questions, contact your property manager directly.<br><br>
      <a href="#">Tenant Portal</a> &nbsp;·&nbsp; <a href="#">Privacy Policy</a>
    </p>
  </div>
</div>
</body>
</html>
