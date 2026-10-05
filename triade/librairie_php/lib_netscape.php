<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
 *   Site                 : http://www.triade-educ.org
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
declare(strict_types=1);

if (file_exists("../../common/lib_admin.php")) 	include_once "../../common/lib_admin.php";
if (file_exists("../../common/lib_ecole.php")) 	include_once "../../common/lib_ecole.php";
if (file_exists("../common/lib_admin.php")) 	include_once "../common/lib_admin.php";
if (file_exists("../common/lib_ecole.php")) 	include_once "../common/lib_ecole.php";
if (file_exists("./common/lib_admin.php")) 	include_once "./common/lib_admin.php";
if (file_exists("./common/lib_ecole.php")) 	include_once "./common/lib_ecole.php";
?>

<script>
(function () {
    "use strict";

    const supportsModernFeatures =
        typeof fetch === "function" &&
        typeof Promise === "function" &&
        typeof window.localStorage !== "undefined" &&
        typeof window.addEventListener === "function";

    if (!supportsModernFeatures) {
        alert(
            "Votre navigateur est trop ancien pour utiliser Triade dans de bonnes conditions.\n\n" +
            "Merci d'utiliser un navigateur moderne et à jour :\n" +
            "- Chrome\n" +
            "- Firefox\n" +
            "- Edge\n" +
            "- Safari\n\n" +
            "Site officiel : https://www.triade-educ.org\n\n" +
            "L'Équipe Triade"
        );
    }
})();
</script>
