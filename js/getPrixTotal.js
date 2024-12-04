function getPrixTotal() {
    // Récupérer tous les éléments avec la classe "prix"
    const elemsPrix = document.querySelectorAll('.prix .type');
    let total = 0;

    // Parcourir chaque élément prix
    elemsPrix.forEach(elem => {
        // Vérifier si l'élément contient un prix (€)
        if (elem.textContent.includes('€')) {
            // Extraire le nombre du texte (ex: "99 €" -> 99)
            const prix = parseFloat(elem.textContent.replace('€', ''));
            
            // Récupérer la quantité sélectionnée dans le select associé
            const select = elem.nextElementSibling;
            if (select && select.tagName === 'SELECT') {
                const quantite = parseInt(select.value) || 0;
                total += prix * quantite;
            }
        }
    });

    // Mettre à jour l'affichage du prix total
    const affichagePrixTotal = document.querySelector('.casePrix .type:last-child');
    if (affichagePrixTotal) {
        affichagePrixTotal.textContent = total.toFixed(2) + ' €';
    }

    return total;
}

// Ajouter les écouteurs d'événements sur tous les selects
document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', getPrixTotal);
});
