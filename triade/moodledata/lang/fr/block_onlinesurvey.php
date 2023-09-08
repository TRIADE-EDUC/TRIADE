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
 * Strings for component 'block_onlinesurvey', language 'fr', version '4.1'.
 *
 * @package     block_onlinesurvey
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allsurveys'] = 'Toutes les enquêtes';
$string['error_config_not_accessible'] = 'Configuration non accessible';
$string['error_debugmode_missing_capability'] = 'Le bloc est en mode débogage. Vous n\'avez pas la permission d\'afficher le contenu.';
$string['error_lti_learnermapping_missing'] = 'Correspondance du rôle apprenant manquante';
$string['error_lti_password_missing'] = 'Clé client LTI manquante';
$string['error_lti_settings_error'] = 'Erreur de paramétrage LTI';
$string['error_lti_url_missing'] = 'URL du fournisseur LTI manquante';
$string['error_occured'] = '<b>Une erreur s\'est produite :</b><br />{$a}<br />';
$string['error_soap_settings_error'] = 'Erreur de paramétrage SOAP';
$string['error_survey_curl_timeout_msg'] = 'Les enquêtes n\'ont pas pu être interrogées.';
$string['error_survey_login_missing'] = 'Absence de chemin pour les enquêtes en ligne';
$string['error_survey_pwd_missing'] = 'Mot de passe SOAP manquant';
$string['error_survey_server_missing'] = 'URL du serveur evasys manquante';
$string['error_survey_user_missing'] = 'Utilisateur SOAP manquant';
$string['error_userid_not_found'] = 'Identifiant de l\'utilisateur non trouvé';
$string['error_warning_message'] = '<b>Attention :</b><br />{$a}<br />';
$string['error_wsdl_namespace'] = 'Erreur d\'analyse de l\'espace de noms WSDL<br />';
$string['lti'] = 'LTI';
$string['onlinesurvey:addinstance'] = 'Ajouter une instance du bloc Évaluations (evasys)';
$string['onlinesurvey:myaddinstance'] = 'Ajouter une instance du bloc Évaluations (evasys) à ma page';
$string['onlinesurvey:view'] = 'Voir le bloc Évaluations (evasys)';
$string['onlinesurvey:view_debugdetails'] = 'Afficher les détails du débogage';
$string['pluginname'] = 'Évaluations (evasys)';
$string['privacy:metadata:block_onlinesurvey'] = 'Le plugin de bloc evasys ne stocke pas de données personnelles, mais transmet les données de l\'utilisateur de Moodle à l\'instance evasys connectée.';
$string['privacy:metadata:block_onlinesurvey:email'] = 'Le courriel de l\'utilisateur envoyé à evasys pour vérifier les enquêtes existantes.';
$string['privacy:metadata:block_onlinesurvey:username'] = 'La valeur du nom de l\'utilisateur est envoyée à evasys pour vérifier les enquêtes existantes.';
$string['setting_additionalcss'] = 'CSS supplémentaire pour iframe';
$string['setting_additionalcss_desc'] = 'Ici, vous pouvez ajouter du code CSS qui sera ajouté à la page chargée dans le bloc evasys. Vous pouvez utiliser ce paramètre pour modifier le style du contenu du bloc evasys en fonction de vos besoins.<br /><em>Veuillez noter que ce paramètre est utilisé en mode compact pour les connexions LTI et SOAP ainsi qu\'en mode détaillé pour les connexions SOAP. Il n\'est pas utilisé en mode détaillé pour les connexions LTI - si vous devez ajouter des styles personnalisés dans ce mode, veuillez modifier votre modèle LTI dans evasys.</em>';
$string['setting_blocktitle'] = 'Titre';
$string['setting_blocktitle_desc'] = 'Le texte saisi ici est utilisé comme titre du bloc.';
$string['setting_blocktitle_multilangnote'] = 'Vous pouvez définir plus d\'une langue (par exemple, l\'anglais et l\'allemand) en utilisant la syntaxe du filtre multilingue de Moodle (voir https://docs.moodle.org/en/Multi-language_content_filter pour plus de détails).';
$string['setting_communication_interface'] = 'Protocole de communication';
$string['setting_communication_interface_desc'] = 'Ici, vous pouvez définir si Moodle doit communiquer avec evasys via SOAP ou LTI. <br /><em>Selon le protocole de communication sélectionné ici, veuillez effectuer vos autres réglages dans la section de protocole correspondante ci-dessous.</em>';
$string['setting_customfieldnumber'] = 'Champ personnalisé n°.';
$string['setting_customfieldnumberinevasys'] = 'Champ personnalisé dans evasys';
$string['setting_customfieldnumberinevasys_desc'] = 'Si le nom d\'utilisateur est sélectionné comme identifiant d\'utilisateur, l\'un des trois premiers champs personnalisés d\'evasys peut être utilisé pour l\'authentification.<br /><em>Remarque : ce paramètre n\'est pertinent que pour les apprenants. Si vous décidez d\'utiliser le nom d\'utilisateur pour les instructeurs, le nom d\'utilisateur doit être stocké dans evasys dans le champ « ID externe » des paramètres de l\'utilisateur.</em>';
$string['setting_heading_appearance'] = 'Aspect';
$string['setting_heading_appearance_desc'] = 'Les paramètres de cette section définissent la manière dont le bloc evasys sera affiché.';
$string['setting_heading_communication'] = 'Communication';
$string['setting_heading_communication_desc'] = 'Les paramètres de cette section définissent comment le bloc evasys communiquera avec evasys.';
$string['setting_heading_expert'] = 'Réglages experts';
$string['setting_heading_expert_desc'] = 'Les paramètres de cette section ne nécessitent normalement aucune modification et sont prévus pour des scénarios d\'utilisation particuliers.';
$string['setting_heading_lti'] = 'Réglages LTI';
$string['setting_heading_lti_desc'] = 'Les paramètres de cette section définissent la manière dont le bloc evasys communiquera avec evasys.<br /><em>Ces paramètres ne sont requis que si vous avez sélectionné « LTI » dans le paramètre « Protocole de communication ».</em>';
$string['setting_heading_soap'] = 'Réglages SOAP';
$string['setting_heading_soap_desc'] = 'Les paramètres de cette section définissent comment le bloc evasys communiquera avec evasys.<br /><em>Ces paramètres ne sont requis que si vous avez sélectionné « SOAP » dans le paramètre « Protocole de communication ».</em>';
$string['setting_lti_customparameters'] = 'Paramètre personnalisé evasys LTI';
$string['setting_lti_customparameters_desc'] = 'Les paramètres personnalisés stockés ici peuvent être utilisés pour définir les paramètres d\'affichage des enquêtes, par ex. si la vue étudiant doit également afficher les enquêtes terminées (learner_show_completed_surveys=1) ou si les rapports des enquêtes peuvent également être appelées dans la vue instructeur (instructor_show_report=1). Chaque paramètre doit être ajouté sur une ligne distincte. Pour des informations détaillées sur les paramètres disponibles, veuillez consulter le manuel evasys LTI.';
$string['setting_lti_instructormapping'] = 'Correspondance dur rôle LTI « Instructeur »';
$string['setting_lti_instructormapping_desc'] = 'Définit les rôles Moodle qui doivent correspondre au rôle LTI « Instructeur » et qui verront le contenu du bloc evasys en tant qu\'instructeurs.';
$string['setting_lti_learnermapping'] = 'Correspondance du rôle LTI « apprenant »';
$string['setting_lti_learnermapping_desc'] = 'Définit les rôles Moodle qui doivent correspondre au rôle LTI « apprenant » et qui verront le contenu du bloc evasys en tant qu\'étudiants.';
$string['setting_lti_regex_instructor'] = 'Expression régulière Instructeur-LTI';
$string['setting_lti_regex_instructor_desc'] = 'Expression régulière qui recherche le contenu de LTI-Response pour les enquêtes en ligne ouvertes. Cela ne doit être ajusté que si des modèles personnalisés ont été créés ou modifiés de manière à ce que les fonctions diffèrent des modèles standard.<br /><em>Remarque : Ce paramètre n\'est traité que si vous avez sélectionné « LTI » dans le Paramètre « Protocole de communication ».</em>';
$string['setting_lti_regex_learner'] = 'Expression régulière Apprenant-LTI';
$string['setting_lti_regex_learner_desc'] = 'Expression régulière qui recherche le contenu de LTI-Response pour les enquêtes en ligne ouvertes. Cela ne doit être ajusté que si des modèles personnalisés ont été créés ou modifiés de manière à ce que les fonctions diffèrent des modèles standard.<br /><em>Remarque : ce paramètre n\'est traité que si vous avez sélectionné « LTI » dans le Paramètre « Protocole de communication ».</em>';
$string['setting_offer_zoom'] = 'Toujours offrir une vue agrandie de la liste';
$string['setting_offer_zoom_desc'] = 'Si activé, l\'utilisateur pourra toujours ouvrir la vue de liste agrandie. S\'il n\'est pas activé, l\'utilisateur ne pourra ouvrir la vue de liste agrandie que s\'il a des enquêtes ouvertes.<br /><em>Veuillez noter : si le modèle LTI que vous utilisez est configuré de manière à ce que les participants soient autorisés à voir et/ou accéder aux résultats des enquêtes auxquels ils ont participé, vous devrez activer ce paramètre. Sinon, les participants ne pourraient plus accéder aux résultats.</em>';
$string['setting_presentation'] = 'Mode d\'affichage';
$string['setting_presentation_brief'] = 'Compact';
$string['setting_presentation_desc'] = 'En mode compact, le bloc evasys affiche le nombre d\'enquêtes ouvertes au moyen d\'un graphique. Dans ce mode, une vue de liste agrandie peut être ouverte dès que l\'utilisateur a au moins une enquête ouverte en cliquant sur le graphique.<br />En mode détaillé, le bloc evasys affiche directement la liste des enquêtes disponibles. Dans ce mode, mais uniquement lors de l\'utilisation d\'une connexion SOAP, une vue de liste agrandie peut être ouverte dès que l\'utilisateur a au moins une enquête ouverte en cliquant sur un bouton sous la liste.';
$string['setting_presentation_detailed'] = 'Détaillé';
$string['setting_show_spinner'] = 'Montrer la roue';
$string['setting_show_spinner_desc'] = 'Si elle est activée, une icône en forme de roue sera affichée dans le bloc jusqu\'à ce que les enquêtes ouvertes soient chargées depuis evasys.';
$string['setting_soap_request_eachtime'] = 'Demande de données SOAP à chaque rendu';
$string['setting_soap_request_eachtime_desc'] = 'Si activé, les données qui sont rendues dans le bloc evasys seront demandées à evasys à chaque fois que le bloc est rendu. Si elles ne sont pas activées, les données ne sont demandées qu\'une seule fois par session (c\'est-à-dire une seule fois lorsqu\'un utilisateur s\'est connecté à Moodle).';
$string['setting_survey_debug'] = 'Mode debogage';
$string['setting_survey_debug_desc'] = 'S\'il est activé, les messages de débogage et d\'erreur sont affichés dans le bloc evasys.';
$string['setting_survey_hide_empty'] = 'Cacher les blocs vides';
$string['setting_survey_hide_empty_desc'] = 'S\'il est activé, le bloc evasys est masqué lorsque l\'utilisateur n\'a pas de enquêtes. S\'il n\'est pas activé, dans la vue compacte, un graphique avec le texte « Aucune évaluation ouverte disponible » s\'affiche et dans la vue détaillée, une liste vide est présentée.<br /><em>Veuillez noter : si le modèle LTI que vous utilisez using est configuré de manière à ce que les participants soient autorisés à voir et/ou à accéder aux résultats des enquêtes auxquelles ils ont participé, vous ne voudrez peut-être pas masquer le blocage. Sinon, les participants ne pourraient plus accéder aux résultats.</em>';
$string['setting_survey_login'] = 'Chemin SOAP evasys pour les enquêtes en ligne';
$string['setting_survey_login_desc'] = 'URL de la connexion à l\'enquête en ligne evasys (https://[SERVERNAME]/evasys/).';
$string['setting_survey_lti_password'] = 'Mot de passe LTI evasys';
$string['setting_survey_lti_password_desc'] = 'Mot de passe de l\'interface LTI evasys.';
$string['setting_survey_lti_url'] = 'URL du fournisseur LTI evasys';
$string['setting_survey_lti_url_desc'] = 'URL du fichier PHP du fournisseur LTI sur le serveur evasys (https://[SERVERNAME]/customer/lti/lti_provider.php).';
$string['setting_survey_popupinfo_content'] = 'Contenu de la pop-up';
$string['setting_survey_popupinfo_content_default'] = '<p>Cher étudiant,</p>
<p>il existe actuellement une ou plusieurs enquêtes en ligne ouvertes disponibles pour les cours que vous avez visités. Votre participation nous aide à améliorer nos offres.<br />
Les liens vers l\'enquête sont affichés dans le bloc "Évaluations".</p>
<p>Merci pour votre soutien !<br />
Votre équipe d\'évaluation</p>';
$string['setting_survey_popupinfo_content_desc'] = 'Si nécessaire, le contenu qui est présenté dans la pop-up peut être modifié avec ce paramètre.';
$string['setting_survey_popupinfo_title'] = 'Titre de la pop-up';
$string['setting_survey_popupinfo_title_default'] = 'Évaluations ouvertes';
$string['setting_survey_popupinfo_title_desc'] = 'Si nécessaire, le titre de la pop-up peut être modifié grâce à ce paramètre.';
$string['setting_survey_pwd'] = 'Mot de passe SOAP evasys';
$string['setting_survey_pwd_desc'] = 'Mot de passe de l\'utilisateur SOAP evasys.';
$string['setting_survey_server'] = 'URL WSDL SOAP evasys';
$string['setting_survey_server_desc'] = 'URL du fichier de description du service Web sur le serveur evasys (https://[SERVERNAME]/evasys/services/soapserver-v61.wsdl).<br /><em>Remarque : Si evasys fonctionne avec plusieurs serveurs (dual option serveur), le serveur principal sur lequel les utilisateurs et les administrateurs travaillent, doit être spécifié ici. Cela évite une charge trop élevée sur le serveur d\'enquête en ligne.</em>';
$string['setting_survey_show_popupinfo'] = 'Info pop-up active';
$string['setting_survey_show_popupinfo_desc'] = 'S\'il est activé, une pop-up contenant des informations sur les enquêtes en ligne ouvertes (si elles existent) s\'affiche chaque fois qu\'un étudiant se connecte à Moodle.';
$string['setting_survey_timeout'] = 'Délai de connexion';
$string['setting_survey_timeout_desc'] = 'Temps de réponse maximum (en secondes) du serveur evasys. Si le serveur evasys ne répond pas dans ce délai, la demande est abandonnée et les enquêtes ne sont pas montrées à l\'utilisateur.';
$string['setting_survey_user'] = 'Identifiant SOAP evasys';
$string['setting_survey_user_desc'] = 'Nom de l\'utilisateur SOAP evasys.';
$string['setting_useridentifier'] = 'Identifiant de l\'utilisateur';
$string['setting_useridentifier_desc'] = 'Indiquez si l\'adresse électronique ou le nom d\'utilisateur doit être utilisé comme identifiant unique de l\'utilisateur.';
$string['soap'] = 'SOAP';
$string['surveys_exist'] = 'Enquêtes ouvertes disponibles';
$string['surveys_exist_not'] = 'Aucune enquête ouverte disponible';
$string['upgrade_notice_2020010900'] = 'La version recommandée pour l\'API SOAP Evasys est passée de la version 51 à la version 61. Ainsi, les paramètres du plugin ont été automatiquement modifiés lors de la mise à jour du plugin.<br />L\'URL Evasys SOAP WSDL était jusqu\'à présent : {$a->old }<br />L\'URL Evasys SOAP WSDL est désormais : {$a->new}<br />Veuillez vérifier que l\'URL modifiée automatiquement est correcte.';
$string['zoomsurveylist'] = 'Zoom sur la liste d\'enquête';
