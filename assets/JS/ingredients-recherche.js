if (window.location.pathname === '/recettes') {
    document.addEventListener('DOMContentLoaded', function () {


        function get_ingredients_params() {
            const urlParams = new URLSearchParams(window.location.search);
            const ingredients_params_url = urlParams.get('ingredients');
            return ingredients_params_url ? ingredients_params_url.split('--') : [];
        }

        function select_ingredient_OFD(ingredients_array) {

            ingredients_array = ingredients_array.map(ingredient => ingredient.toLowerCase());

            const items_ingredients = document.querySelectorAll('.search-ingredient-list-all .ingredient-item');
            const div_ingredients_selected = document.querySelector('.search-ingredient-list-selected');

            items_ingredients.forEach((item) => {
                if (ingredients_array.includes(item.dataset.ingredient.toLowerCase())) {
                    item.classList.add('selected');
                    const item_selected = item.cloneNode(true);
                    div_ingredients_selected.appendChild(item_selected);
                }
            });
        }

        function set_ingredients_in_local_cache() {
            const items_ingredients = document.querySelectorAll('.search-ingredient-list-selected .ingredient-item');
            const ingredients_array = Array.from(items_ingredients).map(item => item.dataset.ingredient);
            localStorage.setItem('ingredients', JSON.stringify(ingredients_array));
        }


        function add_ingredient_listeners() {
            const items_ingredients_all = document.querySelector('.search-ingredient-list-all');
            const items_ingredients_selected = document.querySelector('.search-ingredient-list-selected');
        
            // Écouteur pour la liste complète d'ingrédients
            items_ingredients_all.addEventListener('click', function (event) {
                const target = event.target.closest('.ingredient-item');
                if (!target) return;
        
                // Vérifie si l'élément est déjà sélectionné pour éviter les duplications
                if (target.classList.contains('selected')) return;
        
                target.classList.add('selected');
                const item_selected = target.cloneNode(true);
                items_ingredients_selected.appendChild(item_selected);
        
                // Ajoute un écouteur d'événement à l'élément cloné pour permettre sa suppression
                item_selected.addEventListener('click', function () {
                    target.classList.remove('selected');
                    item_selected.remove();
                    set_ingredients_in_local_cache();
                });
        
                set_ingredients_in_local_cache();
            });
        
            // Écouteur pour la liste des ingrédients sélectionnés
            items_ingredients_selected.addEventListener('click', function (event) {
                const target = event.target.closest('.ingredient-item');
                if (!target) return;
        
                target.remove();
                const ingredient = target.dataset.ingredient;
                const item_to_remove = items_ingredients_all.querySelector(`.ingredient-item[data-ingredient="${ingredient}"]`);
                if (item_to_remove) {
                    item_to_remove.classList.remove('selected');
                }
                set_ingredients_in_local_cache();
            });

            remove_selected_ingredient_listener()
        }
        
        // Fonction pour enregistrer les ingrédients sélectionnés dans le cache local
        function set_ingredients_in_local_cache() {
            const items_ingredients = document.querySelectorAll('.search-ingredient-list-selected .ingredient-item');
            const ingredients_array = Array.from(items_ingredients).map(item => item.dataset.ingredient);
            localStorage.setItem('ingredients', JSON.stringify(ingredients_array));
        }
        
        // Suppression des écouteurs d'événements obsolètes
        function remove_selected_ingredient_listener() {
            const items_ingredients_selected = document.querySelectorAll('.search-ingredient-list-all .ingredient-item.selected');
            items_ingredients_selected.forEach(item => {
                item.removeEventListener('click', () => {});
            });
        }

        function get_ingredients_from_local_cache() {
            const ingredients = localStorage.getItem('ingredients');
            return ingredients ? JSON.parse(ingredients) : [];
        }

        function remove_ingredient_from_local_cache() {
            localStorage.removeItem('ingredients');
        }

        function add_button_reset_listeners() {
            // ingredients
            const button_reset_ingredients = document.querySelector('#search-ingredient-button');

            button_reset_ingredients.addEventListener('click', function () {
                remove_ingredient_from_local_cache();
                remove_selected_ingredients()

                // supprimer search bar ingredients value et afficher tous les ingredients
                document.querySelector('#search-ingredient').value = '';
                const items_ingredients = document.querySelectorAll('.search-ingredient-list-all .ingredient-item');
                items_ingredients.forEach((item) => {
                    item.style.display = 'block';
                });
                return;

            });


            // name
            const button_reset_name = document.querySelector('#search-name-button');
            button_reset_name.addEventListener('click', function () {
                localStorage.removeItem('name');
                document.querySelector('#search-name').value = '';
            });
        }

        function remove_selected_ingredients() {
            const items_ingredients_selected = document.querySelector('.search-ingredient-list-selected');
            const items_ingredients_all = document.querySelector('.search-ingredient-list-all');

            const items_selected = items_ingredients_selected.querySelectorAll('.ingredient-item');
            items_selected.forEach((item) => {
                const ingredient = item.dataset.ingredient;
                const item_to_remove = items_ingredients_all.querySelector(`.ingredient-item[data-ingredient="${ingredient}"]`);
                item_to_remove.classList.remove('selected');
                item.remove();
            });
        }

        function get_name_params() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('name')) {
                localStorage.setItem('name', urlParams.get('name'));
            }
            return urlParams.get('name');
        }

        function get_name_from_local_cache() {
            return localStorage.getItem('name');
        }

        let ingredients_array = get_ingredients_params();

        if (ingredients_array.length === 0) {
            ingredients_array = get_ingredients_from_local_cache();
        }

        let name_search_value = get_name_params();
        if (!name_search_value) {
            name_search_value = get_name_from_local_cache()
        }

        if (name_search_value) {
            document.querySelector('#search-name').value = name_search_value;
        }
       

        select_ingredient_OFD(ingredients_array);
        set_ingredients_in_local_cache();
        add_ingredient_listeners();
        add_button_reset_listeners()


        // search bar ingredients
        const search_bar_ingredients = document.querySelector('#search-ingredient');
        search_bar_ingredients.addEventListener('keyup', function () {
            const search_value = search_bar_ingredients.value.toLowerCase();

            const items_ingredients = document.querySelectorAll('.search-ingredient-list-all .ingredient-item');

            items_ingredients.forEach((item) => {
                const ingredient = item.dataset.ingredient.toLowerCase();
                if (ingredient.includes(search_value)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });


        // bouton recherche
        const search_button = document.querySelector('#bouton-search');
        search_button.addEventListener('click', function () {
            let final_url = '/recettes';
            let url_ingr = '';
            let url_name = '';

            // ingredients
            let ingredients_array_local = get_ingredients_from_local_cache()
            ingredients_array_local = ingredients_array_local.map(ingredient => ingredient.toLowerCase());

            if (Array.isArray(ingredients_array_local) && ingredients_array_local.length > 0) {
                url_ingr = '?ingredients=' + ingredients_array_local.join('--');
            }

            //name
            let name = document.querySelector('#search-name').value.toLowerCase();
            if (name) {
                url_name = url_ingr ? '&name=' + name : '?name=' + name;
            }

            final_url += url_ingr + url_name;
            window.location.href = final_url;
        });

    });
}