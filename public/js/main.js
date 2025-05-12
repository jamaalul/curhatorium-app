// Quote Section
// const quoteText = document.querySelector(".quote-text");

// quoteText.textContent = `"` +

fetch("/quote/today")
    .then((response) => {
        if (!response.ok) {
            throw new Error("Failed to fetch quote");
        }
        return response.json();
    })
    .then((data) => {
        document.querySelector(".quote-text").textContent = `"${data.quote}"`;
    })
    .catch((error) => {
        document.querySelector(".quote-text").textContent =
            "Failed to load quote.";
        console.error("Error fetching quote:", error);
    });

// Features Section

const mentalTest = document.getElementById("to-mental-test");
const shareTalk = document.getElementById("to-share-talk");
const chatbot = document.getElementById("to-chatbot");
const missions = document.getElementById("to-missions");
const sgd = document.getElementById("to-sgd");
const deepCards = document.getElementById("to-deep-cards");

mentalTest.addEventListener("click", function () {
    window.location.href = "/mental-health-test";
});

shareTalk.addEventListener("click", function () {
    window.location.href = "/share-and-talk";
});

chatbot.addEventListener("click", function () {
    window.location.href = "/mental-support-chatbot";
});

missions.addEventListener("click", function () {
    window.location.href = "/missions-of-the-day";
});

sgd.addEventListener("click", function () {
    window.location.href = "/support-group-discussion";
});

deepCards.addEventListener("click", function () {
    window.location.href = "/deep-cards";
});

// Agendas Section
const agendaSection = document.querySelector(".agenda-section");

fetch("/agenda/pending")
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        // console.log("Pending agendas:", data);
        // Kamu bisa looping dan tampilkan datanya di halaman:

        let i = 1;
        data.forEach((item) => {
            // console.log(item.title); // atau properti lain seperti item.id, item.is_done
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
