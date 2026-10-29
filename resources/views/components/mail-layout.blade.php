<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;">
    <tr>
        <td align="center" style="padding:24px 12px;">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;border:1px solid #e2e8f0;">
                <tr>
                    <td style="background:#111111;padding:16px 24px;border-radius:16px 16px 0 0;">
                        <img src="{{ asset('images/logo-ut.svg') }}" alt="UT" width="34" height="34"
                             style="vertical-align:middle;border-radius:8px;background:#FDC300;padding:4px;">
                        <span style="color:#FDC300;font-weight:bold;font-size:16px;vertical-align:middle;margin-left:10px;">Sistem Magang UT</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:24px;">
                        <h2 style="margin:0 0 14px;color:#0f172a;font-size:18px;line-height:1.4;">{{ $judul }}</h2>
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="background:#f8fafc;padding:16px 24px;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;color:#64748b;font-size:12px;line-height:1.6;">
                        PT United Tractors Samarinda &middot; Sistem Pendaftaran &amp; Manajemen Magang (Prakerin).<br>
                        Email ini dikirim otomatis oleh sistem dan mohon tidak membalas email ini.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
