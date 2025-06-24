<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keluar - {{ $suratKeluar->perihal }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h3 { margin: 0; }
        .header p { margin: 0; }
        .content { margin-top: 20px; }
        .signature { margin-top: 50px; text-align: right; }
        .signature p { margin: 0; }
        .footer { text-align: center; margin-top: 50px; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>KOP SURAT</h3>
            <p>UNIVESITAS BAHAUDIN MUDHARY MADURA</p>
            <p>Jln Raya Lenteng, Aredake, Batuan, Kec. Batuan, Kabupaten Sumenep, Jawa Timur 69451</p>
            <hr style="border: 1px solid black;">
        </div>

        <p style="text-align: right;">UNIBA MADURA, {{ \Carbon\Carbon::parse($suratKeluar->tanggal_surat)->format('d F Y') }}</p>
        <p>Nomor: {{ $suratKeluar->nomor_surat }}</p>
        <p>Sifat: {{ $suratKeluar->sifat->nama_sifat ?? 'Biasa' }}</p>
        <p>Perihal: {{ $suratKeluar->perihal }}</p>
        <br>
        <p>Yth. {{ $suratKeluar->penerima_id }}</p>
        <p>Di tempat</p>
        <br>

        <div class="content">
            {{-- Isi surat bisa berupa teks biasa atau HTML jika perlu formatting --}}
            <pre style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; white-space: pre-wrap;">{{ $suratKeluar->isi_surat }}</pre>
        </div>

        <div class="signature">
            <p>Hormat kami,</p>
            <br><br><br> {{-- Ruang untuk tanda tangan --}}
            <p>(Nama Pimpinan)</p>
            <p>Jabatan Pimpinan</p>
        </div>

        <div class="footer">
            <p>Telp: (021)xxxxxx | Email: info@kampus.com | Website: kampus.com</p>
        </div>
    </div>
</body>
</html>