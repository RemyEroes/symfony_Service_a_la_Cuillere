<?php

function move_file_and_get_filemame($file, $location, $name_wanted, $kernel)
{

    $cleanedName = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '-', $name_wanted));
    $newFilename = $cleanedName . '-' . time() . '.' . $file->guessExtension();

    // Définir le chemin du répertoire de destination
    $destinationDirectory = $kernel->getProjectDir() . '/public/images/' . $location;

    // Déplacer le fichier vers le répertoire approprié
    $file->move($destinationDirectory, $newFilename);

    return $newFilename;
}
