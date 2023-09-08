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
 * Strings for component 'report_benchmark', language 'fr', version '4.1'.
 *
 * @package     report_benchmark
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminreport'] = 'Benchmark du système';
$string['benchfail'] = '<b>Attention !</b><br />La performance de votre installation de Moodle n\'est pas optimale.';
$string['benchmark'] = 'Benchmark';
$string['benchmark:view'] = 'Consulter le rapport Benchmark';
$string['benchshare'] = 'Partager mon score sur le forum';
$string['benchsuccess'] = '<b>Félicitations !</b><br />La performance de votre installation de Moodle semble parfaite.';
$string['cloadmoreinfo'] = 'Charge le fichier de configuration « config.php »';
$string['cloadname'] = 'Temps de chargement de Moodle';
$string['coursereadmoreinfo'] = 'Lit un cours à plusieurs reprises pour vérifier la vitesse de lecture de la base de données';
$string['coursereadname'] = 'Performance de lecture de cours';
$string['coursewritemoreinfo'] = 'Écrit un cours à plusieurs reprises pour vérifier la vitesse d\'écriture de la base de données';
$string['coursewritename'] = 'Performance d\'écriture de cours';
$string['description'] = 'Description';
$string['duration'] = '{$a} s';
$string['during'] = 'Durée (secondes)';
$string['filereadmoreinfo'] = 'Lit un fichier à plusieurs reprises pour vérifier la vitesse de lecture dans le dossier temporaire de Moodle';
$string['filereadname'] = 'Performance de lecture de fichiers';
$string['filewritemoreinfo'] = 'Écrit un fichier à plusieurs reprises pour vérifier la vitesse d\'écriture dans le dossier temporaire de Moodle';
$string['filewritename'] = 'Performance d\'écriture de fichiers';
$string['info'] = 'La durée de ce benchmark doit être inférieure à 1 minute. Il est stoppé automatiquement après 2 minutes. Veuillez patienter jusqu\'à l\'affichage des résultats.';
$string['infoaverage'] = 'Il est recommandé d\'effectuer ce benchmark plusieurs fois pour obtenir une moyenne significative. Si la performance de votre installation n\'est pas optimale, vous trouverez des recommandation pour l\'améliorer dans la <a href="https://docs.moodle.org/fr/Recommandations_de_performance" target="_blank">documentation de Moodle</a>.';
$string['infodisclaimer'] = 'Ne pas lancer ce benchmark sur une plateforme en production, car cela pourrait dégrader les performances de manière significative.';
$string['infodisclamer'] = 'Il est déconseillé de lancer ce benchmark sur une plateforme en production.';
$string['limit'] = 'Limite acceptable';
$string['loginguestmoreinfo'] = 'Vérifie le temps de chargement de la page de connexion de l\'utilisateur anonyme';
$string['loginguestname'] = 'Temps de connexion du compte utilisateur anonyme';
$string['loginusermoreinfo'] = 'Vérifie le temps de chargement de la page de connexion d\'un compte utilisateur bidon';
$string['loginusername'] = 'Temps de connexion d\'un compte utilisateur bidon';
$string['modulename'] = 'Moodle Benchmark';
$string['modulenameplural'] = 'Moodle Benchmarks';
$string['notificatiopagedownloadmoreinfo'] = 'Charge quelques fois la page de notification de l\'interface d\'administration pour vérifier la vitesse du serveur web';
$string['notificatiopagedownloadname'] = 'Durée de chargement de la page de notification de l\'administration';
$string['over'] = 'Limite critique';
$string['pluginname'] = 'Moodle Benchmark';
$string['points'] = '{$a} points';
$string['privacy:no_data_reason'] = 'Le plugin rapport Benchmark n\'enregistre aucune donnée personnelle. Il accède simplement aux données d\'autres plugins';
$string['processormoreinfo'] = 'Appel d\'une fonction PHP avec une boucle pour vérifier la rapidité du processeur';
$string['processorname'] = 'Rapidité de calcul du processeur';
$string['querytype1moreinfo'] = 'Exécute une requête SQL complexe pour vérifier la vitesse de la base de données';
$string['querytype1name'] = 'Performance de la base de données (n°1)';
$string['querytype2moreinfo'] = 'Exécute une requête SQL complexe pour vérifier la vitesse de la base de données';
$string['querytype2name'] = 'Performance de la base de données (n°2)';
$string['redo'] = 'Relancer le benchmark';
$string['score'] = 'Score';
$string['scoremsg'] = 'Score du benchmark :';
$string['seconde'] = '{$a} s';
$string['slowdatabaselabel'] = 'La base de données semble trop lente.';
$string['slowdatabasesolution'] = '<ul><li>Vérifiez <a href="https://mariadb.com/kb/en/library/mysqlcheck/" target="_blank">l\'intégrité de la base de données</a>.</li><li>Optimisez <a href="https://mariadb.com/kb/en/library/optimization-and-tuning/" target="_blank">la base de données</a>.</li></ul>';
$string['slowharddrivelabel'] = 'Le disque dur semble trop lent.';
$string['slowharddrivesolution'] = '<ul><li>Vérifiez l\'état du disque et/ou du dossier temporaire.</li><li>Changez de disque dur et/ou de dossier temporaire.</li></ul>';
$string['slowprocessorlabel'] = 'Le processeur semble trop lent.';
$string['slowprocessorsolution'] = '<ul><li>Vérifier que votre configuration matérielle soit suffisante pour faire fonctionner Moodle.</li></ul>';
$string['slowserverlabel'] = 'Votre serveur web semble trop lent.';
$string['slowserversolution'] = '<ul><li>Placez votre serveur Apache en mode <a href="https://httpd.apache.org/docs/2.4/en/mpm.html" target="_blank">multi-processing</a> ou passez à <a href="https://nginx.org/" target="_blank">NGinx</a>.</li><li>Si votre Moodle est installé sur votre ordinateur, configurez votre antivirus avec précaution pour qu\'il ne vérifie pas l\'installation de Moodle.</li></ul>';
$string['slowweblabel'] = 'La page de connexion est trop lente à charger.';
$string['slowwebsolution'] = '<ul><li><a href="{$a}" target="_blank">Videz le cache de Moodle</a>.</li></ul>';
$string['start'] = 'Lancer le benchmark';
$string['total'] = 'Durée totale';
