<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        // Lecture de l'entête
        $header = fgetcsv($handle, 1000, ';');
        $data = [];

        // Lecture des lignes
        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            $data[] = array_combine($header, $row);
        }

        fclose($handle);

        // Exemple : afficher les données pour vérifier
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        // Exemple : insertion en base MySQL avec PDO
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=edt;charset=utf8', 'user', 'password', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->prepare("
                INSERT INTO emplois_du_temps
                (classe, date, heure_debut, heure_fin, matiere, professeur, salle, groupe)
                VALUES (:Classe, :Date, :Heure_debut, :Heure_fin, :Matiere, :Professeur, :Salle, :Groupe)
            ");

            foreach ($data as $ligne) {
                $stmt->execute($ligne);
            }

            echo "Import terminé avec succès !";
        } catch (PDOException $e) {
            echo "Erreur base de données : " . $e->getMessage();
        }
    } else {
        echo "Impossible d'ouvrir le fichier CSV.";
    }
} else {
    echo "Aucun fichier reçu.";
}
?>
