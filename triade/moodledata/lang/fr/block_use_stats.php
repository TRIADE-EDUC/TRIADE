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
 * Strings for component 'block_use_stats', language 'fr', version '4.1'.
 *
 * @package     block_use_stats
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activetrackingparams'] = 'Activer les paramètres de suivi';
$string['activities'] = 'Activités';
$string['allowrule'] = 'Autoriser l\'envoi lors de la correspondance de la règle';
$string['allusers'] = 'Tous les utilisateurs';
$string['blockdisplay'] = 'Paramétrage de l\'affichage du bloc';
$string['blockname'] = 'Mesure d\'activité';
$string['byname'] = 'Par nom';
$string['bytimedesc'] = 'Par heure';
$string['cachedef_aggregate'] = 'Synthèses';
$string['capabilitycontrol'] = 'Capacité';
$string['configbacktrackmode'] = 'Mode de retour en arrière';
$string['configbacktrackmode_desc'] = 'Sélectionner le mode du retour en arrière.';
$string['configbacktracksource'] = 'Source de retour en arrière';
$string['configbacktracksource_desc'] = 'Sélectionner qui indique la référence de temps de retour en arrière.';
$string['configcalendarskin'] = 'Apparence du calendrier';
$string['configcalendarskin_desc'] = 'Charger l\'apparence du calendrier.';
$string['configcustomtagselect'] = 'Sélectionnez une balise personnalisée';
$string['configcustomtagselect_desc'] = 'Cette requête doit retourner un résultat unique par ligne du journal. Ce résultat va nourrir la colonne balise personnalisée {$a}.';
$string['configdisplayactivitytimeonly'] = 'Choisir le temps de référence à afficher';
$string['configdisplayactivitytimeonly_desc'] = 'Vous pouvez choisir le temps de référence à afficher';
$string['configdisplayothertime'] = 'Afficher la durée « hors cours »';
$string['configdisplayothertime_desc'] = 'Est défini, affiche la ligne durée « hors cours »';
$string['configenablecompilecube'] = 'Activer la compilation de cubes';
$string['configenablecompilecube_desc'] = 'Lorsqu\'elles sont activées, les dimensions supplémentaires sont calculées en utilisant les sélections définies';
$string['configenrolmentfilter'] = 'Filtrer les périodes inscrites';
$string['configenrolmentfilter_desc'] = 'En cas d\'activation, les journaux seront analysés à partir de la première des deux dates entre l\'activation de l\'inscription et le début du cours. S\'il est désactivé, le début du cours sera la seule limite la plus ancienne.';
$string['configfilterdisplayunder'] = 'Filtrer l\'affichage sous';
$string['configfilterdisplayunder_desc'] = 'Si la valeur n\'est pas nulle, seuls les temps de parcours supérieurs à la limite donnée (en secondes) seront affichés dans le bloc';
$string['configfromwhen'] = 'Depuis';
$string['configfromwhen_desc'] = 'Période de compilation (en jours jusqu\'à aujourd\'hui)';
$string['configkeepalivecontrol'] = 'Méthode de contrôle';
$string['configkeepalivecontrol_desc'] = 'données internes utilisées pour contrôler la capacité d\'envoi';
$string['configkeepalivecontrolvalue'] = 'Nom de l\'élément de contrôle';
$string['configkeepalivecontrolvalue_desc'] = 'correspondra à la règle si la capacité est autorisée ou si le champ de profil n\'a pas de valeur nulle. Le paramètre par défaut exclut les administrateurs.';
$string['configkeepalivedelay'] = 'Durée d\'une session';
$string['configkeepalivedelay_desc'] = 'Intervalle entre deux traces de durée de journal pour les utilisateurs connectés (secondes). Garder aussi grand que possible pour réduire la charge du serveur lorsque de nombreux utilisateurs sont connectés, tout en conservant des mesures d\'activités cohérentes.';
$string['configkeepaliverule'] = 'Envoyer keepalive si';
$string['configkeepaliverule_desc'] = 'Règle pour contrôler l\'envoi ajax keepalive';
$string['configlastcompiled'] = 'Date du dernier enregistrement compilé';
$string['configlastcompiled_desc'] = 'En changeant cette date, le cron recalculera tous les journaux postérieurs à la date indiquée';
$string['configlastpingcredit'] = 'Crédit-temps supplémentaire sur le dernier ping';
$string['configlastpingcredit_desc'] = 'Ce laps de temps (en minutes) sera systématiquement ajouté au journal de suivi pour chaque fermeture de session ou discontinuité supposée';
$string['configonesessionpercourse'] = 'Une session par cours';
$string['configonesessionpercourse_desc'] = 'Lorsqu\'elle est activée, utiliser les statistiques interrompra les sessions chaque fois que la piste change le cours actuel. Si elle est désactivée, une session représente une séquence de travail pouvant utiliser plusieurs cours.';
$string['configthreshold'] = 'Seuil';
$string['configthreshold_desc'] = 'Seuil de continuité d\'activité (en minutes) . Au-dessus de ce temps de pause entre deux marques successives dans le journal, l\'utilisateur est considéré comme déconnecté. Un temps arbitraire "Dernier Crédit de Ping" sera ajouté au compteur temps .';
$string['datacubing'] = 'Cube de données';
$string['declaredtime'] = 'Temps déclaré';
$string['denyrule'] = 'Autoriser l\'envoi sauf règle de correspondance';
$string['dimensionitem'] = 'Classes observables';
$string['displayactivitiestime'] = 'Uniquement le temps consacré aux activités effectives du cours';
$string['displaycoursetime'] = 'Cours en temps réel (tout le temps passé dans tous les contextes du cours)';
$string['errornorecords'] = 'Aucune information de suivi';
$string['eventscount'] = 'Affichages';
$string['eventusestatskeepalive'] = 'Durée de session';
$string['fixedchoice'] = 'Paramètres forcés à la date de début du cours / compte';
$string['fixeddate'] = 'À partir d\'une date de référence fixe';
$string['from'] = 'Depuis&ensp;';
$string['fromrange'] = 'De&ensp;';
$string['go'] = 'C\'est parti !';
$string['hidecourselist'] = 'Masquer les temps de cours';
$string['isfiltered'] = 'Seules les durées de plus de {$a} secondes sont prises en compte';
$string['keepuseralive'] = 'L\'utilisateur {$a} a une session en cours';
$string['loganalysisparams'] = 'Paramètres des Journaux d\'analyse';
$string['modulename'] = 'Suivi d\'activité';
$string['noavailablelogs'] = 'Pas d\'enregistrement disponible';
$string['onthismoodlefrom'] = 'ici jusque';
$string['other'] = 'Autres temps de présence de cours';
$string['othershort'] = 'AUTRE';
$string['pluginname'] = 'Mesure d\'activité';
$string['pluginname_desc'] = 'Un bloc qui compile les durées de session';
$string['printpdf'] = 'Imprimer le PDF';
$string['profilefieldcontrol'] = 'Champ Profil';
$string['showdetails'] = 'Montrer les détails';
$string['sliding'] = 'temps glissant';
$string['studentchoice'] = 'Choix des étudiants';
$string['studentscansee'] = 'Les étudiants peuvent voir';
$string['task_cache_ttl'] = 'Cache d\'agrégation TTL';
$string['task_cleanup'] = 'Nettoyage des intervalles de temps';
$string['task_compile'] = 'Compilation des intervalles de temps';
$string['timeelapsed'] = 'Temps écoulé';
$string['to'] = '&ensp; à &ensp;';
$string['use_stats:addinstance'] = 'Permettre l\'ajout d\'une instance';
$string['use_stats:export'] = 'Permettre l\'export au format PDF (nécessite un rapport sur les sessions de formation)';
$string['use_stats:myaddinstance'] = 'Permettre l\'ajout d\'une instance de Ma Page';
$string['use_stats:seecoursedetails'] = 'Permettre la visualisation des détails de tous les utilisateurs depuis leur cours';
$string['use_stats:seegroupdetails'] = 'Permettre la visualisation des détails de tous les utilisateurs depuis leur groupes';
$string['use_stats:seeowndetails'] = 'Permettre la visualisation de ses propres détails';
$string['use_stats:seesitedetails'] = 'Permettre la visualisation des détails de tous les utilisateurs';
$string['use_stats:view'] = 'Permettre la visualisation de l\'activité';
$string['use_stats_description'] = 'En publiant ce service, vous permettez à des serveurs distants d\'accéder à la lecture des statistiques des utilisateurs locaux. <br/> En souscrivant à ce service , vous permettez à votre serveur local d\'interroger un serveur distant sur les statistiques de ses membres. <br/>';
$string['use_stats_name'] = 'Accès à distance aux statistiques';
$string['use_stats_rpc_service'] = 'Accès à distance aux statistiques';
$string['use_stats_rpc_service_name'] = 'Accès à distance aux statistiques';
$string['youspent'] = 'Vous avez déjà passé';
