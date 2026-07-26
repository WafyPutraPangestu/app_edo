<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $document->releaseRequest->awb_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 15px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0284c7;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Tabel Rincian */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f3f4f6;
            color: #333;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        /* Total */
        .total-wrapper {
            width: 40%;
            float: right;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .total-row {
            display: block;
            width: 100%;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .total-row.grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #0284c7;
        }

        .total-label {
            display: inline-block;
            width: 50%;
        }

        .total-amount {
            display: inline-block;
            width: 45%;
            text-align: right;
        }

        /* Stempel Lunas */
        .stamp-paid {
            position: absolute;
            top: 150px;
            left: 35%;
            font-size: 60px;
            font-weight: bold;
            color: rgba(22, 163, 74, 0.15);
            text-transform: uppercase;
            transform: rotate(-15deg);
            border: 5px solid rgba(22, 163, 74, 0.15);
            padding: 10px 20px;
            border-radius: 10px;
            z-index: -1;
        }

        .footer {
            margin-top: 150px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            clear: both;
        }
    </style>
</head>

<body>

    <!-- Cap Lunas Watermark -->
    <div class="stamp-paid">LUNAS</div>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company-name">PT. NCS LINE WORLD WIDE</div>
                    <div>Jl. Raya Bekasi Karawang KM 30.6</div>
                    <div>Jakarta, Indonesia 16455</div>
                    <div>Telp: (021) 8741528</div>
                </td>
                <td>
                    <div class="invoice-title">INVOICE</div>
                    <div style="text-align: right; margin-top: 5px;">
                        <strong>Nomor:</strong>
                        INV/{{ date('Y') }}/{{ str_pad($document->releaseRequest->transaction->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <strong>Tanggal:</strong>
                        {{ $document->releaseRequest->transaction->updated_at->format('d F Y') }}<br>
                        <strong>Status:</strong> <span style="color: #16a34a; font-weight:bold;">LUNAS (PAID)</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Tagihan Kepada (Bill To):</div>
                <div style="font-size: 14px; font-weight: bold; margin-bottom: 3px;">
                    {{ $document->releaseRequest->user->company_name ?? $document->releaseRequest->user->name }}</div>
                <div>UP: {{ $document->releaseRequest->user->name }}</div>
                <div>Email: {{ $document->releaseRequest->user->email }}</div>
            </td>
            <td>
                <div class="section-title">Referensi Kargo:</div>
                <div><strong>No. AWB:</strong> {{ $document->releaseRequest->awb_number }}</div>
                <div><strong>Rute:</strong> {{ $document->releaseRequest->origin }} ➔
                    {{ $document->releaseRequest->destination }}</div>
                <div><strong>Penerbangan:</strong> {{ $document->releaseRequest->flight_number }}</div>
                <div><strong>Kuantitas:</strong> {{ $document->releaseRequest->quantity }} | <strong>GW:</strong>
                    {{ $document->releaseRequest->gross_weight }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="55%">Deskripsi Layanan</th>
                <th width="20%" class="text-right">Biaya (Rp)</th>
                <th width="20%" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>Biaya Administrasi Dokumen (Local Charges)</strong><br>
                    <span style="font-size: 10px; color: #555;">Penanganan Release Dokumen Kargo via Sistem Elektronik
                        (e-DO)</span>
                </td>
                <td class="text-right">
                    {{ number_format($document->releaseRequest->transaction->total_amount, 0, ',', '.') }}</td>
                <td class="text-right">
                    {{ number_format($document->releaseRequest->transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-wrapper">
        <div class="total-row">
            <span class="total-label">Subtotal</span>
            <span
                class="total-amount">{{ number_format($document->releaseRequest->transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">TOTAL (IDR)</span>
            <span
                class="total-amount">{{ number_format($document->releaseRequest->transaction->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Invoice ini diterbitkan secara otomatis oleh sistem komputer PT. NCS Line World Wide dan tidak memerlukan
            tanda tangan basah.</p>
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.</p>
    </div>

</body>

</html>
