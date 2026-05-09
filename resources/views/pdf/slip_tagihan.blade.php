<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Tagihan - {{ $tagihan->idTagihan }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #006767;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #006767;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 12px;
        }
        .info-box {
            background-color: #f9f9fc;
            border: 1px solid #e2e2e5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
            color: #555;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e2e5;
        }
        .details-table th {
            background-color: #f3f3f6;
            color: #333;
            font-weight: bold;
        }
        .details-table td.amount {
            text-align: right;
        }
        .total-row {
            background-color: #e6f4f1;
            font-weight: bold;
        }
        .total-row td {
            color: #006767;
            border-top: 2px solid #006767;
            border-bottom: none;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
        .status {
            text-align: center;
            margin-bottom: 20px;
        }
        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .badge.lunas {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge.belum {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIMADES</h1>
        <p>Sistem Informasi Manajemen Air Desa</p>
        <p>Struk Bukti Pembayaran Tagihan Air</p>
    </div>

    <div class="status">
        @if($tagihan->isSudahLunas())
            <div class="badge lunas">STATUS: LUNAS</div>
        @else
            <div class="badge belum">STATUS: BELUM BAYAR</div>
        @endif
    </div>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td>No. Tagihan</td>
                <td>: {{ $tagihan->idTagihan }}</td>
            </tr>
            <tr>
                <td>Nama Pelanggan</td>
                <td>: <strong>{{ $pelanggan->namaLengkap }}</strong></td>
            </tr>
            <tr>
                <td>ID Pelanggan</td>
                <td>: {{ $pelanggan->idPelanggan }}</td>
            </tr>
            <tr>
                <td>Alamat / RT RW</td>
                <td>: {{ $pelanggan->rt }}/{{ $pelanggan->rw }}</td>
            </tr>
            <tr>
                <td>Periode Tagihan</td>
                <td>: <strong>{{ $tagihan->periodeLabel() }}</strong></td>
            </tr>
        </table>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Keterangan Pemakaian</th>
                <th style="text-align: right;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Meteran Awal Bulan Sebelumnya</td>
                <td class="amount">{{ $tagihan->meterAwal }}</td>
            </tr>
            <tr>
                <td>Meteran Akhir Bulan Ini</td>
                <td class="amount">{{ $tagihan->meterAkhir }}</td>
            </tr>
            <tr style="background-color: #fafafa;">
                <td><strong>Total Pemakaian (m&sup3;)</strong></td>
                <td class="amount"><strong>{{ $tagihan->totalPemakaianM3 }} m&sup3;</strong></td>
            </tr>
        </tbody>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Rincian Biaya</th>
                <th style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Biaya Air ({{ $tagihan->totalPemakaianM3 }} m&sup3; × Rp {{ number_format($tarif->airPerM3, 0, ',', '.') }})</td>
                <td class="amount">Rp {{ number_format($tagihan->totalPemakaianM3 * $tarif->airPerM3, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Beban Sampah (Per Bulan)</td>
                <td class="amount">Rp {{ number_format($tarif->bebanSampah, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Kematian / Sosial</td>
                <td class="amount">Rp {{ number_format($tarif->danaKematian, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td style="font-size: 16px;">TOTAL TAGIHAN</td>
                <td class="amount" style="font-size: 16px;">Rp {{ number_format($tagihan->totalTagihan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <p>Terima kasih atas pembayaran tepat waktu Anda untuk mendukung operasional air desa.</p>
        <p><em>Simpan struk ini sebagai bukti pembayaran yang sah jika terjadi perbedaan pencatatan.</em></p>
    </div>
</body>
</html>
