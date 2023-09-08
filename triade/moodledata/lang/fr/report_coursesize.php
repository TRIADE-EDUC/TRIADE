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
 * Strings for component 'report_coursesize', language 'fr', version '4.1'.
 *
 * @package     report_coursesize
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allcourses'] = 'Tous les cours';
$string['backupsize'] = 'Taille des sauvegardes';
$string['cachedef_topuserdata'] = 'Informations en cache sur les utilisateurs ayant la plus grande quantité totale de données.';
$string['calcmethod'] = 'Mise à jour du rapport principal';
$string['calcmethodcron'] = 'Tâche programmée';
$string['calcmethodhelp'] = 'S\'il est calculé par une tâche programmée, le rapport sera mis à jour à l\'heure programmée et mettra les résultats en cache pour une consultation ultérieure. Ceci est recommandé par rapport aux calculs instantanés effectués au chargement de la page car il peut s\'agir d\'une requête intensive/lente à exécuter.';
$string['calcmethodlive'] = 'Au chargement de la page';
$string['catsystembackupuse'] = 'Les sauvegardes du système et des catégories pèsent {$a}.';
$string['catsystemuse'] = 'Les sauvegardes du système et des catégories sans les cours ni les utilisateurs pèsent {$a}.';
$string['coursebackupbytes'] = '{$a->backupbytes} octets utilisés par les sauvegardes du cours {$a->shortname}';
$string['coursebytes'] = '{$a->bytes} octets utilisés par le cours {$a->shortname}';
$string['coursereport'] = 'Résumé des types de plugins - À noter : cela peut être différent de la valeur du cours, et probablement plus précis.';
$string['coursesize'] = 'Taille des cours';
$string['coursesize:view'] = 'Voir le rapport des cours';
$string['coursesize_desc'] = '<p>Ce rapport fourni seulement des valeurs indicatives : si un fichier est utilisé plusieurs fois dans un cours, ou dans plusieurs cours, le rapport comptera chaque instance, alors que Moodle ne le stocke qu\'une seule fois sur le disque.</p>';
$string['coursesummary'] = '(voir statistiques)';
$string['diskusage'] = 'Total';
$string['emptycourseshidden'] = 'Les cours qui n\'utilisent pas de stockage de documents n\'apparaissent pas dans ce rapport.';
$string['exportcsv'] = 'Export CSV';
$string['filearea'] = 'Zone de fichiers';
$string['lastupdate'] = '(Dernière mise à jour {$a})';
$string['lastupdatenever'] = 'En attente de la tâche programmée pour générer des statistiques.';
$string['nouserfiles'] = 'Aucun fichier utilisateur n\'a été trouvé.';
$string['numberofusers'] = 'Nombre d\'utilisateurs principaux';
$string['numberofusershelp'] = 'Le nombre d\'utilisateurs principaux que le rapport doit afficher.';
$string['pluginname'] = 'Poids des cours';
$string['privacy:metadata'] = 'Le plugin Poids des cours n\'enregistre aucune donnée personnelle.';
$string['sharedusage'] = 'Usage partagé';
$string['sharedusagecourse'] = 'Environ {$a} est partagé avec d\'autres cours.';
$string['sitefilesusage'] = 'Rapport d\'utilisation des fichiers';
$string['sizepermitted'] = '(Utilisation autorisée {$a} Mo)';
$string['totalsitedata'] = 'Total du répertoire de données : {$a}';
$string['userstopnum'] = 'Utilisateurs (top {$a})';
