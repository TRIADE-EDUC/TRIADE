<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

// Emulation de register_globals à off
function unregister_GLOBALS() {
    if (!ini_get('register_globals')) {
        return;
    }

    // Vous pouvez vouloir modifier cela pour avoir une erreur plus jolie
    if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
    die('Tentative d\'effacement des GLOBALS détectée');
    }

    // Les variables à ne jamais effacer
    $noUnset = array('GLOBALS',  '_GET',
    '_POST',    '_COOKIE',
    '_REQUEST', '_SERVER',
    '_ENV',     '_FILES');

    $input = array_merge($_GET,    $_POST,
    $_COOKIE, $_SERVER,
    $_ENV,    $_FILES,
    isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

    foreach ($input as $k => $v) {
        if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {
            unset($GLOBALS[$k]);
        }
    }
}

unregister_GLOBALS();

?>