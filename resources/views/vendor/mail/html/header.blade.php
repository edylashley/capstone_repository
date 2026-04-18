@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="vertical-align: middle; padding-right: 15px;">
            <img src="{{ url('/images/system-logo.jpg') }}" alt="{{ config('app.name') }}" style="height: 50px; border-radius: 4px;">
        </td>
        <td style="vertical-align: middle; text-align: left;">
            @if (trim($slot) === 'CSIT Research Library' || trim($slot) === 'Laravel')
            <h2 style="color: #4f46e5; margin: 0; font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; line-height: 1;">
                CSIT Research Library
            </h2>
            <p style="color: #a1a1aa; font-size: 9px; font-weight: bold; text-transform: uppercase; margin: 4px 0 0 0; letter-spacing: 0.5px; line-height: 1;">
                Official Institutional Archive
            </p>
            @else
            {!! $slot !!}
            @endif
        </td>
    </tr>
</table>
</a>
</td>
</tr>
