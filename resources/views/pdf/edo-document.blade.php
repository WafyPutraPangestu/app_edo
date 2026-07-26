<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>e-Delivery Order - {{ $document->releaseRequest->awb_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .logo-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .sub-title {
            font-size: 12px;
            color: #666;
        }

        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            text-decoration: underline;
        }

        .table-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-info td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .table-info td.label {
            font-weight: bold;
            width: 30%;
            background-color: #f9f9f9;
        }

        .qr-section {
            margin-top: 30px;
            text-align: right;
        }

        .qr-code {
            border: 2px solid #333;
            padding: 10px;
            display: inline-block;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="logo-title">PT. NCS LINE WORLD WIDE</div>
        <div class="sub-title">Electronic Cargo Release System • Bandara Internasional Soekarno-Hatta</div>
    </div>

    <div class="doc-title">ELECTRONIC DELIVERY ORDER (e-DO)</div>

    <p style="margin-bottom: 20px;">
        Kepada Yth. Petugas Gudang Bandara,<br>
        Bersama dokumen ini, kami memberikan otorisasi untuk pelepasan kargo (Release of Goods) sesuai dengan rincian
        berikut:
    </p>

    <table class="table-info">
        <tr>
            <td class="label">Nomor AWB (Air Waybill)</td>
            <td style="font-size: 16px; font-weight: bold;">{{ $document->releaseRequest->awb_number }}</td>
        </tr>
        <tr>
            <td class="label">Penerbangan (Flight)</td>
            <td>{{ $document->releaseRequest->flight_number }}</td>
        </tr>
        <tr>
            <td class="label">Rute Pengiriman</td>
            <td>{{ $document->releaseRequest->origin }} &nbsp; ➔ &nbsp; {{ $document->releaseRequest->destination }}
            </td>
        </tr>
        <tr>
            <td class="label">Kuantitas (Qty)</td>
            <td>{{ $document->releaseRequest->quantity }}</td>
        </tr>
        <tr>
            <td class="label">Berat Kotor (Gross Weight)</td>
            <td>{{ $document->releaseRequest->gross_weight }}</td>
        </tr>
        <tr>
            <td class="label">Deskripsi Barang</td>
            <td>{{ $document->releaseRequest->goods_description }}</td>
        </tr>
        <tr>
            <td class="label">Importir / Pemilik Kargo</td>
            <td>
                <strong>{{ $document->releaseRequest->user->name }}</strong><br>
                {{ $document->releaseRequest->user->company_name }}
            </td>
        </tr>
    </table>

    <div class="qr-section">
        <p style="margin-bottom: 5px; font-weight: bold;">Tanda Tangan Elektronik Sah</p>
        <div class="qr-code">
            {{-- Render QR Code menjadi format Base64 PNG agar aman dibaca oleh DomPDF --}}
            <img src="data:image/svg+xml;base64, {!! base64_encode(
                QrCode::format('svg')->size(120)->margin(0)->generate(route('verify.edo', $document->qr_code_string)),
            ) !!}" alt="QR Code e-DO">
        </div>
        <p style="font-size: 10px; margin-top: 5px; color: #555;">ID Dok: {{ $document->qr_code_string }}</p>
        <p style="font-size: 10px; margin-top: 2px; color: #555;">Diterbitkan:
            {{ \Carbon\Carbon::parse($document->issued_date)->translatedFormat('d F Y, H:i') }}</p>
    </div>

    <div class="footer">
        Dokumen ini diterbitkan secara elektronik oleh sistem PT NCS Line World Wide dan tidak memerlukan tanda tangan
        basah.<br>
        Keaslian dokumen ini dapat diverifikasi dengan memindai QR Code di atas menggunakan kamera ponsel.
    </div>

</body>

</html>
