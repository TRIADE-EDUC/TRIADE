<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'panopto', language 'fr', version '4.1'.
 *
 * @package     panopto
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['asynchronousmode'] = 'Mode asynchrone';
$string['asynchronousmode_desc'] = 'En mode asynchrone, la demande d’accès qui est soumise à Panopto lorsqu’un utilisateur clique sur un lien vidéo, est mise en file d’attente et exécutée en tâche de fond. Dans des circonstances normales, cette tâche serait simplement exécutée immédiatement, mais dans le cas où toute une classe d\'étudiants tenterait d\'accéder simultanément à une vidéo, le traitement des demandes de cette manière devrait éviter que Moodle ne soit surchargé.<br><br>Afin de faire ce mode possible, vous devrez configurer une tâche cron supplémentaire pour surveiller la file d\'attente des tâches ad hoc, par ex. quelque chose comme :<pre>* * * * * www-data /usr/bin/php /var/www/moodle/admin/tool/task/cli/adhoc_task.php -e --keep-alive=600 > /dev /null</pre>Notez que cet exemple exécute la tâche toutes les minutes et la maintient pendant 10 minutes, ce qui permet de traiter jusqu\'à 10 requêtes en parallèle.<br><br>En cas de doute, laissez cette option désactivée.';
$string['chooseavideo'] = 'Choisir la vidéo Panopto';
$string['crontask'] = 'Module de cours Panopto : effacement des appartenances à un groupe à distance';
$string['deliveryid'] = 'Identifiant de livraison';
$string['deliveryid_help'] = 'Si votre vidéo n’est pas encore disponible via le navigateur Panopto ci-dessus, vous pouvez simplement coller son identifiant de livraison ici.<br><br>L’identifiant de livraison peut être trouvé pour chaque enregistrement dans le portail Panopto via Paramètres.';
$string['modulename'] = 'Vidéo Panopto';
$string['modulename_help'] = 'La ressource Panopto permet à un enseignant de créer une référence à n’importe quelle vidéo Panopto. Choisir simplement la vidéo à l’aide de l’interface de navigation du référentiel et tout étudiant ayant accès à cette ressource dans Moodle pourra regarder la vidéo sur le serveur Panopto.';
$string['modulename_link'] = 'mod/panopto/view';
$string['modulenameplural'] = 'Vidéos Panopto';
$string['nopermissions'] = 'Désolé, vous ne disposez pas des autorisations nécessaires pour visionner cette vidéo Panopto.';
$string['nourl'] = 'Désolé, cette vidéo Panopto n’est pas disponible actuellement. Veuillez réessayer plus tard.';
$string['novideo'] = 'Vous devez soit rechercher et sélectionner une vidéo, soit saisir un ID de livraison Panopto.';
$string['off'] = 'Éteindre';
$string['on'] = 'Allumer';
$string['panopto:addinstance'] = 'Ajouter une nouvelle ressource Panopto';
$string['panopto:view'] = 'Visionner la vidéo Panopto';
$string['pluginadministration'] = 'Administration du module Panopto';
$string['pluginname'] = 'Panopto';
$string['preparing'] = 'Préparation de la vidéo ...';
$string['requiredaccesstime'] = 'Délai d\'accès';
$string['requiredaccesstime_desc'] = 'Lors de la tentative de visionnement de la vidéo Panopto, tout utilisateur pouvant accéder au module sera ajouté au groupe dédié sur le site Panopto et redirigé pour visionner la vidéo sur Panopto. Ce paramètre définit la durée (en heures) pendant laquelle l’utilisateur pourra accéder à la vidéo via le site Panopto. L’accès sera saisi dans un délai défini ci-dessus, pour visionner la vidéo, l’utilisateur devra y accéder à nouveau depuis Moodle. S’il est défini sur illimité, l’utilisateur pourra visionner la vidéo via le site Panopto jusqu’à ce que le module de cours existe, qu’il soit toujours inscrit et qu’il puisse accéder au module ou non.';
$string['unlimited'] = 'Illimité';
$string['usereposettings'] = 'Ce plugin nécessite l’installation et la <a href="{$a}">configuration</a> du plugin de référentiel Panopto.';
$string['video'] = 'Vidéo';
