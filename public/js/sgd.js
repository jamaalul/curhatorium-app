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
    loadGroups();
});
