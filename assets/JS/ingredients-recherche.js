if (window.location.pathname === '/recettes') {
    document.addEventListener('DOMContentLoaded', function () {

//         let ingredient_search_opened = false;

//         // let search_ingredient_container = document.querySelector('.search-ingredient-container');
//         // let search_ingredient_all = document.querySelector('.search-ingredient-list-all');
//         // let close_button = document.querySelector('.close-ingredient-research');

//         // search_ingredient_container.addEventListener('click', function () {
//         //     if (ingredient_search_opened) {
//         //         search_ingredient_all.style.display = 'none';
//         //         ingredient_search_opened = false;
//         //         close_button.style.display = 'none';
//         //     } else {
//         //         search_ingredient_all.style.display = 'flex';
//         //         ingredient_search_opened = true;
//         //         close_button.style.display = 'block';
                
//         //         close_button.addEventListener('click', function () {
//         //             search_ingredient_all.style.display = 'none';
//         //             ingredient_search_opened = false;
//         //             close_button.style.display = 'none';
//         //         });
//         //     }
//         // });
//         let search_ingredient_container = document.querySelector('.search-ingredient-container');
// let search_ingredient_all = document.querySelector('.search-ingredient-list-all');
// let close_button = document.querySelector('.close-ingredient-research');

// search_ingredient_container.addEventListener('click', function () {
//     if (ingredient_search_opened) {
//         search_ingredient_all.style.display = 'none';
//         close_button.style.display = 'none';
//     } else {
//         search_ingredient_all.style.display = 'flex';
//         close_button.style.display = 'block';
//     }
//     ingredient_search_opened = !ingredient_search_opened; // Inverse l'état
// });

// close_button.addEventListener('click', function () {
//     search_ingredient_all.style.display = 'none';
//     close_button.style.display = 'none';
//     ingredient_search_opened = false; // Assure que l'état est fermé
// });


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

            items_ingredients_all.addEventListener('click', function (event) {
                const target = event.target.closest('.ingredient-item');
                if (!target) return;

                target.classList.toggle('selected');
                const item_selected = target.cloneNode(true);
                items_ingredients_selected.appendChild(item_selected);
                set_ingredients_in_local_cache();
            });

            items_ingredients_selected.addEventListener('click', function (event) {
                const target = event.target.closest('.ingredient-item');
                if (!target) return;

                target.remove();
                const ingredient = target.dataset.ingredient;
                const item_to_remove = items_ingredients_all.querySelector(`.ingredient-item[data-ingredient="${ingredient}"]`);
                item_to_remove.classList.remove('selected');
                set_ingredients_in_local_cache();
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
            const button_reset_ingredients = document.querySelector('#search-ingredient-button');

            button_reset_ingredients.addEventListener('click', function () {
                remove_ingredient_from_local_cache();
                remove_selected_ingredients()

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

        let ingredients_array = get_ingredients_params();

        if (ingredients_array.length === 0) {
            ingredients_array = get_ingredients_from_local_cache();
        }

        select_ingredient_OFD(ingredients_array);
        set_ingredients_in_local_cache();
        add_ingredient_listeners();
        add_button_reset_listeners()

    });
}