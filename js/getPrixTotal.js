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
            
            // Récupérer la quantité saisie dans l'input associé
            const input = elem.closest('.prix').querySelector('.quantite');
            const quantite = parseInt(input.value) || 0;
            total += prix * quantite;
        }
    });

    // Mettre à jour l'affichage du prix total
    const affichagePrixTotal = document.getElementById('prixTotal');
    if (affichagePrixTotal) {
        affichagePrixTotal.textContent = total.toFixed(2) + ' €';
    }

    return total;
}

// Ajouter les écouteurs d'événements sur tous les inputs de type number
document.querySelectorAll('.quantite').forEach(input => {
    input.addEventListener('input', getPrixTotal);
});

// Appeler getPrixTotal lors du chargement de la page
document.addEventListener('DOMContentLoaded', getPrixTotal);