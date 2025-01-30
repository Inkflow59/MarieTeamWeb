<!DOCTYPE html>
<?php
include 'module/header.php';
include 'module/footer.php';
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Recherche de Billet</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Rechercher votre billet</h1>
        <form action="afficher_billet.php" method="post" class="bg-white p-6 rounded shadow-md">
            <div class="mb-4">
                <label for="billet" class="block text-gray-700">Numéro de billet :</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" name="billet" id="billet" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Rechercher</button>
        </form>
    </div>
</body>
</html> 