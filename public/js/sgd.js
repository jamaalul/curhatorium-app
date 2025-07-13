// Mock API endpoint (in a real app, this would be your actual API URL)
const API_URL = "https://api.example.com/groups";

// DOM elements
const groupsContainer = document.getElementById("groups-container");
const createGroupForm = document.getElementById("create-group-form");

// Mock data for groups (simulating API response)
const mockGroups = [
    {
        id: 1,
        title: "Anxiety Support Group",
        description:
            "A safe space to discuss anxiety, share coping mechanisms, and support each other through difficult times.",
        creator: "Sarah J.",
        category: "Mental Health",
        memberCount: 12,
    },
    {
        id: 2,
        title: "Career Transition",
        description:
            "For professionals looking to change careers or industries. Share advice, resources, and experiences.",
        creator: "Michael T.",
        category: "Career",
        memberCount: 8,
    },
    {
        id: 3,
        title: "Mindfulness Practice",
        description:
            "Daily mindfulness exercises, meditation techniques, and discussions about incorporating mindfulness into everyday life.",
        creator: "Emma L.",
        category: "Mental Health",
        memberCount: 15,
    },
    {
        id: 4,
        title: "Study Buddies",
        description:
            "Connect with others studying similar subjects. Share study techniques, resources, and motivate each other.",
        creator: "Alex W.",
        category: "Education",
        memberCount: 6,
    },
    {
        id: 5,
        title: "Relationship Advice",
        description:
            "Discuss relationship challenges, communication strategies, and building healthy connections with partners, friends, and family.",
        creator: "Jamie K.",
        category: "Relationships",
        memberCount: 10,
    },
    {
        id: 6,
        title: "Grief Support",
        description:
            "A compassionate community for those experiencing loss. Share your journey and find support through difficult times.",
        creator: "David M.",
        category: "Mental Health",
        memberCount: 9,
    },
];

// Function to fetch groups from API
async function fetchGroups() {
    try {
        // In a real application, you would use fetch like this:
        // const response = await fetch(API_URL);
        // if (!response.ok) throw new Error('Failed to fetch groups');
        // const data = await response.json();

        // For demo purposes, we'll simulate an API call with a delay
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(mockGroups);
            }, 1500); // Simulate network delay
        });
    } catch (error) {
        throw new Error("Failed to fetch groups: " + error.message);
    }
}

// Function to render a single group card
function renderGroupCard(group) {
    const card = document.createElement("div");
    card.className = "group-card";
    card.innerHTML = `
                <div class="card-header">
                    <div class="member-count">${group.memberCount} members</div>
                </div>
                <div class="card-content">
                    <h3 class="card-title">${group.title}</h3>
                    <div class="card-meta">
                        <span>Created by: ${group.creator}</span>
                        <span>${group.category}</span>
                    </div>
                    <p class="card-text">${group.description}</p>
                    <button class="join-button" data-group-id="${group.id}">
                        Join Group <span>➔</span>
                    </button>
                </div>
            `;
    return card;
}

// Function to render error state
function renderError(message) {
    groupsContainer.innerHTML = `
                <div class="error-container">
                    <h3 class="error-title">Error Loading Groups</h3>
                    <p class="error-message">${message}</p>
                    <button class="retry-button" onclick="loadGroups()">Try Again</button>
                </div>
            `;
}

// Function to render loading state
function renderLoading() {
    groupsContainer.innerHTML = `
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                </div>
            `;
}

// Function to load and render all groups
async function loadGroups() {
    // Show loading state
    renderLoading();

    try {
        // Fetch groups data
        const groups = await fetchGroups();

        // Clear loading spinner
        groupsContainer.innerHTML = "";

        // Check if we have groups to display
        if (groups.length === 0) {
            groupsContainer.innerHTML =
                "<p>No groups available. Be the first to create one!</p>";
            return;
        }

        // Render each group card
        groups.forEach((group) => {
            const card = renderGroupCard(group);
            groupsContainer.appendChild(card);
        });

        // Add event listeners to join buttons
        document.querySelectorAll(".join-button").forEach((button) => {
            button.addEventListener("click", function () {
                const groupId = this.getAttribute("data-group-id");
                joinGroup(groupId);
            });
        });
    } catch (error) {
        renderError(error.message);
        console.error("Error loading groups:", error);
    }
}

// Function to handle joining a group
function joinGroup(groupId) {
    // In a real app, you would make an API call here
    alert(`Joining group with ID: ${groupId}`);

    // Example of how you might handle this in a real app:
    // fetch(`${API_URL}/${groupId}/join`, {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //     },
    //     body: JSON.stringify({ userId: currentUserId }),
    // })
    // .then(response => response.json())
    // .then(data => {
    //     // Handle successful join
    //     console.log('Joined group:', data);
    // })
    // .catch(error => {
    //     console.error('Error joining group:', error);
    // });
}

// Function to handle creating a new group
async function createGroup(groupData) {
    try {
        // In a real app, you would make an API call here
        // const response = await fetch(API_URL, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify(groupData),
        // });
        // if (!response.ok) throw new Error('Failed to create group');
        // const data = await response.json();

        // For demo purposes, we'll simulate creating a group
        console.log("Creating group:", groupData);
        alert(`Group "${groupData.title}" created successfully!`);

        // Reload groups to show the new one
        loadGroups();

        // Clear form
        createGroupForm.reset();
    } catch (error) {
        console.error("Error creating group:", error);
        alert("Failed to create group: " + error.message);
    }
}

