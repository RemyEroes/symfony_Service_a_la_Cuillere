// Description: Script pour supprimer les favoris dans le compte

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname === '/compte') {
        let boutonEditFav = document.querySelector('.compte-edit-favoris-button');

        // supprimer le bouton si pas de favoris
        let favoris_container = document.querySelector('.compte-favoris-recettes');
        if (favoris_container.querySelector('.error-no-content')){
            boutonEditFav.style.display = 'none';
        }



        let editMode = false;
        let favorisDeleteIds = [];

        boutonEditFav.addEventListener('click', function() {
            editMode = !editMode;
            boutonEditFav.textContent = editMode ? 'Terminer' : 'Editer mes favoris';

            let favorisDivs = document.querySelectorAll('.recette-favoris');

            favorisDivs.forEach(function(favoris) {
                let a = favoris.querySelector('a');
                let favoriteButton = favoris.querySelector('.compte-recettes-heart-container');
                const recipeID = favoris.getAttribute('data-recipe-id');

                if (editMode) {
                    a.addEventListener('click', preventDefaultLink);
                    favoriteButton.style.backgroundColor = '#ffb1b7';
                    favoriteButton.addEventListener('click', handleFavoriteButtonClick);
                } else {
                    a.removeEventListener('click', preventDefaultLink);
                    favoriteButton.removeEventListener('click', handleFavoriteButtonClick);
                    resetFavoriteButton(favoriteButton);
                }
            });

            if (!editMode && favorisDeleteIds.length > 0) {
                confirmAndDeleteFavorites(favorisDeleteIds);
                favorisDeleteIds = []; // Reset after deletion
            }
        });

        function preventDefaultLink(event) {
            event.preventDefault();
        }

        function handleFavoriteButtonClick() {
            let favoriteButton = this;
            let recipeID = favoriteButton.closest('.recette-favoris').getAttribute('data-recipe-id');
            toggleFavorite(favoriteButton, recipeID);
        }

        function toggleFavorite(favoriteButton, recipeID) {
            let content = favoriteButton.innerHTML.replace('<p>', '').replace('</p>', '');

            if (content === '🗑️') {
                favoriteButton.innerHTML = '<p>♥️</p>';
                favoriteButton.style.backgroundColor = '#ffb1b7';
                favorisDeleteIds = favorisDeleteIds.filter(id => id !== recipeID);
            } else {
                favoriteButton.innerHTML = '<p>🗑️</p>';
                favoriteButton.style.backgroundColor = '#ff3e4e';
                favorisDeleteIds.push(recipeID);
            }

            console.log(favorisDeleteIds);
        }

        function resetFavoriteButton(favoriteButton) {
            favoriteButton.innerHTML = '<p>♥️</p>';
            favoriteButton.style.backgroundColor = 'white';
        }

        function confirmAndDeleteFavorites(favorisDeleteIds) {
            const userConfirmed = confirm('Voulez-vous vraiment supprimer les recettes sélectionnées ?');
            if (userConfirmed) {
                let favorisDeleteIdsSring = favorisDeleteIds.join('--');

                window.location.href = `recette/favoris/supprimer?page=compte&ids=${favorisDeleteIdsSring}`;
                console.log('Recette supprimée : ', favorisDeleteIds);
            } else {
                console.log('Suppression annulée pour les recettes : ', favorisDeleteIds);
            }
        }
    }
});