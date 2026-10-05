<?php
$version = (isset($_GET['version'])) ? intval($_GET['version']) : 0;

$available_changelogs = glob("./changelogs*.txt");
if( !is_countable($available_changelogs) ) {
    $available_changelogs = [];
}

$available_versions = [];
for( $i = 0; $i < count($available_changelogs) ; $i++) {
    $number = intval(str_replace(["./changelogs", ".txt"], "", $available_changelogs[$i]));
    $available_versions[$number] = $available_changelogs[$i];
}
$available_changelogs = null;
krsort($available_versions);

if(!array_key_exists($version, $available_versions)) {
    $version = 0;
}

$page_tpl = '
        <!DOCTYPE html>
        <html>
            <head>
            <meta charset="UTF-8">
            <title>Changelogs</title>
            </head>
            <body>
                <h1>Changelogs</h1>
                <hr />
                <ul>
                <!-- row_tpl -->
                </ul>
                <hr />
            </body>
        </html>';

$row_tpl = '<li><a href="./changelogs.php?version=<!-- version -->" target="_blank" ><!-- label --></li>';

$html_page = '';

switch(true) {

    // Pas de version
    case ( 0 == count($available_versions) ) :

        $html_page = $page_tpl;
        $html_page = str_replace("<!-- row_tpl -->", "<li>Aucun changelog n'est disponible.</li>", $html_page);
        break;

    // Liste des versions
    case (0 == $version) :

        $html_page = $page_tpl;
        foreach($available_versions as $k => $v) {
            $row_content = str_replace(["<!-- version -->", "<!-- label -->"], [$k, "Version ".$k], $row_tpl);
            $html_page = str_replace("<!-- row_tpl -->", $row_content."<!-- row_tpl -->", $html_page);
        }
        break;

    default :
        header("Content-Type: text/plain; charset=utf-8");
        $html_page = file_get_contents("./changelogs".$version.".txt");
        break;
}

echo $html_page;
