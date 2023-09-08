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
 * Strings for component 'videotime', language 'fr', version '4.1'.
 *
 * @package     videotime
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activity_name'] = 'Nom de l\'activité';
$string['activity_name_help'] = 'Nom affiché dans le cours pour cette activité Video Time';
$string['albums'] = 'Albums';
$string['api_not_authenticated'] = 'API Vimeo non authentifiée';
$string['api_not_configured'] = 'API Vimeo non configurée';
$string['apply'] = 'Appliquer';
$string['authenticate_vimeo'] = 'Authentifié avec Vimeo';
$string['authenticate_vimeo_success'] = 'Authentification Vimeo réussie. Vous pouvez maintenant utiliser les fonctionnalités basées sur l\'API Vimeo.';
$string['authenticated'] = 'Authentifié';
$string['autopause'] = 'Pause automatique';
$string['averageviewtime'] = 'Temps moyen de visionnage';
$string['background'] = 'Arrière plan';
$string['browsevideos'] = 'Parcourir les vidéos';
$string['choose_video'] = 'Choisir une vidéo';
$string['choose_video_confirm'] = 'Voulez-vous vraiment sélectionner cette vidéo ?';
$string['cleanupalbumsandtags'] = 'Nettoyer les albums et les tags';
$string['client_id'] = 'ID client Vimeo';
$string['client_id_help'] = 'L\'ID client est généré lorsque vous créez une «App» dans votre compte Vimeo. Aller sur https://developer.vimeo.com/apps/new pour démarrer le processus.';
$string['client_secret'] = 'Clé secrète Vimeo';
$string['client_secret_help'] = 'La clé secrète est générée lorsque vous créez une «App» dans votre compte Vimeo. Aller sur https://developer.vimeo.com/apps/new pour démarrer le processus.';
$string['columns'] = 'Colonnes';
$string['columns_help'] = 'Choisir la largeur de cette vidéo pour le mode Prévisualisation. Le nombre de colonnes indique le nombre de vidéos qui peuvent être affichées sur une ligne.';
$string['completion_hide_detail'] = 'Cacher les détails de l\'état d\'achèvement';
$string['completion_on_finish'] = 'Statut d\'achèvement en fin de lecture de la vidéo';
$string['completion_on_percent'] = 'Statut d\'achèvement en pourcentage de lecture de la vidéo';
$string['completion_on_percent_value'] = 'Valeur d\'achèvement en fonction du pourcentage de lecture';
$string['completion_on_view'] = 'Statut d\'achèvement en fonction du temps de visionnage';
$string['completion_on_view_seconds'] = 'Statut d\'achèvement en secondes de visionnage';
$string['completiondetail:_on_finish'] = 'Fermer la vidéo';
$string['completiondetail:_on_percent'] = 'Validation de fin de lecture {$a} en pourcentage';
$string['completiondetail:_on_view_time'] = 'Validation du temps de lecture {$a}';
$string['configure_vimeo_first'] = 'Vous devez configurer une App Vimeo avant de vous authentifier';
$string['configure_vimeo_help'] = '<ol><li>Naviguer vers <a href="https://developer.vimeo.com/apps/new">https://developer.vimeo.com/apps/new</a> et identifiez-vous sur votre compte Vimeo</li>
<li>Entrer un nom et une description pour votre App. Exemple : Video Time container  API</li>
<li>Vérifier que cette option est sélectionnée  «Tous les comptes Vimeo qui peuvent accéder à mon App m’appartiennent»</li>
<li>Accepter les Termes et Conditions de «Créer une App»</li>
<li>Vous allez être redirigé vers votre nouvelle App</li>
<li>Cliquer «Modifier les paramètres»</li>
<li>Encoder une description pour votre App, qui sera visible par les administrateurs en cours d’authentification sur Vimeo</li>
<li>Encoder l’URL de votre App, elle doit correspondre à  <b>{$a->redirect_url}</b></li>
<li>Cliquer "Mettre à jour"</li>
<li>Ajouter une URL de rappel, elle doit correspondre à  <b>{$a->redirect_url}</b></li>
<li>Copier votre identifiant Client (près du haut de page) et votre clé secrète (Gérer vos clés secrètes)</li>
<li>Encoder votre identifiant Client  et votre clé secrète <a href="{$a->configure_url}">ici </a></li></ol>';
$string['confirmation'] = 'Confirmation';
$string['controls'] = 'Contrôles';
$string['create_vimeo_app'] = 'Créer une App Vimeo';
$string['currentwatchtime'] = 'Temps de visionnage actuel';
$string['datasource:videotime_sessions_data_source'] = 'Sessions Video Time';
$string['datasource:videotime_stats_data_source'] = 'Statistiques Video Time';
$string['default'] = 'Valeur par défaut';
$string['defaulttabsize'] = 'Taille par défaut de l\'Onglet';
$string['defaulttabsize_help'] = 'Définir la taille qui sera utilisée pour la largeur relative initiale des onglets';
$string['deletesessiondata'] = 'Effacer les données de session';
$string['discover_videos'] = 'Rechercher les vidéos Vimeo';
$string['discovering_videos'] = 'Nombre de vidéos trouvées {$a->count}';
$string['display_options'] = 'Options d\'affichage';
$string['dnt'] = 'Désactiver le suivi';
$string['done'] = 'Fait';
$string['duration'] = 'Durée';
$string['embed_options'] = 'Options d\'intégration';
$string['embed_options_defaults'] = 'Options d\'intégration par défaut';
$string['embeds'] = 'Intégrés';
$string['enabletabs'] = 'Onglets intégrés';
$string['estimated_request_time'] = 'Estimation du temps restant';
$string['firstsession'] = 'Première session';
$string['force'] = 'Forcer les paramètres';
$string['force_help'] = 'Si coché, cette valeur va écraser les valeurs existantes';
$string['goback'] = 'Retour';
$string['gradeitemnotcreatedyet'] = 'Il n\'y a pas de Bulletin de Notes pour cette activité. Vérifier <b>Définir une note égale au pourcentage d\'affichage</b>, enregistrer et rééditer cette activité pour définir la catégorie de grade et le score de réussite.';
$string['hideshow'] = 'Montrer/Cacher';
$string['insert_video_metadata'] = 'Insérer les meta data depuis la vidéo (Peut écraser les paramètres de l\'activité)';
$string['invalid_session_state'] = 'État de session invalide';
$string['label_mode'] = 'Mode Étiquette';
$string['lastsession'] = 'Dernière session';
$string['managevideotimetabplugins'] = 'Gérer le plugin des onglets Video Time';
$string['mode'] = 'Mode';
$string['mode_help'] = '<b>Mode normal</b> : Affiche le lien d’activité standard sur la page de cours.<br>
<b>Mode étiquette</b> : Encapsule la vidéo dans le rendu du cours de la même façon que l\'activité Étiquette.<br>
<b>Prévisualiser le mode image</b> : Afficher les vignettes vidéo sur la page du cours qui est liée à l’activité (Seulement pour le container Video Time).';
$string['modulename'] = 'Video Time';
$string['modulename_help'] = 'L’activité Video Time Pro permet au formateur de ;
<ul>
<li>intégrer facilement des vidéos directement depuis Vimeo en ajoutant l’URL</li>
<li>ajouter du contenu au-dessus et en-dessous du lecteur vidéo </li>
<li>suivre le temps de visionnage en utilisant l’achèvement d’activité Moodle</li>
<li>connaître le temps de visionnage de chaque utilisateur </li>
<li>définir les options par défaut d’intégration du plugin </li>
<li>écraser les valeurs globales des occurrences intégrées.</li>
</ul>
Nous travaillons constamment sur ce plugin, restez connecté pout être informé des versions à venir. Vous pouvez voir les tâches en cours et proposer des requêtes sur notre Roadmap public<ahref="https://bdecent.de/products/videotimepro/roadmap">https://bdecent.de/products/videotimepro/roadmap</a>.
Meri pour vos feedback et commentaires.';
$string['modulenameplural'] = 'Instances Video Time';
$string['more'] = 'Plus';
$string['needs_authentication'] = 'Requiert une nouvelle authentification';
$string['next_activity'] = 'Activité suivante';
$string['next_activity_auto'] = 'Aller automatiquement à l\'activité suivante';
$string['next_activity_auto_help'] = 'Afficher automatiquement l\'activité suivante lorsque l\'étudiant a visionné la vidéo.';
$string['next_activity_button'] = 'Activer le bouton Activité suivante';
$string['next_activity_button_help'] = 'Afficher un bouton au-dessus de la vidéo pour démarrer automatiquement l\'activité suivante';
$string['next_activity_in_course'] = 'Par défaut : Activité suivante dans le cours';
$string['nocompletioncriteriaset'] = 'Pas de critère d\'achèvement. Sélectionner un critère ci-dessous.';
$string['normal_mode'] = 'Mode normal';
$string['not_authenticated'] = 'Non authentifié';
$string['of'] = 'ou';
$string['option_autopause'] = 'Pause automatique';
$string['option_autopause_help'] = 'Permet de mettre la vidéo courante en pause si une autre vidéo Vimeo est lancée sur la même page. Choisir la valeur \'Faux\' pour autoriser la lecture simultanée de toutes les vidéos de la page. Cette option est sans effet si votre navigateur n\'accepte pas les Cookies (soit en mode natif, soit à cause d\'une extension ou d\'un autre plugin).';
$string['option_autoplay'] = 'Démarrage automatique';
$string['option_autoplay_help'] = 'Démarrer automatiquement la lecture de la vidéo (Ne fonctionne pas avec certains navigateurs et/ou terminaux)';
$string['option_background'] = 'En arrière-plan';
$string['option_background_help'] = 'Active automatiquement la lecture automatique et la lecture en boucle si le lecteur fonctionne en arrière-plan (ce qui cache les options de contrôle du lecteur).';
$string['option_byline'] = 'Signature de la vidéo (nom de l\'auteur)';
$string['option_byline_help'] = 'Affiche le nom de l\'auteur sur la vidéo';
$string['option_color'] = 'Couleur';
$string['option_color_help'] = 'Définir la couleur des boutons de contrôle du lecteur (La couleur peut être écrasée par celle définie dans les paramètres de la vidéo intégrée).';
$string['option_controls'] = 'Boutons de contrôle.';
$string['option_controls_help'] = 'Ce paramètre cachera tous les options du lecteur (Barre de lecture, boutons de partage...). Avertissement : si vous utilisez ce paramètre, le bouton « Démarrer/Mettre en pause » sera caché. Pour permettre le démarrage de la lecture pour vos visiteurs, il faudra soit activer le « Démarrage automatique », soit utiliser les touches du clavier, soit implémenter le SDK du lecteur et des contrôles. Note : ce paramètre ne désactive pas les touches de lecture du clavier.';
$string['option_dnt'] = 'Ne pas exécuter de suivi';
$string['option_dnt_help'] = 'Si activé, empêche le suivi des données de session du lecteur, en ce compris les Cookies. Avertissement : si activé, ceci bloque aussi les statistiques relatives aux vidéos.';
$string['option_forced'] = '{$a->option} forcé partout en : {$a->value}';
$string['option_height'] = 'Hauteur';
$string['option_height_help'] = 'Définit la hauteur exacte de la vidéo (par défaut, la plus grande valeur disponible en hauteur est sélectionnée).';
$string['option_maxheight'] = 'Hauteur maximale';
$string['option_maxheight_help'] = 'Même hauteur, mais sans excéder la hauteur de la vidéo originale';
$string['option_maxwidth'] = 'Largeur maximale';
$string['option_maxwidth_help'] = 'Même largeur, mais sans excéder la largeur de la vidéo originale';
$string['option_muted'] = 'En sourdine';
$string['option_muted_help'] = 'Couper le son de la vidéo au chargement. Requis pour la lecture automatique avec certains navigateurs';
$string['option_pip'] = 'Image dans l\'image';
$string['option_pip_help'] = 'Inclure le bouton «Image dans l\'image» dans les contrôles du lecteur et activer l\'API Image dans l\'image';
$string['option_playsinline'] = 'Charge la vidéo dans la page';
$string['option_playsinline_help'] = 'Charge la vidéo dans la page sur les appareils mobiles. Pour exécuter le mode plein écran, désactiver cette option.';
$string['option_portrait'] = 'Photo de profil';
$string['option_portrait_help'] = 'Afficher la photo de profil sur la vidéo';
$string['option_preventfastforwarding'] = 'Empêcher l\'avance rapide';
$string['option_responsive'] = 'Mode réactif';
$string['option_responsive_help'] = 'Si activé, le lecteur vidéo fonctionnera en mode réactif avec affichage en fonction des dimensions de la page ou de l\'écran.';
$string['option_speed'] = 'Vitesse';
$string['option_speed_help'] = 'Afficher le contrôle de vitesse de lecture dans le menu « Préférences » et activer l\'API vitesse de lecture (disponible dans les versions PRO et Business).';
$string['option_title'] = 'Titre';
$string['option_title_help'] = 'Afficher le titre sur la vidéo.';
$string['option_transparent'] = 'Transparence de l\'arrière-plan du lecteur';
$string['option_transparent_help'] = 'Le mode réactif et la transparence de l\'arrière-plan du lecteur sont activés par défaut. Pour les désactiver, décocher cette option.';
$string['option_width'] = 'Largeur';
$string['option_width_help'] = 'Largeur exacte de la vidéo. Affiche par défaut la largeur existante la plus grande.';
$string['panelwidthlarge'] = 'Affiche l\'onglet vidéo au format large';
$string['panelwidthmedium'] = 'Affiche l\'onglet vidéo au format moyen';
$string['panelwidthsmall'] = 'Affiche l\'onglet vidéo au format petit';
$string['percentageofvideofinished'] = 'Pourcentage de lecture de la vidéo';
$string['pip'] = 'Image dans l\'image';
$string['pluginadministration'] = 'Administration Video Time';
$string['pluginname'] = 'Video Time';
$string['preventfastforwarding'] = 'Empêche l\'avance rapide';
$string['preventfastforwarding_help'] = 'Empêche le visiteur de rechercher une séquence dans la partie non encore visionnée';
$string['preventfastforwardingmessage'] = 'Vous n\'avez visionné que {$a->percent}% de cette vidéo. Vous ne pouvez pas passer à une séquence ultérieure sans visionner les précédentes.';
$string['preview_mode'] = 'Mode image pour prévisualisation';
$string['preview_picture'] = 'Image pour prévisualisation';
$string['preview_picture_help'] = 'Image affichée pour l\'utilisateur.';
$string['preview_picture_linked'] = 'Lien vers l\'image pour prévisualisation';
$string['preview_picture_url'] = 'URL de l\'image pour prévisualisation';
$string['privacy:metadata'] = 'L\'activité Video Time ne conserve aucune donnée personnelle';
$string['process_videos'] = 'Traiter les vidéos';
$string['process_videos_help'] = 'Les vidéos seront traitées via une tâche programmée. Pour les comptes Vimeo plus importants, cela peut être plus long.';
$string['pull_from_vimeo'] = 'Importer les métadonnées depuis Vimeo';
$string['pull_from_vimeo_invalid_videoid'] = 'Impossible de récupérer l\'ID de la vidéo. Vérifier que vous ayez bien encodé l\'URL Vimeo  (Exemple : https://vimeo.com/635473456).';
$string['pull_from_vimeo_loading'] = 'Importation des métadonnées depuis Vimeo en cours...';
$string['pull_from_vimeo_success'] = 'Les métadonnées ont bien été importées depuis Vimeo. Certains paramètres de l\'activité ont été écrasés.';
$string['rate_limit'] = 'Limite de requête de l\'API Vimeo';
$string['refreshpage'] = 'Recharger la page pour afficher l\'activité en doublon';
$string['results'] = 'résultats';
$string['resume_playback'] = 'Reprendre la lecture';
$string['resume_playback_help'] = 'Reprendre automatiquement la lecture lorsque le visiteur retourne dans l\'activité. La lecture reprend où le visiteur a abandonné la lecture.';
$string['run_discovery_task'] = 'Effectuer la tâche « Rechercher les vidéos Vimeo » pour démarrer l\'importation des données vidéos. Vous pouvez aussi attendre le démarrage automatique.';
$string['search:activity'] = 'Video Time - Information sur l\'activité';
$string['search:texttrack'] = 'Video Time - Information sur les données textuelles (sous-titres, chapitres...)';
$string['search_help'] = 'Rechercher un nom, une description, des albums, des étiquettes...';
$string['seconds'] = 'Secondes';
$string['session_not_found'] = 'Session utilisateur non trouvée';
$string['set_client_id_and_secret'] = 'Encoder votre ID Client et votre clé secrète';
$string['settings'] = 'Paramètres Video Time';
$string['setup_repository'] = 'Conteneur pour l\'installation';
$string['show_description'] = 'Afficher la description';
$string['show_description_in_player'] = 'Afficher la description au-dessus du lecteur';
$string['show_duration'] = 'Afficher la durée';
$string['show_tags'] = 'Afficher les étiquettes';
$string['show_title'] = 'Afficher le titre';
$string['show_viewed_duration'] = 'Afficher le temps de visionnage';
$string['showdescription'] = 'Afficher la description';
$string['showdescription_help'] = 'La description est affichée au-dessus de la vidéo et peut être affichée sur la page du cours.';
$string['showing'] = 'Afficher';
$string['showtab'] = 'Afficher l\'onglet';
$string['state'] = 'État';
$string['state_finished'] = 'Achevé';
$string['state_help'] = 'Le visiteur a-t-il visionné l\'entièreté de la vidéo ?';
$string['state_incomplete'] = 'Incomplet';
$string['status'] = 'Statut';
$string['store_pictures'] = 'Enregistrer les vignettes';
$string['store_pictures_help'] = 'Si activé, les vignettes Vimeo seront enregistrées localement. Sinon, les images seront affichées à partir de Vimeo.';
$string['subplugintype_videotimeplugin'] = 'Extension Video Time';
$string['subplugintype_videotimeplugin_plural'] = 'Extensions Video Time';
$string['subplugintype_videotimetab'] = 'Onglet Video Time';
$string['subplugintype_videotimetab_plural'] = 'Onglets Video Time';
$string['tabinformation'] = 'Information';
$string['tablealias_vt'] = 'Video Time';
$string['tabs'] = 'Onglets';
$string['tabtranscript'] = 'Transcription';
$string['taskscheduled'] = 'Tâche planifiée pour la prochaine exécution cron';
$string['timestarted'] = 'Date de démarrage';
$string['todo'] = 'À FAIRE';
$string['totaluniquevisitors'] = 'Nombre total de visiteurs uniques';
$string['totalvideotime'] = 'Durée totale de la vidéo : {$a->time}';
$string['totalviews'] = 'Nombre total de vues';
$string['totara_video_discovery_help'] = '<p>Vous pouvez exécuter cette tâche manuellement en exécutant cette commande CLI :</p>
<p><b>/usr/bin/php admin/tool/task/cli/schedule_task.php --execute=\\\\videotimeplugin_repository\\\\task\\\\discover_videos</b></p>
<p>Si pas, vous pouvez attendre l’exécution des tâches planifiées.</p>
<p>Vous pouvez également exécuter cette commande pour importer manuellement des informations dans l’album (si vous ne pouvez pas attendre) :</p>
<p><b>/usr/bin/php admin/tool/task/cli/schedule_task.php --execute=\\\\videotimeplugin_repository\\\\task\\\\update_albums</b></p>';
$string['update_albums'] = 'Mettre à jour les albums vidéo';
$string['upgrade_vimeo_account'] = 'NOTE : envisager une mise à jour de votre compte Vimeo. Le seuil de vos requêtes API est trop bas.';
$string['use'] = 'Utiliser';
$string['video_description'] = 'Notes';
$string['video_description_help'] = 'Les notes sont affichées sous la vidéo.';
$string['videocreated'] = 'Vidéo créée';
$string['videos_discovered'] = 'Vidéos trouvées';
$string['videos_processed'] = 'Vidéos traitées';
$string['videotime:addinstance'] = 'Ajouter un nouveau module Video Time';
$string['videotime:view'] = 'Voir la vidéo Video Time';
$string['videotime:view_report'] = 'Voir le rapport (uniquement dans la version PRO)';
$string['videotimelink'] = 'Lien';
$string['videotimetabpluginname'] = 'Nom de l\'onglet Video Time';
$string['videotimetabplugins'] = 'Extensions onglet Video Time';
$string['videotimeurl'] = 'URL';
$string['view_report'] = 'Voir le rapport';
$string['viewpercentgrade'] = 'Définir une note égale au pourcentage de visionnage.';
$string['viewpercentgrade_help'] = 'Créer un élément d\'évaluation pour cette vidéo. Les étudiants recevront une note égale au pourcentage de visionnage de la vidéo.';
$string['views'] = 'Vues';
$string['views_help'] = 'Nombre de vues de l\'activité.';
$string['vimeo_overview'] = 'Aperçu et installation';
$string['vimeo_url'] = 'URL Vimeo';
$string['vimeo_url_help'] = 'URL complète de la vidéo Vimeo.';
$string['vimeo_url_invalid'] = 'URL Vimeo invalide. Copiez-la directement depuis votre navigateur.';
$string['vimeo_url_missing'] = 'URL Vimeo non définie';
$string['vimeo_video_not_found'] = 'Cette vidéo n\'existe pas dans la base de données.';
$string['vimeo_video_not_processed'] = 'La vidéo n\'a pas encore été traitée complètement. Vérifiez à nouveau plus tard.';
$string['watch'] = 'Regarder';
$string['watch_percent'] = 'Pourcentage de visionnage';
$string['watch_percent_help'] = 'Moment le plus avancé que l\'étudiant a consulté dans la vidéo.';
$string['watch_time'] = 'Temps de visionnage';
$string['watch_time_help'] = 'Durée totale de visionnage de la vidéo par l\'étudiant (par tranche de 5 sec.)';
$string['with_play_button'] = 'avec bouton « regarder »';