// Event listener for form submission
createGroupForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const title = document.getElementById("group-title").value.trim();
    const topic = document.getElementById("group-topic").value.trim();
    const category = document.getElementById("group-category").value;

    // Simple validation
    if (!title) {
        alert("Please enter a group title");
        return;
    }

    if (!topic) {
        alert("Please enter a group topic");
        return;
    }

    if (!category) {
        alert("Please select a category");
        return;
    }

    // Create group data object
    const groupData = {
        title,
        description: topic,
        category,
        creator: "JohnDoe", // In a real app, this would come from the logged-in user
        memberCount: 1,
    };

    // Submit the group
    createGroup(groupData);
});

// // Toggle mobile menu
// document
//     .querySelector(".mobile-menu-button")
//     .addEventListener("click", function () {
//         const profileBox = document.getElementById("profile-box");
//         profileBox.classList.toggle("active");

//         // Toggle hamburger to X
//         const spans = this.querySelectorAll("span");
//         if (profileBox.classList.contains("active")) {
//             spans[0].style.transform = "rotate(-45deg) translate(-5px, 6px)";
//             spans[1].style.opacity = "0";
//             spans[2].style.transform = "rotate(45deg) translate(-5px, -6px)";
//         } else {
//             spans[0].style.transform = "none";
//             spans[1].style.opacity = "1";
//             spans[2].style.transform = "none";
//         }
//     });

// // Close menu when clicking outside
// document.addEventListener("click", function (event) {
//     const profileBox = document.getElementById("profile-box");
//     const mobileMenuButton = document.querySelector(".mobile-menu-button");

//     if (
//         !profileBox.contains(event.target) &&
//         !mobileMenuButton.contains(event.target) &&
//         profileBox.classList.contains("active")
//     ) {
//         profileBox.classList.remove("active");

//         // Reset hamburger icon
//         const spans = mobileMenuButton.querySelectorAll("span");
//         spans[0].style.transform = "none";
//         spans[1].style.opacity = "1";
//         spans[2].style.transform = "none";
//     }
// });

// Load groups when the page loads
document.addEventListener("DOMContentLoaded", function () {
    // Auto-submit form when filters change (optional enhancement)
    const filterSelects = document.querySelectorAll(".filter-select");
    const searchInput = document.querySelector(".search-input");

    // Debounce function for search input
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Auto-submit on filter change (optional - uncomment if you want this behavior)
    /*
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    */

    // Real-time search with debouncing (optional enhancement)
    /*
    const debouncedSearch = debounce(function() {
        searchInput.closest('form').submit();
    }, 500);
    
    searchInput.addEventListener('input', debouncedSearch);
    */

    // Add loading states to buttons
    const applyButton = document.querySelector(".apply-filters-btn");
    if (applyButton) {
        applyButton.addEventListener("click", function () {
            this.innerHTML =
                '<span class="loading-spinner-small"></span> Applying...';
            this.disabled = true;
        });
    }

    // Add visual feedback for active filters
    function highlightActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeFilters = [];

        if (urlParams.get("search")) {
            activeFilters.push("Search: " + urlParams.get("search"));
        }
        if (urlParams.get("category") && urlParams.get("category") !== "all") {
            activeFilters.push("Category: " + urlParams.get("category"));
        }
        if (urlParams.get("status")) {
            activeFilters.push("Status: " + urlParams.get("status"));
        }

        // Create active filters display
        if (activeFilters.length > 0) {
            const activeFiltersContainer = document.createElement("div");
            activeFiltersContainer.className = "active-filters";
            activeFiltersContainer.innerHTML = `
                <span class="active-filters-label">Active filters:</span>
                ${activeFilters
                    .map(
                        (filter) =>
                            `<span class="active-filter-tag">${filter}</span>`
                    )
                    .join("")}
            `;

            const searchFilterSection = document.querySelector(
                ".search-filter-section"
            );
            if (searchFilterSection) {
                searchFilterSection.appendChild(activeFiltersContainer);
            }
        }
    }

    // Initialize active filters display
    highlightActiveFilters();

    // Add smooth scrolling for better UX
    function smoothScrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    }

    // Smooth scroll to top when applying filters
    const form = document.querySelector(".search-filter-form");
    if (form) {
        form.addEventListener("submit", function () {
            // Small delay to ensure form submission starts
            setTimeout(smoothScrollToTop, 100);
        });
    }

    // Add keyboard shortcuts
    document.addEventListener("keydown", function (e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
            e.preventDefault();
            searchInput.focus();
        }

        // Escape to clear search
        if (e.key === "Escape" && document.activeElement === searchInput) {
            searchInput.value = "";
            searchInput.blur();
        }
    });

    // Add tooltips for better UX
    const tooltipElements = document.querySelectorAll("[data-tooltip]");
    tooltipElements.forEach((element) => {
        element.addEventListener("mouseenter", function () {
            const tooltip = document.createElement("div");
            tooltip.className = "tooltip";
            tooltip.textContent = this.getAttribute("data-tooltip");
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.left =
                rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + "px";
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + "px";
        });

        element.addEventListener("mouseleave", function () {
            const tooltip = document.querySelector(".tooltip");
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
});

// Add CSS for new elements
const style = document.createElement("style");
style.textContent = `
    .active-filters {
        margin-top: 16px;
        padding: 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border-left: 4px solid #8ecbcf;
    }
    
    .active-filters-label {
        font-weight: 500;
        color: #555;
        margin-right: 8px;
    }
    
    .active-filter-tag {
        display: inline-block;
        background-color: #8ecbcf;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin: 2px 4px 2px 0;
    }
    
    .loading-spinner-small {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }
    
    .tooltip {
        position: fixed;
        background-color: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        white-space: nowrap;
    }
    
    .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
`;
document.head.appendChild(style);
