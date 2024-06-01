document.addEventListener('DOMContentLoaded', function() {
    // sur / ingredient/{id}
    const pathPattern = /^\/ingredient\/\d+$/;
    if (pathPattern.test(window.location.pathname)) {
        
        let boutonModifier = document.querySelector('.button-ingredient.modifier');
        let boutonSupprimer = document.querySelector('.button-ingredient.supprimer');

        boutonModifier.addEventListener('click', function() {
            document.querySelector('.ingredient-nom').contentEditable = true;
            document.querySelector('.ingredient-nom').focus();
            document.querySelector('.ingredient-nom').style.backgroundColor = '#f9f9f9';
            document.querySelector('.ingredient-nom').style.border = '1px solid #ffb1b7';
            boutonModifier.style.display = 'none';
            boutonSupprimer.style.display = 'none';
            document.querySelector('.button-ingredient.valider').style.display = 'block';
            document.querySelector('.button-ingredient.annuler').style.display = 'block';
        });











    }
});