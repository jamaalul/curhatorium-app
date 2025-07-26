<link rel="stylesheet" href="{{ asset('css/main/qotd.css') }}">

<body>
  <div class="quote-section">
    <h2>Quotes of The Day</h2>
    <div class="quote-carousel">
      <div class="quote-container">
        <div class="quote-slide active">
          <div class="quote-box">
            <p class="quote-text" id="quote-1"></p>
          </div>
        </div>
        <div class="quote-slide">
          <div class="quote-box">
            <p class="quote-text" id="quote-2"></p>
          </div>
        </div>
        <div class="quote-slide">
          <div class="quote-box">
            <p class="quote-text" id="quote-3"></p>
          </div>
        </div>
      </div>
      <div class="carousel-indicators">
        <span class="indicator active" data-slide="0"></span>
        <span class="indicator" data-slide="1"></span>
        <span class="indicator" data-slide="2"></span>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Show loading state
      const quoteElements = document.querySelectorAll('.quote-text');
      quoteElements.forEach(element => {
        element.textContent = 'Loading...';
      });

      // Fetch quotes from the API
      fetch('/quote/today')
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to fetch quotes');
          }
          return response.json();
        })
        .then(quotes => {
          if (quotes && quotes.length > 0) {
            quotes.forEach((quote, index) => {
              const quoteElement = document.getElementById(`quote-${index + 1}`);
              if (quoteElement && quote.quote) {
                quoteElement.textContent = quote.quote;
              }
            });
          } else {
            // Show fallback message if no quotes
            quoteElements.forEach(element => {
              element.textContent = 'No quotes available today.';
            });
          }
        })
        .catch(error => {
          console.error('Error fetching quotes:', error);
          // Show error message
          quoteElements.forEach(element => {
            element.textContent = 'Unable to load quotes. Please try again later.';
          });
        });

      // Carousel functionality
      let currentSlide = 0;
      const slides = document.querySelectorAll('.quote-slide');
      const indicators = document.querySelectorAll('.indicator');
      const totalSlides = slides.length;

      function showSlide(index) {
        // Remove all classes from slides
        slides.forEach(slide => {
          slide.classList.remove('active', 'prev', 'next');
        });
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        // Add appropriate classes to slides
        slides[index].classList.add('active');
        
        // Add prev/next classes for smooth transitions
        const prevIndex = (index - 1 + totalSlides) % totalSlides;
        const nextIndex = (index + 1) % totalSlides;
        
        slides[prevIndex].classList.add('prev');
        slides[nextIndex].classList.add('next');
        
        // Show current indicator
        indicators[index].classList.add('active');
      }

      function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
      }

      // Initialize the carousel
      showSlide(0);

      // Auto-slide every 5 seconds
      setInterval(nextSlide, 5000);

      // Manual navigation with indicators
      indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
          currentSlide = index;
          showSlide(currentSlide);
        });
      });
    });
  </script>
</body>