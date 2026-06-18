@extends('layouts.dashboard')

@section('title', 'Video Session')

@section('head')
  <script src='https://8x8.vc/vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/external_api.js' async></script>
  <style>
    html,
    body,
    #jaas-container {
      height: 100%;
    }
  </style>
  <script type="text/javascript">
    window.onload = () => {
      const api = new JitsiMeetExternalAPI("8x8.vc", {
        roomName: "vpaas-magic-cookie-ac9ee141fc8a4c308ac24f5ec225af3f/{{ $room }}",
        parentNode: document.querySelector('#jaas-container'),
      });
    }
  </script>
@endsection

@section('dashboard-content')
  {{-- <div id="timer-container" style="position:fixed;bottom:32px;right:32px;z-index:9999;">
    <div id="session-timer"
      style="background:#222;color:#fff;padding:12px 28px;border-radius:16px;box-shadow:0 2px 12px #0002;display:flex;flex-direction:column;align-items:center;min-width:110px;font-family:'FigtreeReg', Figtree, Arial,sans-serif;">
      <span id="timer-label"
        style="font-size:0.85em;color:#9acbd0;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;letter-spacing:0.5px;">Waiting:
      </span>
      <span id="timer-value"
        style="font-size:1.5em;font-family:'Courier New',monospace;font-weight:700;letter-spacing:1px;">05:00</span>
    </div>
    <div id="warning-message"
      style="background:#f59e42;color:#fff;padding:8px 12px;border-radius:8px;font-size:0.75rem;margin-top:8px;text-align:center;font-family:'FigtreeBold', Figtree, Arial,sans-serif;font-weight:600;box-shadow:0 2px 8px #0002;">
      ⚠️ Don't leave this page until session is cancelled
    </div>
  </div> --}}
  <div id="jaas-container" />
  <meta name="session-id" content="{{ $room }}">

@endsection

@section('scripts')
  <script src="/js/modules/video-session.js"></script>
@endsection