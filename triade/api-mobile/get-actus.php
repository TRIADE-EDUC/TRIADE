<?php 
session_start();

/**
 * 
 *  Mettre les includes nécessaires
 * 
 * 
 */

include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../librairie_php/db_triade.php");
include_once("../librairie_php/timezone.php");
include_once("../librairie_php/langue-text-fr.php");


/* get-actus.php, le demandeur le consulte en mode GET avec session PHP */

if (isset($_SESSION["connecte"]) && $_SESSION["connecte"] === true && $_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    // Classement des actus du plus récent (ordre d'apparition 0) au plus ancien (ordre d'apparition n)
    $cnx=cnx();
    $data=consultMessAdmin();  // idnews,nom,prenom,date,heure,titre,texte,type,config_video
    Pgclose();
    $nb=count($data);

    function extractYoutubeId($html) {
        if (preg_match('/youtube\.com\/v\/([\w-]+)/i',     $html, $m)) return $m[1];
        if (preg_match('/youtube\.com\/watch\?v=([\w-]+)/i',$html, $m)) return $m[1];
        if (preg_match('/youtube\.com\/embed\/([\w-]+)/i', $html, $m)) return $m[1];
        if (preg_match('/youtu\.be\/([\w-]+)/i',           $html, $m)) return $m[1];
        return null;
    }

    $list = [];
    for($i=0;$i<$nb;$i++) {
        $type   = $data[$i][7];
        $texte  = $data[$i][6];
        $ytId   = ($type === 'video') ? extractYoutubeId($texte) : null;
        $list[] = [
            "id"         => $data[$i][0],
            "type"       => $type,
            "titre"      => $data[$i][5],
            "contenu"    => $texte,
            "date"       => dateForm($data[$i][3]),
            "heure"      => timeForm($data[$i][4]),
            "youtube_id" => $ytId,
        ];
    }
    $actus = [ "nombre" => $nb, "actus" => $list ];
	/*
    $actus = [
        "nombre" => 3, // Nombre d'actualités
        "contenu" => [ // Sont classés en 1er d'abord les DST puis les évènements
            "init" => "init",
            "0" => [ // Ordre d'apparition sur l'interface 
                "id" => 123, // ID du DST
                "type" => "texte", // "texte" ou "video"
                "titre" => "Cross du collège", // Titre de l'actualité
                "contenu" => "Chers élèves, <br> Le cross du collège se déroulera le <b>mardi 16 octobre</b>. <br> Pensez à vous présnter en tenue de sport et à prendre une bouteille d'eau (1L).", // Contenu de l'actualité textuel (au format HTML)
                "date" => "16/09/2024", // Date de publication, au format JJ/MM/AAAA
                "heure" => "10:00", // Heure de publication, au format HH:MM
            ],
            "1" => [ // Ordre d'apparition sur l'interface 
                "id" => 456, // ID du DST
                "type" => "video", // "texte" ou "video"
                "titre" => "Concours de design", // Titre de l'actualité
                "contenu" => "N/A", // Si le contenu est une video => "N/A"
                "date" => "16/09/2024", // Date de publication, au format JJ/MM/AAAA
                "heure" => "10:00", // Heure de publication, au format HH:MM
                "video" => [
                    "type" => "youtube",
                    "lien" => "https://www.youtube.com/watch?v=NGFIJLbL-g8",
                ],
            ],
            "2" => [ // Ordre d'apparition sur l'interface 
                "id" => 456, // ID du DST
                "type" => "video", // "texte" ou "video"
                "titre" => "Ouverture antenne de CANAL+", // Titre de l'actualité
                "contenu" => "N/A", // Si le contenu est une video => "N/A"
                "date" => "16/09/2024", // Date de publication, au format JJ/MM/AAAA
                "heure" => "10:00", // Heure de publication, au format HH:MM
                "video" => [
                    "type" => "classique",
                    "lien" => "https://www.cjoint.com/doc/24_10/NJfoIRevdkV_Jingle-Ouverture-Canal+-carr%C3%A9s-1995-2002.mp4",
                ],
            ],
        ],
	];
*/
    echo json_encode($actus);
}else{ /* Le demandeur ne possède pas de sessison */
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    $message = [ "code" => "403" , "message" => "Accès interdit, vous n'êtes pas autorisé à accéder à ce contenu" ];
    echo json_encode($message);
    die();
}

?>
