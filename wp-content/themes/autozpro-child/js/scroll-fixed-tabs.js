document.addEventListener("DOMContentLoaded", function () {
    let tabs = document.querySelector(".tabs.wc-tabs");
    if (!tabs) return; // Jeśli element nie istnieje, nie wykonuj kodu

    let tabsPlaceholder = document.createElement("div");
    tabsPlaceholder.style.width = tabs.offsetWidth + "px";
    tabsPlaceholder.style.height = tabs.offsetHeight + "px";
    tabsPlaceholder.style.display = "none"; // Ukrywamy początkowo

    tabs.parentNode.insertBefore(tabsPlaceholder, tabs);

    let tabsTopOffset = tabs.getBoundingClientRect().top + window.scrollY; // Pobranie poprawnej pozycji
    let lastScrollY = window.scrollY;

    function handleScroll() {
        let currentScrollY = window.scrollY;

        if (currentScrollY >= tabsTopOffset) {
            if (!tabs.classList.contains("fixed-tabs")) {
                tabsPlaceholder.style.display = "block"; // Utrzymuje wysokość
                tabs.classList.add("fixed-tabs");
                tabs.style.width = tabsPlaceholder.offsetWidth + "px"; // Zapewnia stałą szerokość
            }
        } else {
            if (tabs.classList.contains("fixed-tabs")) {
                tabsPlaceholder.style.display = "none";
                tabs.classList.remove("fixed-tabs");
                tabs.style.width = ""; // Reset szerokości
            }
        }

        lastScrollY = currentScrollY;
    }

    window.addEventListener("scroll", () => {
        requestAnimationFrame(handleScroll);
    });

    window.addEventListener("resize", () => {
        tabsTopOffset = tabs.getBoundingClientRect().top + window.scrollY; // Aktualizujemy pozycję
    });
});