// Description: Script pour ajouter des ingrédients dans le formulaire d'ajout d'ingrédients

if (window.location.pathname === '/ingredient/ajouter') {

    let ingredients_part = document.querySelectorAll('.form-ingredient');

    ingredients_part.forEach((ingredient_part) => {
        let checkboxes = ingredient_part.querySelectorAll('.checkbox-nom-pluriel input[name^="choice-"]');
        let div_nom_pluriel = ingredient_part.querySelector('[class*="nom-pluriel-"]');

        // Caché par défaut
        div_nom_pluriel.style.display = 'none';

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked && checkbox.value === 'oui') {
                    div_nom_pluriel.style.display = 'block';
                } else {
                    div_nom_pluriel.style.display = 'none';
                }
            });
        });
    });

    // Add more ingredients
    let add_ingredient = document.querySelector('.add-more-button');

    function addIngredientPart() {
        let ingredients_part = document.querySelectorAll('.form-ingredient');
        let ingredients_part_number = ingredients_part.length + 1;
        console.log('Création de la partie ' + ingredients_part_number);

        let last_ingredient_part = ingredients_part[ingredients_part.length - 1];
        let new_ingredient_part = last_ingredient_part.cloneNode(true);

        // Réinitialiser les valeurs des inputs
        let inputs = new_ingredient_part.querySelectorAll('input');
        inputs.forEach((input) => {
            // Réinitialiser la valeur à une chaîne vide si c'est un input text ou file
            if (input.type === 'text' || input.type === 'file') {
                input.value = '';
            }
            // si c'est un input radio 
            if (input.type === 'radio') {
                if (input.value === 'non') {
                    input.checked = true;
                }else{
                    input.checked = false;
                }

            }
        });

        // rendre visible le bouton de suppression
        let delete_button = new_ingredient_part.querySelector('.delete-button');
        delete_button.style.display = 'block';

        // Changer les noms et ids
        let label_name = new_ingredient_part.querySelector('label[for^="name-ingredient-"]');
        label_name.setAttribute('for', 'name-ingredient-' + ingredients_part_number);
        let input_name = new_ingredient_part.querySelector('input[name^="name-ingredient-"]');
        input_name.setAttribute('name', 'name-ingredient-' + ingredients_part_number);
        input_name.setAttribute('id', 'name-ingredient-' + ingredients_part_number);

        let checkboxes = new_ingredient_part.querySelectorAll('.checkbox-nom-pluriel input[name^="choice-"]');
        checkboxes.forEach((checkbox) => {
            checkbox.setAttribute('name', 'choice-' + ingredients_part_number);
        });

        let div_nom_pluriel = new_ingredient_part.querySelector('[class*="nom-pluriel-"]');
        div_nom_pluriel.classList.remove('nom-pluriel-' + (ingredients_part_number - 1));
        div_nom_pluriel.classList.add('nom-pluriel-' + ingredients_part_number);
        let div_nom_pluriel_label = new_ingredient_part.querySelector('label[for^="name-pluriel-"]');
        div_nom_pluriel_label.setAttribute('for', 'name-pluriel-' + ingredients_part_number);
        let div_nom_pluriel_input = new_ingredient_part.querySelector('input[name^="name-pluriel-"]');
        div_nom_pluriel_input.setAttribute('name', 'name-pluriel-' + ingredients_part_number);
        div_nom_pluriel_input.setAttribute('id', 'name-pluriel-' + ingredients_part_number);

        let image_label = new_ingredient_part.querySelector('label[for^="image-"]');
        image_label.setAttribute('for', 'image-' + ingredients_part_number);
        let image_input = new_ingredient_part.querySelector('input[name^="image-"]');
        image_input.setAttribute('name', 'image-' + ingredients_part_number);
        image_input.setAttribute('id', 'image-' + ingredients_part_number);

        // Caché par défaut
        div_nom_pluriel.style.display = 'none';

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked && checkbox.value === 'oui') {
                    div_nom_pluriel.style.display = 'block';
                } else {
                    div_nom_pluriel.style.display = 'none';
                }
            });
        });

        // Ajouter la nouvelle partie d'ingrédient
        last_ingredient_part.after(new_ingredient_part);

        add_remove_listeners();
        check_file_size()
    }


    add_ingredient.addEventListener('click', (event) => {
        // Prevent default
        event.preventDefault();

        addIngredientPart();
    });


    // Remove ingredient
    function add_remove_listeners() {
        const buttons_remove_ingredient = document.querySelectorAll('.form-ingredient .delete-button');


        buttons_remove_ingredient.forEach(function (button) {
            //si il y a un event listener déjà attaché, on le supprime
            button.removeEventListener('click', function (event) {
                event.preventDefault();

                const ingredientItem = button.parentElement;
                ingredientItem.remove();
            });
        });

    
        buttons_remove_ingredient.forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                const ingredientItem = button.parentElement;
                ingredientItem.remove();
            });
        });
    }
    function disable_first_remove_button() {
        const button_remove_ingredient = document.querySelector('.form-ingredient .delete-button');
        button_remove_ingredient.style.display = 'none';
    }

    disable_first_remove_button();
    add_remove_listeners();
    

    function check_file_size(){
        let inputs = document.querySelectorAll('input[type="file"]');

        inputs.forEach((input) => {
            input.removeEventListener('change', () => {});

            input.addEventListener('change', () => {
                let file = input.files[0];
                if (file.size > 2 * 1024 * 1024) { // 2MB in bytes
                    alert('File size exceeds 2MB.');
                    input.value = '';
                }
            });
        })
    }
    check_file_size()

}