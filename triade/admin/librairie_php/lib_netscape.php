<?php
declare(strict_types=1);

include_once "../common/lib_admin.php";
include_once "../common/lib_ecole.php";
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
