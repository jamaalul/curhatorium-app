<section class="w-full h-fit px-4 flex flex-col items-center justify-center bg-white shadow-md">
  <div class="text-center px-6 py-10 w-full text-black">
    <h2 class="text-teal-500 text-2xl md:text-xl font-bold">Quotes of The Day</h2>
    <div class="relative max-w-4xl mx-auto mt-4">
      <div class="relative overflow-hidden min-h-24 h-full flex flex-col items-center justify-center">
        <div class="quote-slide absolute top-0 left-0 w-full opacity-0 transition-all duration-1000 ease-in-out transform invisible flex items-center justify-center">
          <div class="mx-auto border-y-4 border-stone-200 max-w-full md:max-w-3xl">
            <p class="text-xl md:text-3xl italic text-black font-serif my-2" id="quote-1"></p>
          </div>
        </div>
        <div class="quote-slide absolute top-0 left-0 w-full opacity-0 transition-all duration-1000 ease-in-out transform invisible flex items-center justify-center">
          <div class="mx-auto border-y-4 border-stone-200 max-w-full md:max-w-3xl">
            <p class="text-xl md:text-3xl italic text-black font-serif my-2" id="quote-2"></p>
          </div>
        </div>
        <div class="quote-slide absolute top-0 left-0 w-full opacity-0 transition-all duration-1000 ease-in-out transform invisible flex items-center justify-center">
          <div class="mx-auto border-y-4 border-stone-200 max-w-full md:max-w-3xl">
            <p class="text-xl md:text-3xl italic text-black font-serif my-2" id="quote-3"></p>
          </div>
        </div>
      </div>
      <div class="flex justify-center gap-2.5 mt-4">
        <span class="indicator w-3 h-3 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300 ease-in-out hover:bg-teal-500 hover:opacity-70" data-slide="0"></span>
        <span class="indicator w-3 h-3 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300 ease-in-out hover:bg-teal-500 hover:opacity-70" data-slide="1"></span>
        <span class="indicator w-3 h-3 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300 ease-in-out hover:bg-teal-500 hover:opacity-70" data-slide="2"></span>
      </div>
    </div>
  </div>

  <style>
    .quote-slide.active {
      opacity: 1;
      transform: translateX(0);
      visibility: visible;
    }
    .quote-slide.prev {
      transform: translateX(-100%);
    }
    .quote-slide.next {
      transform: translateX(100%);
    }
    .indicator.active {
        background-color: #2b9c94;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Show loading state
      const quoteElements = [document.getElementById('quote-1'), document.getElementById('quote-2'), document.getElementById('quote-3')];
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
      setInterval(nextSlide, 6000);

      // Manual navigation with indicators
      indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
          currentSlide = index;
          showSlide(currentSlide);
        });
      });
    });
  </script>
</section>