@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'CSIT Research Library' || trim($slot) === 'Laravel')
<h2 style="color: #4f46e5; margin: 0; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px;">
    CSIT Research Library
</h2>
<p style="color: #a1a1aa; font-size: 10px; font-weight: bold; text-transform: uppercase; margin: 5px 0 0 0; letter-spacing: 1px;">
    Official Institutional Archive
</p>
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
