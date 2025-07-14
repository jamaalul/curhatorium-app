<script src='https://8x8.vc/vpaas-magic-cookie-96f0941768964ab380ed0fbada7a502f/external_api.js'></script>
.....
<script type="text/javascript">
  let api;

  const initIframeAPI = () => {
    const domain = '8x8.vc';
    const options = {
      roomName: 'vpaas-magic-cookie-1fc542a3e4414a44b2611668195e2bfe/ExampleRoom',
    //   jwt: 'eyJraWQiOiJ2cGFhcy1tYWdpYy1jb29raWUtOTZmMDk0MTc2ODk2NGFiMzgwZWQwZmJhZGE3YTUwMmYvMGQ4ODVjLVNBTVBMRV9BUFAiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJqaXRzaSIsImV4cCI6MTYxNDAwNTI4OCwibmJmIjoxNjEzOTk4MDgzLCJpc3MiOiJjaGF0Iiwicm9vbSI6IioiLCJzdWIiOiJ2cGFhcy1tYWdpYy1jb29raWUtOTZmMDk0MTc2ODk2NGFiMzgwZWQwZmJhZGE3YTUwMmYiLCJjb250ZXh0Ijp7ImZlYXR1cmVzIjp7ImxpdmVzdHJlYW1pbmciOmZhbHNlLCJvdXRib3VuZC1jYWxsIjpmYWxzZSwidHJhbnNjcmlwdGlvbiI6ZmFsc2UsInJlY29yZGluZyI6ZmFsc2V9LCJ1c2VyIjp7Im1vZGVyYXRvciI6dHJ1ZSwibmFtZSI6IlRlc3QgVXNlciIsImlkIjoiYXV0aDB8NWY5MDNkN2E3N2YzYjQwMDZlYjhlNjdkIiwiYXZhdGFyIjoiIiwiZW1haWwiOiJ0ZXN0LnVzZXJAY29tcGFueS5jb20ifX19.XZTZVSVeFgGNim8YZKLwt37mcc8xkf3oSjuR28KeW8If1Xq5XI7w7K2GnsqZjF0S4XbmZzsswmfh2m9UI7Od_p3USv95Xq6gRjS6KUed5neXTs1k8rtKEtvRjHMpMPTanckTm4ol8GYi0z8Rwq7FQqRr9D8LYXWqNW7sA9pG16GXrhQMBPWEYm4usxZe5QP36PnoV-15xZ6leQ7KF3woRScxPcPb7L81bACsT0GjBzIBg_dEMpLG0ckRl4w1LW8YfnYUrbmLK4gE5FwlD8hjJOBW4z_Tm_KGu8-gYE1zzb5KlOCeVGVcik2dUEP7U4zy20iDaBXLIDoD-ayZBKkwiw',
      parentNode: document.querySelector('#meet')
    };
    api = new JitsiMeetExternalAPI(domain, options);
  }

  window.onload = () => {
    initIframeAPI();
  }
</script>
...
<body><div id="meet" /></body>