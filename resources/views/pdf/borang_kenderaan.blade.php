<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Borang Kebenaran Menggunakan Kenderaan Pejabat - {{ $booking->booking_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 15mm 14mm 15mm;
        }
        body {
            font-family: "Times New Roman", "DejaVu Serif", serif;
            font-size: 11px;
            color: #111111;
            line-height: 1.38;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .emblem {
            margin: 0 auto 4px auto;
            width: 48px;
            height: 48px;
        }
        .org-title {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.4px;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .form-title {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin: 2px 0 4px 0;
            text-transform: uppercase;
        }
        .ref-code {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            color: #444444;
        }
        table {
            border-collapse: collapse;
        }
        .layout-table {
            width: 100%;
            table-layout: fixed;
        }
        .layout-table > tbody > tr > td {
            vertical-align: top;
        }
        .left-col {
            width: 55%;
            padding-right: 12px;
        }
        .right-col {
            width: 45%;
        }
        .applicant-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .applicant-table td {
            padding: 2.5px 2px;
            vertical-align: top;
        }
        .applicant-label {
            width: 68px;
            font-weight: normal;
        }
        .applicant-colon {
            width: 10px;
        }
        .applicant-val {
            border-bottom: 1px dotted #555555;
            font-weight: bold;
        }
        .trip-table {
            width: 100%;
            border: 1px solid #222222;
            margin-bottom: 12px;
        }
        .trip-table th,
        .trip-table td {
            border: 1px solid #222222;
            padding: 6px 7px;
            vertical-align: top;
        }
        .trip-header {
            text-align: center;
            font-weight: bold;
            background-color: #f4f5f7;
            letter-spacing: 0.4px;
        }
        .trip-subhead {
            text-align: center;
            font-weight: bold;
            background-color: #fafafa;
        }
        .trip-body-cell {
            height: 295px;
        }
        .ceo-notice {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 7px 8px;
            background-color: #fafafa;
        }
        .declaration {
            margin: 12px 0 38px 0;
            text-align: justify;
        }
        .sig-table {
            width: 100%;
        }
        .sig-table td {
            padding: 4px 2px;
        }
        .sig-line {
            border-bottom: 1px dotted #333333;
        }
        .admin-box {
            border: 1px solid #222222;
            padding: 0;
        }
        .admin-box-title {
            text-align: center;
            font-weight: bold;
            padding: 6px 4px;
            border-bottom: 1px solid #222222;
            background-color: #f4f5f7;
            letter-spacing: 0.3px;
        }
        .admin-section {
            padding: 10px 10px 8px 10px;
        }
        .chk-row {
            width: 100%;
            margin-bottom: 6px;
        }
        .chk-row td {
            vertical-align: middle;
            padding: 2px 0;
        }
        .chk-box {
            display: inline-block;
            width: 24px;
            height: 15px;
            border: 1px solid #222222;
            text-align: center;
            line-height: 14px;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            font-weight: bold;
        }
        .indent-cell {
            width: 26px;
        }
        .remark-lines {
            margin: 4px 0 8px 34px;
            font-size: 10px;
        }
        .remark-line {
            border-bottom: 1px solid #555555;
            min-height: 15px;
            padding-top: 2px;
        }
        .admin-meta {
            width: 100%;
            margin-top: 16px;
            margin-bottom: 6px;
        }
        .admin-meta td {
            padding: 3px 2px;
            vertical-align: bottom;
        }
        .tick-note {
            font-size: 9.5px;
            font-style: italic;
            margin-top: 4px;
            margin-bottom: 12px;
        }
        .vehicle-section-title {
            text-align: center;
            font-weight: bold;
            padding: 6px 0 8px 0;
            border-top: 1px dashed #666666;
            letter-spacing: 0.3px;
        }
        .vehicle-table {
            width: 100%;
        }
        .vehicle-table td {
            padding: 5px 2px;
            vertical-align: bottom;
        }
        .vehicle-label {
            width: 92px;
        }
        .footer-note {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #cccccc;
            font-size: 10.5px;
        }
        .urgent-tag {
            display: inline-block;
            border: 1px solid #991b1b;
            color: #991b1b;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8.5px;
            font-weight: bold;
            padding: 1px 4px;
            margin-top: 4px;
        }
        .meta-small {
            font-size: 9.5px;
            color: #333333;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    @php
        $isApprovedOrCompleted = in_array($booking->status, [\App\Models\Booking::STATUS_APPROVED, \App\Models\Booking::STATUS_COMPLETED], true);
        $isRejected = $booking->status === \App\Models\Booking::STATUS_REJECTED;
        $driverName = $booking->pilot
            ? $booking->pilot->name
            : ($booking->isSelfDrive() ? $booking->applicant->name . ' (Pandu Sendiri)' : '-');
        $emblemPath = public_path('images/ykn-emblem.png');
        $emblemDataUri = file_exists($emblemPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($emblemPath))
            : null;
    @endphp

    <div class="header">
        <div class="emblem">
            @if($emblemDataUri)
                <img src="{{ $emblemDataUri }}" alt="Logo YKN" style="width: 48px; height: 48px;">
            @endif
        </div>
        <div class="org-title">YAYASAN KEBAJIKAN NEGARA MALAYSIA</div>
        <div class="form-title">BORANG KEBENARAN MENGGUNAKAN KENDERAAN PEJABAT</div>
        <div class="ref-code">No. Rujukan: {{ $booking->booking_code }} &nbsp;|&nbsp; Jenis: {{ $booking->requestTypeLabel() }}</div>
    </div>

    <table class="layout-table">
        <tr>
            {{-- LEFT COLUMN: Applicant Info & Trip Details --}}
            <td class="left-col">
                <table class="applicant-table">
                    <tr>
                        <td class="applicant-label">Nama</td>
                        <td class="applicant-colon">:</td>
                        <td class="applicant-val">{{ $booking->applicant->name }}</td>
                    </tr>
                    <tr>
                        <td class="applicant-label">Jawatan</td>
                        <td class="applicant-colon">:</td>
                        <td class="applicant-val">{{ $booking->applicant->jawatan }}</td>
                    </tr>
                    <tr>
                        <td class="applicant-label">Unit</td>
                        <td class="applicant-colon">:</td>
                        <td class="applicant-val">{{ $booking->applicant->unit }}</td>
                    </tr>
                </table>

                <table class="trip-table">
                    <tr>
                        <th colspan="2" class="trip-header">MAKLUMAT PERJALANAN</th>
                    </tr>
                    <tr>
                        <th class="trip-subhead" style="width: 34%;">Tarikh</th>
                        <th class="trip-subhead" style="width: 66%;">Tujuan</th>
                    </tr>
                    <tr>
                        <td class="trip-body-cell">
                            <strong>Mula:</strong><br>
                            {{ $booking->start_datetime->format('d/m/Y') }}<br>
                            {{ $booking->start_datetime->format('h:i A') }}
                            <br><br>
                            <strong>Tamat:</strong><br>
                            {{ $booking->end_datetime->format('d/m/Y') }}<br>
                            {{ $booking->end_datetime->format('h:i A') }}

                            @if($booking->is_urgent)
                                <br><br>
                                <span class="urgent-tag">SEGERA (&lt; 3 HARI)</span>
                            @endif
                        </td>
                        <td class="trip-body-cell">
                            <strong>Destinasi:</strong><br>
                            {{ $booking->destination }}
                            <br><br>
                            <strong>Tujuan Rasmi:</strong><br>
                            {!! nl2br(e($booking->purpose)) !!}

                            <div class="meta-small">
                                <br>
                                <strong>Kategori Permohonan:</strong> {{ $booking->requestTypeLabel() }}
                                @if($booking->isSelfDrive() && $booking->driver_license_no)
                                    <br><strong>No. Lesen Memandu:</strong> {{ $booking->driver_license_no }}
                                @endif
                                @if($booking->pilot_remarks)
                                    <br><br><strong>Catatan Pilot / Perjalanan:</strong><br>
                                    {{ $booking->pilot_remarks }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="ceo-notice">
                            Ketua Pegawai Eksekutif berhak menarik balik kelulusan yang telah diberikan pada bila-bila masa
                        </td>
                    </tr>
                </table>

                <div class="declaration">
                    Saya mohon kebenaran menggunakan kenderaan pejabat bagi tujuan seperti yang dinyatakan di atas.
                </div>

                <table class="sig-table">
                    <tr>
                        <td style="width: 75px;">Tandatangan</td>
                        <td style="width: 10px;">:</td>
                        <td class="sig-line">{{ $booking->applicant->name }}</td>
                    </tr>
                    <tr>
                        <td>Tarikh</td>
                        <td>:</td>
                        <td class="sig-line">{{ $booking->created_at->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </td>

            {{-- RIGHT COLUMN: KEGUNAAN UNIT PENTADBIRAN & MAKLUMAT KENDERAAN --}}
            <td class="right-col">
                <div class="admin-box">
                    <div class="admin-box-title">KEGUNAAN UNIT PENTADBIRAN</div>

                    <div class="admin-section">
                        <table class="chk-row">
                            <tr>
                                <td style="width: 32px;">
                                    <span class="chk-box">{{ $isApprovedOrCompleted ? 'X' : '' }}</span>
                                </td>
                                <td><strong>Permohonan diluluskan</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="chk-box">{{ $isRejected ? 'X' : '' }}</span>
                                </td>
                                <td><strong>Permohonan tidak dapat dipenuhi</strong></td>
                            </tr>
                        </table>

                        <table class="chk-row" style="margin-top: 8px;">
                            <tr>
                                <td class="indent-cell"></td>
                                <td style="width: 32px;">
                                    <span class="chk-box">{{ $isRejected && $booking->rejection_reason === \App\Models\Booking::REJECTION_ALL_IN_USE ? 'X' : '' }}</span>
                                </td>
                                <td>Kesemua kenderaan digunakan</td>
                            </tr>
                            <tr>
                                <td class="indent-cell"></td>
                                <td>
                                    <span class="chk-box">{{ $isRejected && $booking->rejection_reason === \App\Models\Booking::REJECTION_ALREADY_BOOKED ? 'X' : '' }}</span>
                                </td>
                                <td>Kenderaan telah ditempah</td>
                            </tr>
                            <tr>
                                <td class="indent-cell"></td>
                                <td>
                                    <span class="chk-box">{{ $isRejected && $booking->rejection_reason === \App\Models\Booking::REJECTION_MAINTENANCE ? 'X' : '' }}</span>
                                </td>
                                <td>Kenderaan sedang diselenggara</td>
                            </tr>
                            <tr>
                                <td class="indent-cell"></td>
                                <td>
                                    <span class="chk-box">{{ $isRejected && $booking->rejection_reason === \App\Models\Booking::REJECTION_OTHER ? 'X' : '' }}</span>
                                </td>
                                <td>Lain-lain</td>
                            </tr>
                        </table>

                        <div class="remark-lines">
                            <div class="remark-line">{{ $isRejected ? ($booking->rejection_remarks ?? '') : '' }}</div>
                            <div class="remark-line">&nbsp;</div>
                        </div>

                        <table class="admin-meta">
                            <tr>
                                <td style="width: 72px;">Tandatangan</td>
                                <td style="width: 10px;">:</td>
                                <td class="sig-line">{{ $booking->approver?->name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Tarikh</td>
                                <td>:</td>
                                <td class="sig-line">{{ $booking->approved_at?->format('d/m/Y') ?? '' }}</td>
                            </tr>
                        </table>

                        <div class="tick-note">Tandakan (X) diruang berkaitan</div>

                        <div class="vehicle-section-title">MAKLUMAT KENDERAAN</div>

                        <table class="vehicle-table">
                            <tr>
                                <td class="vehicle-label">No. Kenderaan</td>
                                <td style="width: 10px;">:</td>
                                <td class="sig-line">
                                    @if($booking->vehicle)
                                        <strong>{{ $booking->vehicle->plate_number }}</strong> ({{ $booking->vehicle->model }})
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="vehicle-label">Nama Pilot</td>
                                <td>:</td>
                                <td class="sig-line">{{ $driverName }}</td>
                            </tr>
                            <tr>
                                <td class="vehicle-label">Odometer Mula</td>
                                <td>:</td>
                                <td class="sig-line">
                                    {{ $booking->odometer_start !== null ? number_format($booking->odometer_start) . ' km' : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="vehicle-label">Odometer Tamat</td>
                                <td>:</td>
                                <td class="sig-line">
                                    @if($booking->odometer_end !== null)
                                        {{ number_format($booking->odometer_end) }} km
                                        @if($booking->totalDistance() !== null)
                                            ({{ number_format($booking->totalDistance()) }} km)
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="vehicle-label" style="padding-top: 20px;">Tandatangan</td>
                                <td style="padding-top: 20px;">:</td>
                                <td class="sig-line" style="padding-top: 20px;">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        <strong>Nota :</strong> Bagi mengelakkan sebarang kesulitan permohonan hendaklah dibuat selewat-lewatnya 3 hari daripada tarikh penggunaannya.
    </div>
</body>
</html>
