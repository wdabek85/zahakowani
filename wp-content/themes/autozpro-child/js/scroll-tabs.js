document.addEventListener('DOMContentLoaded', function () {
    // Znajdź wszystkie linki w zakładkach
    const tabs = document.querySelectorAll('#tab-title-specification_tab > a, #tab-title-description > a, #tab-title-reviews > a'); // Dodaj odpowiednie selektory

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault(); // Zablokuj domyślne zachowanie linku

            const targetId = this.getAttribute('href'); // Pobierz ID docelowej sekcji (np. #tab-description)
            const targetElement = document.querySelector(targetId); // Znajdź element docelowy

            if (targetElement) {
                // Płynne przewijanie do sekcji
                targetElement.scrollIntoView({
                    behavior: 'smooth', // Płynne przewijanie
                    block: 'start' // Przewiń do górnej części sekcji
                });
            }
        });
    });
});

