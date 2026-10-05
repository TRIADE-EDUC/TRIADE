<?php
session_start();
error_reporting(0);
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("../librairie_php/timezone.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Documentation API</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Documentation API v1</font></b></td></tr>
<tr id="cadreCentral0" ><td > <p align="left"><font color="#000000">
<?php
$chapitres = [
    'authentification' => 'Étape 1 : Authentification',
    'eleves' => '2.1 - Récupérer tous les élèves',
    'notes' => '2.2 - Récupérer les notes d\'un élève par période',
    'edt' => '2.3 - Récupérer l\'emploi du temps',
    'prof' => '2.4 - Récupérer le personnel',
    'classes' => '2.5 - Récupérer les classes',
    'matieres' => '2.6 - Récupérer les matières',
    'affectation' => '2.7 - Récupérer les affectations professeurs/classes',
    'absencesretards' => '2.8 - Récupérer les absences et retards d\'un élève',
    'sanctionsdisciplines' => '2.9 - Récupérer les sanctions et retenues d\'un élève',
    'ue' => '2.10 - Récupérer les unités d\'enseignement (UE)',
    'securite' => 'Contrôles de sécurité',
];
$chapitre = isset($_GET['chapitre']) ? $_GET['chapitre'] : '';

function printLink($key, $title) {
    $url = "api_doc_chapitre.php?chapitre=" . urlencode($key);
    return "<li><a href=\"$url\">$title</a></li>";
}

if (!$chapitre || !isset($chapitres[$chapitre])) {
    echo "<h3>Chapitres de la documentation API</h3>";
    echo "<p>Choisissez un chapitre pour afficher les détails.</p>";
    echo "<ul>";
    foreach ($chapitres as $key => $title) {
        echo printLink($key, $title);
    }
    echo "</ul>";
    echo "<p><a href=\"config_api.php\">&laquo; Retour à la configuration API</a></p>";
} else {
    $title = $chapitres[$chapitre];
    echo "<h3>$title</h3>";
    echo "<p><a href=\"api_doc_chapitre.php\">&laquo; Retour à la liste des chapitres</a></p>";

    switch ($chapitre) {
        case 'authentification':
            echo "<p><strong>Endpoint :</strong> <code>POST /api-v1/get-session.php</code></p>";
            echo "<p><strong>Description :</strong> Ouvre une session API en vérifiant la clef du client et son IP.</p>";
            echo "<p><strong>Méthode :</strong> POST avec body JSON.</p>";
            echo "<p><strong>Body :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{" . "\n  \"key\": \"clef_api_generee\"\n}</pre>";
            echo "<p><strong>Réponses :</strong></p>";
            echo "<ul><li><code>200 OK</code> avec <code>session_id</code></li>";
            echo "<li><code>403</code> : clef invalide/inactive</li>";
            echo "<li><code>400</code> : clef manquante</li>";
            echo "<li><code>405</code> : mauvaise méthode</li></ul>";
            break;
        case 'eleves':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-eleves.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère la liste de tous les élèves.</p>";
            echo "<p><strong>Paramètres :</strong> Aucun (autre que la session).</p>";
            echo "<p><strong>Réponse :</strong> Liste complète des élèves avec tous les champs, sauf les mots de passe.</p>";
            echo "<p><strong>Exemple :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{" . "\n  \"status\": \"success\",\n  \"data\": [\n    {\n      \"elev_id\": 123,\n      \"nom\": \"DUPONT\",\n      \"prenom\": \"Jean\"\n    }\n  ]\n}</pre>";
            break;
        case 'notes':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-notes.php?idpers=[id]&annee=[AAAA]&mois=[MM]</code></p>";
            echo "<p><strong>Description :</strong> Récupère les notes d'un élève pour une période donnée.</p>";
            echo "<p><strong>Paramètres :</strong></p>";
            echo "<ul>";
            echo "<li><code>idpers</code> : ID de l'élève (obligatoire)</li>";
            echo "<li><code>annee</code> : année au format AAAA (obligatoire)</li>";
            echo "<li><code>mois</code> : mois au format MM (obligatoire)</li>";
            echo "</ul>";
            echo "<p><strong>Exemple :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{" . "\n  \"status\": \"success\",\n  \"nombre\": 5,\n  \"contenu\": [\n    {\n      \"date\": \"10-02-2025\",\n      \"matiere\": \"FRANCAIS\",\n      \"note\": \"15\"\n    }\n  ]\n}</pre>";
            break;
        case 'edt':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-edt.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère les séances de l'emploi du temps pour une période donnée. Au moins un filtre (idclasse, idprof ou idressource) est obligatoire.</p>";
            echo "<p><strong>Paramètres :</strong></p>";
            echo "<ul>";
            echo "<li><code>idclasse</code> : ID de la classe (au moins un des trois filtres est obligatoire)</li>";
            echo "<li><code>idprof</code> : ID du professeur</li>";
            echo "<li><code>idressource</code> : ID de la salle/ressource</li>";
            echo "<li><code>date_debut</code> : date de début au format AAAA-MM-JJ (optionnel, défaut : lundi de la semaine courante)</li>";
            echo "<li><code>date_fin</code> : date de fin au format AAAA-MM-JJ (optionnel, défaut : dimanche de la semaine courante)</li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : séances trouvées</li>";
            echo "<li><code>400</code> : filtre manquant ou format de date invalide</li>";
            echo "<li><code>404</code> : aucune séance pour cette période</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>EDT d'une classe pour la semaine courante :<br><code>GET /api-v1/get-edt.php?idclasse=12</code></li>";
            echo "<li>EDT d'un professeur sur une période :<br><code>GET /api-v1/get-edt.php?idprof=5&amp;date_debut=2026-05-05&amp;date_fin=2026-05-09</code></li>";
            echo "<li>EDT d'une salle sur une journée :<br><code>GET /api-v1/get-edt.php?idressource=3&amp;date_debut=2026-05-05&amp;date_fin=2026-05-05</code></li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 3,\n  \"date_debut\": \"2026-04-28\",\n  \"date_fin\": \"2026-05-04\",\n  \"contenu\": [\n    {\n      \"id\": 42,\n      \"date\": \"2026-04-28\",\n      \"heuredebut\": \"08:00\",\n      \"heurefin\": \"09:00\",\n      \"matiere\": \"FRANCAIS\",\n      \"enseignant\": \"M. TAESCH Eric\",\n      \"classe\": \"1ERE A\",\n      \"salle\": \"Salle 101\",\n      \"groupe\": null,\n      \"annule\": false\n    }\n  ]\n}</pre>";
            break;
        case 'prof':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-prof.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère la liste du personnel. Les champs sensibles (mot de passe, clef, identifiant) sont automatiquement masqués. Le personnel désactivé (<code>offline = 1</code>) est exclu.</p>";
            echo "<p><strong>Paramètres optionnels :</strong></p>";
            echo "<ul>";
            echo "<li><code>id</code> : ID d'un personnel précis (ex: <code>?id=5</code>)</li>";
            echo "<li><code>type</code> : filtre par type de personnel :</li>";
            echo "<ul>";
            echo "<li><code>ENS</code> — Enseignant</li>";
            echo "<li><code>ADM</code> — Administratif</li>";
            echo "<li><code>PER</code> — Personnel</li>";
            echo "<li><code>TUT</code> — Tuteur</li>";
            echo "<li><code>MVS</code> — Maître de stage</li>";
            echo "</ul>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>Tous les enseignants : <code>GET /api-v1/get-prof.php?type=ENS</code></li>";
            echo "<li>Un personnel précis : <code>GET /api-v1/get-prof.php?id=5</code></li>";
            echo "<li>Tout le personnel : <code>GET /api-v1/get-prof.php</code></li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : personnel trouvé</li>";
            echo "<li><code>404</code> : aucun personnel trouvé</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 2,\n  \"data\": [\n    {\n      \"pers_id\": 5,\n      \"nom\": \"DUPONT\",\n      \"prenom\": \"Marie\",\n      \"type_pers\": \"ENS\",\n      \"civ\": 2,\n      \"email\": \"m.dupont@ecole.fr\",\n      \"tel\": \"0123456789\",\n      \"tel_port\": \"0612345678\",\n      \"qualite\": \"Professeur de Français\",\n      \"offline\": 0\n    }\n  ]\n}</pre>";
            break;
        case 'classes':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-classes.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère la liste des classes actives (<code>offline = 0</code>).</p>";
            echo "<p><strong>Paramètres optionnels :</strong></p>";
            echo "<ul>";
            echo "<li><code>id</code> : ID d'une classe précise (ex: <code>?id=3</code>)</li>";
            echo "<li><code>niveau</code> : filtre par niveau (ex: <code>?niveau=BAC</code>)</li>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>Toutes les classes : <code>GET /api-v1/get-classes.php</code></li>";
            echo "<li>Par niveau : <code>GET /api-v1/get-classes.php?niveau=BAC</code></li>";
            echo "<li>Une classe précise : <code>GET /api-v1/get-classes.php?id=3</code></li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : classes trouvées</li>";
            echo "<li><code>404</code> : aucune classe trouvée</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 2,\n  \"data\": [\n    {\n      \"code_class\": 3,\n      \"libelle\": \"1ERE A\",\n      \"desclong\": \"Première A\",\n      \"offline\": 0,\n      \"niveau\": \"BAC\",\n      \"specification\": \"\",\n      \"langueclasse\": \"fr\"\n    }\n  ]\n}</pre>";
            break;
        case 'matieres':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-matieres.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère la liste des matières actives (<code>offline = 0</code>).</p>";
            echo "<p><strong>Paramètres optionnels :</strong></p>";
            echo "<ul>";
            echo "<li><code>id</code> : ID d'une matière précise (ex: <code>?id=5</code>)</li>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>Toutes les matières : <code>GET /api-v1/get-matieres.php</code></li>";
            echo "<li>Une matière précise : <code>GET /api-v1/get-matieres.php?id=5</code></li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : matières trouvées</li>";
            echo "<li><code>404</code> : aucune matière trouvée</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 2,\n  \"data\": [\n    {\n      \"code_mat\": 5,\n      \"libelle\": \"MATHEMATIQUES\",\n      \"sous_matiere\": \"\",\n      \"offline\": 0,\n      \"couleur\": \"#3399FF\",\n      \"libelle_long\": \"Mathématiques\",\n      \"code_matiere\": \"MATH\",\n      \"libelle_en\": \"Mathematics\"\n    }\n  ]\n}</pre>";
            break;
        case 'affectation':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-affectation.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère les affectations professeurs/classes/matières (table <code>tria_affectations</code>). Les noms du professeur, de la classe et de la matière sont inclus via jointure.</p>";
            echo "<p><strong>Paramètres optionnels :</strong></p>";
            echo "<ul>";
            echo "<li><code>idprof</code> : filtre par ID professeur (ex: <code>?idprof=5</code>)</li>";
            echo "<li><code>idclasse</code> : filtre par ID classe (ex: <code>?idclasse=3</code>)</li>";
            echo "<li><code>idmatiere</code> : filtre par ID matière (ex: <code>?idmatiere=7</code>)</li>";
            echo "<li><code>annee_scolaire</code> : filtre par année scolaire (ex: <code>?annee_scolaire=2025-2026</code>)</li>";
            echo "<li><code>trim</code> : filtre par trimestre (ex: <code>?trim=T1</code> ou <code>?trim=tous</code>)</li>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>Toutes les affectations : <code>GET /api-v1/get-affectation.php</code></li>";
            echo "<li>Par année scolaire : <code>GET /api-v1/get-affectation.php?annee_scolaire=2025-2026</code></li>";
            echo "<li>Classes et matières d'un professeur : <code>GET /api-v1/get-affectation.php?idprof=5</code></li>";
            echo "<li>Professeurs d'une classe : <code>GET /api-v1/get-affectation.php?idclasse=3</code></li>";
            echo "<li>Affectations d'une matière pour une classe : <code>GET /api-v1/get-affectation.php?idclasse=3&amp;idmatiere=7</code></li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : affectations trouvées</li>";
            echo "<li><code>404</code> : aucune affectation trouvée</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 2,\n  \"data\": [\n    {\n      \"code_prof\": 5,\n      \"code_classe\": 3,\n      \"code_matiere\": 7,\n      \"annee_scolaire\": \"2025-2026\",\n      \"trim\": \"tous\",\n      \"coef\": \"1.00\",\n      \"nb_heure\": \"3h\",\n      \"ordre_affichage\": 1,\n      \"code_groupe\": null,\n      \"langue\": null,\n      \"visubull\": 1,\n      \"prof_nom\": \"DUPONT\",\n      \"prof_prenom\": \"Marie\",\n      \"prof_type\": \"ENS\",\n      \"classe_libelle\": \"1ERE A\",\n      \"classe_niveau\": \"BAC\",\n      \"matiere_libelle\": \"MATHEMATIQUES\"\n    }\n  ]\n}</pre>";
            break;
        case 'absencesretards':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-absences-retards.php?idpers=[id]&annee=[AAAA]</code></p>";
            echo "<p><strong>Description :</strong> Récupère les absences et retards d'un élève pour l'année scolaire spécifiée.</p>";
            echo "<p><strong>Paramètres :</strong></p>";
            echo "<ul>";
            echo "<li><code>idpers</code> : ID de l'élève (obligatoire)</li>";
            echo "<li><code>annee</code> : année de début de l'année scolaire au format AAAA (optionnel, ex. 2025)</li>";
            echo "</ul>";
            echo "<p><strong>Exemple :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{" . "\n  \"status\": \"success\",\n  \"idpers\": 123,\n  \"annee\": \"2025 - 2026\",\n  \"nombre_absences\": 2,\n  \"nombre_retards\": 1,\n  \"absences\": [\n    {\n      \"date\": \"10-09-2025\",\n      \"duree\": \"1h30\",\n      \"motif\": \"Maladie\"\n    }\n  ],\n  \"retards\": [\n    {\n      \"date\": \"12-09-2025\",\n      \"heure\": \"08:10\",\n      \"motif\": \"Transport\"\n    }\n  ]\n}</pre>";
            break;
        case 'sanctionsdisciplines':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-sanctions-disciplines.php?idpers=[id]&annee=[AAAA]</code></p>";
            echo "<p><strong>Description :</strong> Récupère les sanctions et retenues d'un élève pour l'année scolaire spécifiée.</p>";
            echo "<p><strong>Paramètres :</strong></p>";
            echo "<ul>";
            echo "<li><code>idpers</code> : ID de l'élève (obligatoire)</li>";
            echo "<li><code>annee</code> : année de début de l'année scolaire au format AAAA (optionnel, ex. 2025)</li>";
            echo "</ul>";
            echo "<p><strong>Exemple :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{" . "\n  \"status\": \"success\",\n  \"idpers\": 123,\n  \"annee\": \"2025 - 2026\",\n  \"nombre_sanctions\": 1,\n  \"nombre_retenues\": 1,\n  \"sanctions\": [\n    {\n      \"date\": \"15-11-2025\",\n      \"auteur\": \"Mme Dupont\",\n      \"categorie\": \"Avertissement\",\n      \"type\": \"Avertissement\",\n      \"motif\": \"Violence verbale\",\n      \"devoir\": \"Rédiger une lettre d'excuses\",\n      \"description_fait\": \"Agression verbale d'un camarade\",\n      \"signature_parent\": \"non\",\n      \"origine\": \"direction\"\n    }\n  ],\n  \"retenues\": [\n    {\n      \"date\": \"20-11-2025\",\n      \"heure\": \"16:00\",\n      \"duree\": \"01:30\",\n      \"motif\": \"Non-respect des règles\",\n      \"categorie\": \"Retenue\",\n      \"type\": \"Retenue\",\n      \"auteur\": \"M. Martin\",\n      \"devoir\": \"Préparer un exposé sur le règlement intérieur\",\n      \"description_fait\": \"Retenue disciplinaire pour incivilité\",\n      \"effectue\": \"oui\",\n      \"signature_parent\": \"oui\",\n      \"origine\": \"professeur principal\",\n      \"date_saisie\": \"20-11-2025\",\n      \"repport_du\": null\n    }\n  ]\n}</pre>";
            break;
        case 'ue':
            echo "<p><strong>Endpoint :</strong> <code>GET /api-v1/get-UE.php</code></p>";
            echo "<p><strong>Description :</strong> Récupère les unités d'enseignement (table <code>tria_ue</code>) avec leurs matières et enseignants associés (table <code>tria_ue_detail</code>).</p>";
            echo "<p><strong>Paramètres optionnels :</strong></p>";
            echo "<ul>";
            echo "<li><code>id</code> : code_ue d'une UE précise (ex: <code>?id=1</code>)</li>";
            echo "<li><code>idclasse</code> : filtre par ID classe (ex: <code>?idclasse=3</code>)</li>";
            echo "<li><code>annee_scolaire</code> : filtre par année scolaire (ex: <code>?annee_scolaire=2025-2026</code>)</li>";
            echo "<li><code>semestre</code> : filtre par semestre (ex: <code>?semestre=1</code>)</li>";
            echo "</ul>";
            echo "<p><strong>Exemples de requêtes :</strong></p>";
            echo "<ul>";
            echo "<li>Toutes les UE : <code>GET /api-v1/get-UE.php</code></li>";
            echo "<li>UE d'une classe pour une année : <code>GET /api-v1/get-UE.php?idclasse=3&amp;annee_scolaire=2025-2026</code></li>";
            echo "<li>UE d'un semestre : <code>GET /api-v1/get-UE.php?idclasse=3&amp;semestre=1</code></li>";
            echo "</ul>";
            echo "<p><strong>Codes retour :</strong></p>";
            echo "<ul>";
            echo "<li><code>200</code> : UE trouvées</li>";
            echo "<li><code>404</code> : aucune UE trouvée</li>";
            echo "<li><code>405</code> : méthode non autorisée</li>";
            echo "</ul>";
            echo "<p><strong>Exemple de réponse :</strong></p>";
            echo "<pre style=\"background: #f0f0f0; padding: 10px; border-radius: 5px;\">{\n  \"status\": \"success\",\n  \"nombre\": 1,\n  \"data\": [\n    {\n      \"code_ue\": 1,\n      \"num_ue\": 1,\n      \"nom_ue\": \"Sciences et Techniques\",\n      \"nom_ue_en\": \"Science and Technology\",\n      \"matricule_ue\": \"UE-SCI-1\",\n      \"semestre\": 1,\n      \"coef_ue\": \"3\",\n      \"ects_ue\": \"6\",\n      \"annee_scolaire\": \"2025-2026\",\n      \"code_classe\": 3,\n      \"classe_libelle\": \"BTS SIO\",\n      \"classe_niveau\": \"BTS\",\n      \"idpers_profp\": 5,\n      \"profp_nom\": \"DUPONT\",\n      \"profp_prenom\": \"Marie\",\n      \"matieres\": [\n        {\n          \"code_ue_detail\": 12,\n          \"code_ue\": 1,\n          \"code_matiere\": 7,\n          \"code_enseignant\": 5,\n          \"code_idgroupe\": 0,\n          \"matiere_libelle\": \"MATHEMATIQUES\",\n          \"enseignant_nom\": \"DUPONT\",\n          \"enseignant_prenom\": \"Marie\"\n        }\n      ]\n    }\n  ]\n}</pre>";
            break;
        case 'securite':
            echo "<p>Points importants de sécurité :</p>";
            echo "<ul>";
            echo "<li>L'IP du client doit correspondre à celle enregistrée dans la table d'accès (ou être <code>*</code> pour autoriser toutes les IPs).</li>";
            echo "<li>La clef doit être active dans la base de données.</li>";
            echo "<li>Une session API valide doit être ouverte avant toute requête de données.</li>";
            echo "<li>Les données sensibles (mots de passe) sont automatiquement masquées.</li>";
            echo "</ul>";
            break;
    }
    echo "<p><a href=\"config_api.php\">&laquo; Retour à la configuration API</a></p>";
}
?>
</font></p>
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
