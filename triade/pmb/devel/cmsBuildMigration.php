<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cmsBuildMigration.php,v 1.2 2023/11/28 15:21:07 qvarin Exp $

use Pmb\CMS\Library\ParserCMS\Container;
use Pmb\CMS\Library\ParserCMS\Zone;

$base_path = './..';
$base_noheader = 1;
$base_nocheck = 0;
$base_nobody = 1;
$base_nosession = 1;

$_GET = [];
$_POST = [];
$_FILES = [];

require_once $base_path . '/includes/init.inc.php';

function show($element)
{
    if ($element instanceof Zone) {
        $classlist = "zone";
        $type = "<strong style='color:green'>[Zone]</strong>";
    } else {
        $classlist = "cadre";
        $type = "<strong style='color:orange'>[Cadre]</strong>";
    }

    if ($element->isHidden) {
        $type .= "<strong style='color:red'>[Masqu&eacute;]</strong>";
    }

    echo "<div class='{$classlist}'>";
    echo "<p>{$type} {$element->id}</p>";
    if ($element instanceof Zone) {
        echo "<div class='children'>";
        $child = $element->getFirstChild();
        if ($child) {
            while ($child) {
                show($child);
                $child = $child->getNext();
            }
        } else {
            echo "<p><i><b>**</b>Aucun enfant<b>**</b></i></p>";
        }
        echo "</div>";
    }
    echo "</div>";
}

$container = new Container();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='SHORTCUT ICON' href='../images/favicon.ico'>
    <title>Migration Portail</title>
    <style>
        pre.result {
            width: 600px;
            overflow: hidden;
            padding: 2%;
            background-color: aliceblue;
        }

        pre.result p {
            margin: 0;
        }

        .cadre+.zone {
            margin-top: 2%;
        }

        .children {
            border-left: 1px solid gray;
            border-bottom: 1px solid gray;
            padding: 1% 2%;
            padding-right: 0;
            margin-bottom: 2%;
        }

        .description-title {
            text-decoration: underline;
        }

        .description {
            border: 1px solid gray;
            padding: 1% 2%;
            width: 600px;
        }

        .description dd {
            font-style: italic;
        }
    </style>
</head>

<body>
    <a href="../cms.php?categ=editorial&sub=list">Retour</a>

    <h1>Migration du Portail</h1>
    <p>Ce script est l&agrave; pour vous montrer l'ordre des cadres qui sera utilis&eacute; pour la refonte portail.</p>

    <dl>
        <dt>Nom du portail :</dt>
        <dd>
            <strong><?= htmlentities($container->cmsName) ?></strong>
        </dd>

        <dt>Version du portail :</dt>
        <dd>
            <strong><?= $container->parserCMS->cmsVersion ?></strong>
        </dd>
    </dl>

    <h2>R&eacute;sultats :</h2>
    <p class="description-title">Description :</p>
    <dl class="description">
        <dt><strong style='color:green'>[Zone]</strong></dt>
        <dd>&Eacute;lement consid&eacute;r&eacute; comme une zone pour la refonte portail.</dd>

        <dt><strong style='color:orange'>[Cadre]</strong></dt>
        <dd>&Eacute;lement consid&eacute;r&eacute; comme un cadre pour la refonte portail.</dd>

        <dt><strong style='color:red'>[Masqu&eacute;]</strong></dt>
        <dd>&Eacute;lement obligatoire et non pr&eacute;sent pour la refonte portail, donc masqu&eacute;.</dd>
    </dl>
    <pre class="result"><?php show($container->zone); ?></pre>
</body>

</html>