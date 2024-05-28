
let ingredients_part = document.querySelectorAll('.form-ingredient');

ingredients_part.forEach((ingredient_part) => {
    let checkboxes = ingredient_part.querySelectorAll('.checkbox-nom-pluriel input[name="choice"]');
    let div_nom_pluriel = ingredient_part.querySelector('.nom-pluriel');

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            if (checkbox.value === 'oui') {
                div_nom_pluriel.style.display = 'block'; 
            } else {
                div_nom_pluriel.style.display = 'none'; 
            }
        });
    });
});




// add more ingredients
let add_ingredient = document.querySelector('.add-more-button');


function addIngredientPart() {
    let ingredients_part = document.querySelectorAll('.form-ingredient');
    let last_ingredient_part = ingredients_part[ingredients_part.length - 1];
    let new_ingredient_part = last_ingredient_part.cloneNode(true);


     // Réinitialiser les valeurs des inputs
     let inputs = new_ingredient_part.querySelectorAll('input');
     inputs.forEach((input) => {
         input.value = ''; // Réinitialiser la valeur à une chaîne vide
     });

     
    let checkboxes = new_ingredient_part.querySelectorAll('.checkbox-nom-pluriel input[name="choice"]');
    let div_nom_pluriel = new_ingredient_part.querySelector('.nom-pluriel');

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            if (checkbox.value === 'oui') {
                div_nom_pluriel.style.display = 'block'; 
            } else {
                div_nom_pluriel.style.display = 'none'; 
            }
        });
    });

    last_ingredient_part.after(new_ingredient_part);
}

add_ingredient.addEventListener('click', (event) => {

    //prevent default
    event.preventDefault();

    addIngredientPart();
});