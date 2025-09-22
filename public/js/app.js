/**
 * Main Application JavaScript
 * Entry point for all JavaScript modules
 */

// Note: Individual modules are loaded directly in their respective Blade templates
// This file contains shared functionality and utilities

// Quote Section
document.addEventListener('DOMContentLoaded', function() {
    const quoteText = document.querySelector(".quote-text");
    if (quoteText) {
        fetch("/quote/today")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch quote");
                }
                return response.json();
            })
            .then((data) => {
                quoteText.textContent = `"${data.quote}"`;
            })
            .catch((error) => {
                quoteText.textContent = "Failed to load quote.";
                console.error("Error fetching quote:", error);
            });
    }
});

// Features Section Navigation
document.addEventListener('DOMContentLoaded', function() {
    const mentalTest = document.getElementById("to-mental-test");
    const shareTalk = document.getElementById("to-share-talk");
    const chatbot = document.getElementById("to-chatbot");
    const missions = document.getElementById("to-missions");
    const sgd = document.getElementById("to-sgd");
    const deepCards = document.getElementById("to-deep-cards");

    if (mentalTest) {
        mentalTest.addEventListener("click", function () {
            window.location.href = "/mental-health-test";
        });
    }

    if (shareTalk) {
        shareTalk.addEventListener("click", function () {
            window.location.href = "/share-and-talk";
        });
    }

    if (chatbot) {
        chatbot.addEventListener("click", function () {
            window.location.href = "/mental-support-chatbot";
        });
    }

    if (missions) {
        missions.addEventListener("click", function () {
            window.location.href = "/missions-of-the-day";
        });
    }

    if (sgd) {
        sgd.addEventListener("click", function () {
            window.location.href = "/support-group-discussion";
        });
    }

    if (deepCards) {
        deepCards.addEventListener("click", function () {
            window.location.href = "/deep-cards";
        });
    }
});

// Agendas Section
document.addEventListener('DOMContentLoaded', function() {
    const agendaSection = document.querySelector(".agenda-section");
    if (agendaSection) {
        fetch("/agenda/pending")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                let i = 1;
                data.forEach((item) => {
                    const agendaCard = document.createElement("div");
                    agendaCard.className = "agenda-card";
                    if (i % 2 == 0) {
                        agendaCard.classList.add("teal");
                    } else {
                        agendaCard.classList.add("yellow");
                    }
                    
                    const iconBox = document.createElement("div");
                    iconBox.className = "icon-box";
                    
                    const agendaText = document.createElement("div");
                    agendaText.className = "agenda-text";
                    
                    const small = document.createElement("small");
                    small.textContent = item.type;
                    
                    const strong = document.createElement("strong");
                    strong.textContent = item.title;
                    
                    const br = document.createElement("br");
                    
                    const span = document.createElement("span");
                    span.textContent = item.description;
                    
                    const dateTime = document.createElement("span");
                    dateTime.textContent = item.event_date + " " + item.event_time;

                    agendaText.appendChild(small);
                    agendaText.appendChild(strong);
                    agendaText.appendChild(span);
                    agendaText.appendChild(br);
                    agendaText.appendChild(dateTime);
                    agendaCard.appendChild(iconBox);
                    agendaCard.appendChild(agendaText);

                    agendaSection.appendChild(agendaCard);
                    i += 1;
                });
            })
            .catch((error) => {
                console.error("Fetch error:", error);
            });
    }
});

// Toast notification handler
document.addEventListener('DOMContentLoaded', function() {
    const toastError = document.getElementById('toast-error');
    if (toastError) {
        setTimeout(function() {
            if (toastError) toastError.style.display = 'none';
        }, 4000);
    }
});

// Utility functions
window.CurhatoriumApp = {
    // Show toast notification
    showToast: function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.id = 'toast-' + type;
        toast.style.cssText = `
            position: fixed; 
            top: 24px; 
            right: 24px; 
            z-index: 9999; 
            background: ${type === 'error' ? '#f87171' : type === 'success' ? '#10b981' : '#3b82f6'}; 
            color: white; 
            padding: 16px 24px; 
            border-radius: 8px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.15); 
            font-size: 1rem;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4000);
    },

    // Format date
    formatDate: function(date) {
        return new Date(date).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    // Format time
    formatTime: function(date) {
        return new Date(date).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
};

// Make available globally
window.CurhatoriumApp = window.CurhatoriumApp || {}; 