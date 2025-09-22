<link rel="stylesheet" href="{{ asset('css/main/cta.css') }}">

<body>
  <section class="promo-section">
    <div class="promo-text">
      <h2>
        Gunakan layanan secara rutin dan <br>
        dapatkan free-privileges<br>
        <span>atau</span>
      </h2>
      <button onclick="window.location.href = '{{ route('membership.index') }}'" class="membership-btn">Beli Membership</button>
    </div>
    <div class="cards">
      <img src="images/cta_cards.svg" alt="cards" class="cta-cards">
    </div>
  </section>
</body>