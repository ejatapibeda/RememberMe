<!DOCTYPE html>
<html>
<head>
    <title>RememberME - Pengingat Tugas</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        
        <h1 style="color: #1d4ed8; margin-bottom: 10px;">
            Halo, {{ $tugas->user->name }} 
        </h1>

        {{-- Pesan berdasarkan jenis reminder --}}
        @if($jenis === '1_jam')
            <p>Tugas Anda akan jatuh tempo <b>1 jam lagi</b>.</p>
        @elseif($jenis === '5_menit')
            <p>Perhatian! Tugas Anda akan jatuh tempo <b>5 menit lagi</b>.</p>
        @elseif($jenis === 'deadline')
            <p style="color: #dc2626; font-weight: bold;">
                Deadline tugas telah tiba sekarang!
            </p>
        @endif

        <div style="background-color: #eff6ff; padding: 20px; border-radius: 12px; margin: 20px 0;">
            <h2 style="margin: 0; color: #1e40af;">
                {{ $tugas->tugas }}
            </h2>
            <p style="margin: 8px 0;">
                <b>Deadline:</b> {{ \Carbon\Carbon::parse($tugas->tanggal)->format('d M Y H:i') }}
            </p>
        </div>

        <p>Ayo segera selesaikan tugasmu agar tidak terlambat 🚀</p>

        <p style="margin-top: 30px;">
            Salam,<br>
            <strong>RememberME Team</strong>
        </p>

    </div>

</body>
</html>
