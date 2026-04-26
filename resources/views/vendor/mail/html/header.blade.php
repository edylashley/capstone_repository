@props(['url'])
<tr>
    <td class="header" align="center" style="padding: 40px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
            <tr>
                <!-- Logo with White Wrap Glow -->
                <td style="padding-right: 20px; vertical-align: middle;">
                    @php
                        $logoUrl = 'https://i.postimg.cc/2SSPY06F/erasebg-transformed-(2).png';
                    @endphp
                    <img src="{{ $logoUrl }}" alt="CSIT Logo"
                        style="height: 70px; width: auto; display: block; filter: drop-shadow(0 0 4px #ffffff) drop-shadow(0 0 2px #ffffff);">
                </td>
                <!-- Text Content -->
                <td align="left" style="vertical-align: middle;">
                    <h2
                        style="color: #f8fafc !important; margin: 0 !important; font-size: 22px !important; font-weight: 800 !important; text-transform: uppercase !important; letter-spacing: 2px !important; line-height: 1 !important;">
                        CSIT Capstone Library
                    </h2>
                    <p
                        style="color: #38bdf8 !important; font-size: 10px !important; font-weight: 800 !important; text-transform: uppercase !important; margin: 6px 0 0 0 !important; letter-spacing: 1.5px !important; line-height: 1 !important;">
                        Official Institutional Archive
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>