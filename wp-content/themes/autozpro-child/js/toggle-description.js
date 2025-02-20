document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.querySelector('.toggle-description');
    const descriptionContent = document.querySelector('.description-content');

    // Sprawdzenie szerokości ekranu
    const mediaQuery = window.matchMedia('(min-width: 787px)');

    function toggleDescription() {
        if (toggleButton && descriptionContent) {
            toggleButton.addEventListener('click', function () {
                if (descriptionContent.classList.contains('expanded')) {
                    // Zwinięcie opisu
                    descriptionContent.classList.remove('expanded');
                    toggleButton.textContent = 'Rozwiń pełen opis';

                    // Przewiń do początku sekcji
                    descriptionContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    // Rozwinięcie opisu
                    descriptionContent.classList.add('expanded');
                    toggleButton.textContent = 'Zwiń pełen opis';
                }
            });
        }
    }

    // Funkcja uruchamiana, jeśli media query pasuje
    if (mediaQuery.matches) {
        toggleDescription();
    }

    // Nasłuchiwanie zmian szerokości okna
    mediaQuery.addEventListener('change', function (e) {
        if (e.matches) {
            toggleDescription();
        } else {
            // Możesz dodać tu logikę na wyczyszczenie działania dla mniejszych ekranów
        }
    });
});

