document.addEventListener("DOMContentLoaded", function () {
    const searchToggle = document.querySelector(".js-search-toggle");
    const searchInput = document.querySelector(".js-surah-search");
    const chips = document.querySelectorAll(".js-filter-chip");
    const juzSections = document.querySelectorAll(".juz-section");
    const noResultsMsg = document.getElementById("noResultsMsg");

    let activeFilter = "all";
    let autoCloseTimer = null;
    const AUTO_CLOSE_DELAY = 3000;

    const emptyMessages = {
        all: "No surah found.",
        progress: "No surah in progress yet.",
        completed: "No surah completed yet.",
    };

    function openSearch() {
        searchInput.classList.add("is-open");
        searchToggle.setAttribute("aria-expanded", "true");
        searchInput.focus();
        resetAutoCloseTimer();
    }

    function closeSearch() {
        if (searchInput.value.trim() !== "") return;
        searchInput.classList.remove("is-open");
        searchToggle.setAttribute("aria-expanded", "false");
        clearTimeout(autoCloseTimer);
    }

    function resetAutoCloseTimer() {
        clearTimeout(autoCloseTimer);
        autoCloseTimer = setTimeout(closeSearch, AUTO_CLOSE_DELAY);
    }

    searchToggle.addEventListener("click", () => {
        if (searchInput.classList.contains("is-open")) {
            searchInput.value = "";
            applyFilters();
            closeSearch();
        } else {
            openSearch();
        }
    });

    searchInput.addEventListener("input", () => {
        resetAutoCloseTimer();
        applyFilters();
    });

    document.addEventListener("click", (e) => {
        const isInsideSearch =
            searchInput.contains(e.target) || searchToggle.contains(e.target);
        if (!isInsideSearch && searchInput.classList.contains("is-open")) {
            closeSearch();
        }
    });

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        let totalVisible = 0;

        juzSections.forEach((section) => {
            let visibleInSection = 0;

            section.querySelectorAll(".js-surah-row").forEach((row) => {
                const name = row.dataset.name;
                const percent = parseInt(row.dataset.percent, 10);

                const matchesSearch = name.includes(query);
                const matchesFilter =
                    activeFilter === "all" ||
                    (activeFilter === "progress" &&
                        percent > 0 &&
                        percent < 100) ||
                    (activeFilter === "completed" && percent === 100);

                const show = matchesSearch && matchesFilter;
                row.style.display = show ? "flex" : "none";
                if (show) visibleInSection++;
            });

            section.style.display = visibleInSection > 0 ? "block" : "none";
            totalVisible += visibleInSection;
        });

        // Search khaali hai toh filter-specific message, warna generic "not found"
        const query_ = searchInput.value.trim();
        noResultsMsg.textContent =
            query_ !== ""
                ? "No surah found."
                : emptyMessages[activeFilter] || "No surah found.";

        noResultsMsg.style.display = totalVisible === 0 ? "block" : "none";
    }

    chips.forEach((chip) => {
        chip.addEventListener("click", () => {
            chips.forEach((c) => c.classList.remove("active"));
            chip.classList.add("active");
            activeFilter = chip.dataset.filter;
            applyFilters();
        });
    });
});
