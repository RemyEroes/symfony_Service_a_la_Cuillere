if (window.location.pathname === '/recette/ajouter') {
    document.addEventListener('DOMContentLoaded', function () {

        function add_new_ingredient() {
            const $ingredients_item = document.querySelector('.ingredients-recette-item').cloneNode(true);

            // Afficher le bouton de suppression de l'ingrédient
            $ingredients_item.querySelector('.ingredient-supprimer-button').style.display = 'block';

            // Réinitialiser la valeur de l'input number à 0
            $ingredients_item.querySelector('.ingredient-quantity input').value = 0;

            // Mettre la mesure de l'ingrédient à 'unité'
            $ingredients_item.querySelector('.ingredient-mesurement select').value = 'unité';

            // Ajouter l'item en avant-dernier dans le container des ingrédients
            const $ingredients_container = document.querySelector('.ingredients-recette-container');
            $ingredients_container.insertBefore($ingredients_item, $ingredients_container.lastElementChild);

            // Mettre à jour les attributs name des nouveaux éléments
            update_ingredient_names();

            add_remove_listeners();
        }


        function add_remove_listeners() {
            const buttons_remove_ingredient = document.querySelectorAll('.ingredient-supprimer-button');
        
            buttons_remove_ingredient.forEach(function (button) {
                button.addEventListener('click', function () {
                    const ingredientItem = button.parentElement;
                    ingredientItem.remove();
                    update_ingredient_names();
                });
            });
        }

        function update_ingredient_names() {
            const $ingredients_container = document.querySelector('.ingredients-recette-container');
            const items = $ingredients_container.querySelectorAll('.ingredients-recette-item');
            items.forEach((item, index) => {
                item.querySelector('.ingredient-name select').name = `ingredients[${index}][name]`;
                item.querySelector('.ingredient-quantity input').name = `ingredients[${index}][quantity]`;
                item.querySelector('.ingredient-mesurement select').name = `ingredients[${index}][measurement]`;
            });
        }

        function disable_first_remove_button() {
            const button_remove_ingredient = document.querySelector('.ingredient-supprimer-button');
            button_remove_ingredient.style.display = 'none';
        }

        const button_add_ingredient = document.querySelector('.ingredients-recette-add-button');
        button_add_ingredient.addEventListener('click', function () {
            add_new_ingredient();
        });

        disable_first_remove_button();
        add_remove_listeners();



        // image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const fileType = file.type;
                const validImageTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if (validImageTypes.includes(fileType)) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    }

                    reader.readAsDataURL(file);
                } else {
                    alert('Please select an image file of type PNG, JPEG, or WebP.');
                }
            }
        });

    });
}