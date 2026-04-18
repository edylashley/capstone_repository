@props(['url'])
<tr>
<td class="header" align="center" style="text-align: center !important; padding: 30px 0;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none; width: 100%;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            {{-- Using a reliable public placeholder for local testing --}}
            @php
                $logoUrl = (str_contains(config('app.url'), 'localhost') || str_contains(config('app.url'), '192.168')) 
                    ? 'https://img.icons8.com/clouds/100/000000/graduation-cap.png' 
                    : url('/images/system-logo.jpg');
            @endphp
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" style="height: 70px; width: auto; display: block; margin: 0 auto; border: none; outline: none;">
        </td>
    </tr>
    <tr>
        <td align="center" style="text-align: center !important;">
            <h2 style="color: #4f46e5 !important; margin: 0 !important; font-size: 24px !important; font-weight: 900 !important; text-transform: uppercase !important; letter-spacing: 2px !important; line-height: 1.2 !important; text-align: center !important;">
                CSIT Research Library
            </h2>
            <p style="color: #64748b !important; font-size: 11px !important; font-weight: bold !important; text-transform: uppercase !important; margin: 8px 0 0 0 !important; letter-spacing: 1.5px !important; line-height: 1 !important; text-align: center !important;">
                Official Institutional Archive
            </p>
        </td>
    </tr>
</table>
</a>
</td>
</tr>