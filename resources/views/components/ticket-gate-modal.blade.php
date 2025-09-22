@php
    $isHour = $ticket->limit_type === 'hour';
    $isCount = $ticket->limit_type === 'count';
    $isDay = $ticket->limit_type === 'day';
    $showTotal = ($isCount || $isDay) && isset($total_remaining);
    $isSupportGroupJoin = request()->routeIs('group.join');
    $isChatbot = request()->routeIs('chatbot');
@endphp

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Tiket Diperlukan</title>
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/ticket-gate-modal.css') }}">
    <meta name="color-scheme" content="light only">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
</head>
<body class="ticket-modal-bg">
    <div class="ticket-modal-center">
        <div class="ticket-modal-box login-box">
            <h1 class="ticket-modal-title">Tiket Diperlukan</h1>
            <div style="margin-bottom:18px;">
                <p style="font-size:1.1em;margin-bottom:6px;">Fitur: <strong>{{ ucfirst(str_replace(['_', '-'], ' ', $ticketType)) }}</strong></p>
                <p style="font-size:1.1em;margin-bottom:6px;">Tipe Tiket: <strong>{{ ucfirst($ticket->limit_type) }}</strong></p>
                <p style="font-size:1.1em;margin-bottom:6px;">Sisa: <strong id="ticket-remaining">
                    @if($showTotal)
                        {{ $total_remaining }}
                    @else
                        {{ $ticket->limit_type === 'unlimited' ? 'Unlimited' : $ticket->remaining_value }}
                    @endif
                </strong></p>
                <p style="font-size:1.1em;margin-bottom:6px;">Expired: <strong>{{ \Carbon\Carbon::parse($ticket->expires_at)->format('d M Y H:i') }}</strong></p>
            </div>
            @if($isHour)
                @if($isChatbot)
                    <form method="GET" action="">
                        <label for="consume_amount" style="font-size:1em;">Masukkan waktu yang ingin digunakan (menit):</label>
                        <input type="number" step="1" min="1" max="{{ intval($ticket->remaining_value * 60) }}" name="consume_amount" id="consume_amount" required class="form-control" style="margin-bottom:1rem;">
                        <button type="submit" class="modal-btn">Gunakan</button>
                    </form>
                @else
                    <form method="POST" action="">
                        @csrf
                        <label for="consume_amount" style="font-size:1em;">Masukkan waktu yang ingin digunakan (menit):</label>
                        <input type="number" step="1" min="1" max="{{ intval($ticket->remaining_value * 60) }}" name="consume_amount" id="consume_amount" required class="form-control" style="margin-bottom:1rem;">
                        <button type="submit" class="modal-btn">Gunakan</button>
                    </form>
                @endif
            @elseif($isCount || $isDay)
                @if($isSupportGroupJoin)
                    <form method="POST" action="">
                        @csrf
                        <p style="margin-bottom:1rem;">Anda akan menggunakan 1 tiket untuk fitur ini.<br>Sisa tiket setelah ini: <strong>{{ $showTotal ? $total_remaining - 1 : $ticket->remaining_value - 1 }}</strong></p>
                        <button type="submit" class="modal-btn">Lanjutkan</button>
                    </form>
                @else
                    <form method="GET" action="">
                        <input type="hidden" name="redeem" value="1">
                        <p style="margin-bottom:1rem;">Anda akan menggunakan 1 tiket untuk fitur ini.<br>Sisa tiket setelah ini: <strong>{{ $showTotal ? $total_remaining - 1 : $ticket->remaining_value - 1 }}</strong></p>
                        <button type="submit" class="modal-btn">Lanjutkan</button>
                    </form>
                @endif
            @endif
            <form method="GET" action="/dashboard" style="margin-top:1rem;">
                <button type="submit" class="modal-btn cancel">Batal</button>
            </form>
        </div>
    </div>
</body>
</html>