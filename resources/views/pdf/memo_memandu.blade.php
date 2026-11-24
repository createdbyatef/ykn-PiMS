<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Memo Kebenaran Memandu Kenderaan Pejabat - {{ $booking->booking_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 22mm 20mm 22mm;
        }
        body {
            font-family: "Times New Roman", "DejaVu Serif", serif;
            font-size: 12.5px;
            color: #111111;
            line-height: 1.65;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .emblem {
            margin: 0 auto 6px auto;
            width: 56px;
            height: 56px;
        }
        .org-title {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .memo-title {
            font-size: 13.5px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .rule {
            border: none;
            border-top: 1.2px solid #222222;
            margin: 12px 0 18px 0;
        }
        .memo-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .memo-meta td {
            padding: 4px 2px;
            vertical-align: top;
        }
        .meta-label {
            width: 95px;
        }
        .meta-colon {
            width: 16px;
        }
        .dotted-row {
            border-bottom: 1px dotted #444444;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .perkara-val {
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .body-content {
            margin-top: 18px;
            text-align: justify;
        }
        .body-content p {
            margin: 0 0 16px 0;
        }
        .details-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 10px 14px;
            margin: 14px 0 20px 0;
            font-size: 11.5px;
        }
        .details-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-box td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .closing {
            margin-top: 24px;
            margin-bottom: 58px;
        }
        .sig-block {
            width: 260px;
            margin-bottom: 48px;
        }
        .sig-dots {
            border-bottom: 1px dotted #222222;
            height: 22px;
            margin-bottom: 6px;
        }
        .sig-caption {
            font-size: 11.5px;
            line-height: 1.4;
        }
        .footer-note {
            text-align: center;
            font-style: italic;
            font-size: 11.5px;
            margin-top: 32px;
            margin-bottom: 22px;
        }
        .bottom-rule {
            border: none;
            border-top: 1px solid #222222;
            margin-top: 16px;
        }
        .ref-footer {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            color: #555555;
            text-align: right;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    @php
        $vehicleLabel = $booking->vehicle
            ? $booking->vehicle->plate_number . ' - ' . $booking->vehicle->model
            : 'Kenderaan Pejabat YKN';
        $startFormatted = $booking->start_datetime->format('d/m/Y (h:i A)');
        $endFormatted = $booking->end_datetime->format('d/m/Y (h:i A)');
        $emblemPath = public_path('images/ykn-emblem.png');
        $emblemDataUri = file_exists($emblemPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($emblemPath))
            : null;
    @endphp

    <div class="header">
        <div class="emblem">
            @if($emblemDataUri)
                <img src="{{ $emblemDataUri }}" alt="Logo YKN" style="width: 56px; height: 56px;">
            @endif
        </div>
        <div class="org-title">YAYASAN KEBAJIKAN NEGARA MALAYSIA</div>
        <div class="memo-title">MEMO</div>
    </div>

    <hr class="rule">

    <table class="memo-meta">
        <tr>
            <td class="meta-label">Kepada</td>
            <td class="meta-colon">:</td>
            <td>
                <div class="dotted-row"><strong>{{ $booking->applicant->name }}</strong></div>
                <div class="dotted-row">{{ $booking->applicant->jawatan }}</div>
                <div class="dotted-row">{{ $booking->applicant->unit }}</div>
            </td>
        </tr>
        <tr>
            <td class="meta-label" style="padding-top: 12px;">Daripada</td>
            <td class="meta-colon" style="padding-top: 12px;">:</td>
            <td style="padding-top: 12px;">Unit Pentadbiran</td>
        </tr>
        <tr>
            <td class="meta-label">Salinan</td>
            <td class="meta-colon">:</td>
            <td>Ketua Pegawai Eksekutif YKN</td>
        </tr>
        <tr>
            <td class="meta-label">Tarikh</td>
            <td class="meta-colon">:</td>
            <td>{{ $booking->approved_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Perkara</td>
            <td class="meta-colon">:</td>
            <td class="perkara-val">KEBENARAN MEMANDU KENDERAAN PEJABAT</td>
        </tr>
    </table>

    <hr class="rule">

    <div class="body-content">
        <p>Perkara di atas dirujuk.</p>

        <p>
            Sehubungan itu, tuan/puan diberi kebenaran memandu kenderaan pejabat
            (<strong>{{ $vehicleLabel }}</strong>)
            bagi tempoh <strong>{{ $startFormatted }}</strong> hingga <strong>{{ $endFormatted }}</strong>
            bagi menjayakan perlaksanaan <strong>{{ $booking->purpose }}</strong>
            di <strong>{{ $booking->destination }}</strong>.
            Adalah menjadi tanggungjawab tuan/puan memastikan kenderaan dijaga sebaiknya dengan selamat
            dan mematuhi semua peraturan yang sedang berkuatkuasa.
        </p>

        <div class="details-box">
            <table>
                <tr>
                    <td style="width: 165px;"><strong>No. Rujukan Tempahan</strong></td>
                    <td style="width: 12px;">:</td>
                    <td>{{ $booking->booking_code }}</td>
                </tr>
                <tr>
                    <td><strong>Kenderaan Dibenarkan</strong></td>
                    <td>:</td>
                    <td>{{ $vehicleLabel }} ({{ $booking->vehicle?->type ?? '-' }})</td>
                </tr>
                <tr>
                    <td><strong>No. Lesen Memandu</strong></td>
                    <td>:</td>
                    <td>{{ $booking->driver_license_no ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Odometer Semasa</strong></td>
                    <td>:</td>
                    <td>{{ $booking->vehicle ? number_format($booking->vehicle->current_odometer) . ' km' : '-' }}</td>
                </tr>
            </table>
        </div>

        <p class="closing">Sekian, terima kasih.</p>

        <div class="sig-block">
            <div class="sig-dots"></div>
            <div class="sig-caption">
                <strong>({{ $booking->approver?->name ?? 'Pegawai Unit Pentadbiran' }})</strong><br>
                {{ $booking->approver?->jawatan ?? 'Unit Pentadbiran' }}<br>
                Yayasan Kebajikan Negara Malaysia
            </div>
        </div>
    </div>

    <div class="footer-note">
        Nota :<br>
        Memo kebenaran ini hanya sah jika ditandatangani oleh Ketua Pegawai Eksekutif atau<br>
        Pegawai Unit Pentadbiran
    </div>

    <hr class="bottom-rule">
    <div class="ref-footer">Dijana secara digital melalui Sistem e-Pilot YKN &bull; {{ $booking->booking_code }}</div>
</body>
</html>
