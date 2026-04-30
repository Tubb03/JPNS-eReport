<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports Export PDF</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
        }
        .header {
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header td {
            vertical-align: bottom;
        }
        .logo {
            height: 50px;
        }
        .title {
            color: #1e3a8a;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 2px 0;
        }
        .subtitle {
            color: #6b7280;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .unit-label {
            color: #2563eb;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px 0;
            text-align: right;
        }
        .program-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin: 0;
            text-align: right;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0 5px 0;
        }
        .objective {
            font-size: 12px;
            color: #334155;
            border-left: 3px solid #bfdbfe;
            padding-left: 10px;
            font-style: italic;
            margin: 0 0 15px 0;
        }
        .full-report {
            text-align: justify;
            white-space: pre-wrap;
            margin-bottom: 20px;
        }
        .gallery {
            width: 100%;
            margin-bottom: 20px;
        }
        .gallery td {
            padding: 5px;
            vertical-align: middle;
            text-align: center;
        }
        .gallery img {
            max-width: 100%;
            max-height: 150px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .footer {
            margin-top: 30px;
            border-top: 2px solid #f1f5f9;
            padding-top: 15px;
        }
        .signature-line {
            border-bottom: 1px solid #cbd5e1;
            width: 150px;
            margin-bottom: 5px;
            height: 20px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach($reports as $index => $report)
        <table width="100%" class="header" cellspacing="0" cellpadding="0">
            <tr>
                <td width="60px">
                    <img src="{{ public_path('KPM Logo.png') }}" class="logo">
                </td>
                <td>
                    <h1 class="title">One Page Report</h1>
                    <p class="subtitle">Sektor Sumber dan Teknologi Pendidikan</p>
                </td>
                <td align="right" width="40%">
                    <p class="unit-label">{{ $report->unit }}</p>
                    <div class="program-name">{{ $report->program_name ?? $report->program }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Program Objective</div>
        <div class="objective">{{ $report->objective ?? 'N/A' }}</div>

        <div class="section-title">Photographic Evidence</div>
        <table class="gallery" cellspacing="0" cellpadding="0">
            <tr>
                @foreach($report->images->take(4) as $image)
                    @php
                        $imgPath = public_path(str_replace('/storage/', 'storage/', $image->image_path));
                    @endphp
                    <td width="{{ 100 / max(min(count($report->images), 4), 1) }}%">
                        @if(file_exists($imgPath))
                            <img src="{{ $imgPath }}">
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>

        <div class="section-title">Detailed Report</div>
        <div class="full-report">{{ $report->full_report ?? 'N/A' }}</div>

        <table width="100%" class="footer" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div style="font-size: 9px; color: #94a3b8; font-weight: bold; text-transform: uppercase;">Reported By</div>
                    <div style="font-size: 12px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $report->user->name ?? $report->name }}</div>
                </td>
                <td align="right">
                    <div style="font-size: 9px; color: #94a3b8; font-weight: bold; text-transform: uppercase;">Date of Program</div>
                    <div style="font-size: 12px; font-weight: bold; color: #1e293b; margin-top: 5px;">{{ $report->program_date ?? $report->date }}</div>
                </td>
            </tr>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
