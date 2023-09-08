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
 * Strings for component 'tool_usersuspension', language 'fr', version '4.1'.
 *
 * @package     tool_usersuspension
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['action:confirm-delete-exclusion'] = 'Voulez-vous vraiment supprimer cet élément de la liste d\'exclusion ?';
$string['action:delete-exclusion'] = 'Supprimer l\'élément de la liste d\'exclusion';
$string['action:exclude:add:cohort'] = 'Ajouter une exclusion de cohorte';
$string['action:exclude:add:user'] = 'Ajouter une exclusion d\'utilisateur';
$string['button:backtocourse'] = 'Retour au cours';
$string['button:backtoexclusions'] = 'Retour à l\'aperçu des exclusions';
$string['button:backtouploadform'] = 'Retour au formulaire de téléchargement';
$string['button:continue'] = 'Continuer';
$string['config:cleanlogs:disabled'] = 'Le nettoyage automatique des journaux est désactivé dans la configuration globale';
$string['config:cleanup:disabled'] = 'L\'option « nettoyage » de la suspension de l\'utilisateur est désactivée dans la configuration globale de l\'outil';
$string['config:fromfolder:disabled'] = 'L\'option de suspension de l\'utilisateur ­« suspendre du téléchargement » est désactivée dans la configuration globale de l\'outil';
$string['config:fromfolder:enabled'] = 'L\'option de suspension de l\'utilisateur ­« suspendre du téléchargement » est activée dans la configuration globale de l\'outil';
$string['config:smartdetect:disabled'] = 'L\'option de suspension d\'utilisateur ­« détection intelligente » est désactivée dans la configuration globale de l\'outil';
$string['config:tool:disabled'] = 'L\'utilitaire de suspension d\'utilisateur est désactivé dans la configuration globale de l\'outil';
$string['config:tool:enabled'] = 'L\'utilitaire de suspension d\'utilisateur est activé dans la configuration globale de l\'outil';
$string['config:unsuspendfromfolder:disabled'] = 'L\'option de suspension d\'utilisateur ­« annuler la suspension du téléchargement » est désactivée dans la configuration globale de l\'outil';
$string['config:unsuspendfromfolder:enabled'] = 'L\'option de suspension d\'utilisateur ­« annuler la suspension du téléchargement » est activée dans la configuration globale de l\'outil';
$string['config:uploadfile:exists'] = 'Le fichier de téléchargement « {$a} » existe';
$string['config:uploadfile:not-exists'] = 'Le fichier de téléchargement « {$a} » n\'existe pas';
$string['config:uploadfolder:exists'] = 'Le dossier de téléchargement « {$a} » existe';
$string['config:uploadfolder:not-exists'] = 'Le dossier de téléchargement « {$a} » n\'existe pas';
$string['configoption:notactive'] = 'Malgré l\'aperçu ci-dessous, les paramètres dictent que le processus réel n\'est <i>pas</i> appliqué.';
$string['csv:delimiter'] = 'Délimiteur';
$string['csv:enclosure'] = 'Enveloppe';
$string['csv:upload:continue'] = 'Continuer';
$string['csvdelimiter'] = 'Séparateur CSV';
$string['csvencoding'] = 'Encodage CSV';
$string['deleteon'] = 'Supprimer le';
$string['download-sample-csv'] = 'Télécharger un exemple de fichier CSV';
$string['email:user:delete:body'] = '<p>Cher {$a->name}</p>
<p>Votre compte a été supprimé après avoir été suspendu pendant {$a->timesuspended}</p>
<p>Cordialement<br/>{$a->signature}</p>';
$string['email:user:delete:subject'] = 'Votre compte a été supprimé';
$string['email:user:suspend:auto:body'] = '<p>Cher {$a->name}</p>
<p>Votre compte a été suspendu après avoir été inactif pendant {$a->timeinactive}</p>
<p>Si vous pensez que ce n\'est pas intentionnel ou si vous souhaitez réactiver votre compte,
veuillez contacter {$a->contact}</p>
<p>Cordialement<br/>{$a->signature}</p>';
$string['email:user:suspend:manual:body'] = '<p>Cher {$a->name}</p>
<p>Votre compte a été suspendu.</p>
<p>Si vous pensez que ce n\'est pas intentionnel ou si vous souhaitez réactiver votre compte,
veuillez contacter {$a->contact}</p>
<p>Cordialement<br/>{$a->signature}</p>';
$string['email:user:suspend:subject'] = 'Votre compte a été suspendu';
$string['email:user:unsuspend:body'] = '<p>Cher {$a->name}</p>
<p>Votre compte a été réactivé.</p>
<p>Si vous pensez que cela n\'est pas intentionnel ou si vous souhaitez que votre compte soit à nouveau suspendu,
veuillez contacter {$a->contact}</p>
<p>Cordialement<br/>{$a->signature}</p>';
$string['email:user:unsuspend:subject'] = 'Votre compte a été réactivé';
$string['email:user:warning:body'] = '<p>Cher {$a->name}</p>
<p>Votre compte sera suspendu dans {$a->warningperiod} en raison de l\'inactivité sur la plateforme.</p>
<p>Vous devez vous connecter dans les prochains {$a->warningperiod} pour garder votre compte actif.
Pour éviter que cela ne se produise, veuillez vous assurer de vous connecter au système au moins tous les {$a->suspendinterval}.</p>
<p>Cordialement<br/>{$a->signature}</p>';
$string['email:user:warning:subject'] = 'Votre compte sera bientôt suspendu';
$string['err:statustable:set_sql'] = 'set_sql() est désactivé. Ce tableau est défini automatiquement et n\'est pas personnalisable';
$string['event:user:suspended'] = 'Utilisateur suspendu.';
$string['excludeuser'] = 'exclure l\'utilisateur du traitement';
$string['form:static:uploadfile:desc'] = 'Téléchargez votre fichier de suspension d\'utilisateur ici<br/>
Le fichier CSV importé peut être configuré comme suit :<br/>
<ol>
<li>fichier « simple » contenant UNIQUEMENT les adresses e-mail, une par ligne</li>
<li>Fichier  « smart » contenant 2 colonnes, indiquant le type et la valeur.<br/>
Les valeurs possibles pour la colonne type sont
<ul><li>email : la colonne de valeur indique l\'adresse e-mail du compte utilisateur</li>
<li>idnumber : la colonne de valeur indique le numéro d\'identification du compte utilisateur</li>
<li>nom d\'utilisateur : la colonne de valeur indique le nom d\'utilisateur du compte utilisateur</li>
</ul></ol>';
$string['info:no-exclusion-cohorts'] = 'Plus aucune cohorte ne peut être configurée pour être exclue. Tous sont déjà ajoutés à la liste d\'exclusion';
$string['label:users:excluded'] = 'Utilisateurs exclus';
$string['label:users:potential'] = 'Utilisateurs potentiels';
$string['link:currentstatus:overview'] = 'Afficher les changements d\'état actuels';
$string['link:exclude:overview'] = 'Aperçu des exclusions';
$string['link:log:overview'] = 'Afficher les journaux de changement d\'état';
$string['link:upload'] = 'Télécharger le dossier de suspension';
$string['link:viewstatus'] = 'Afficher la liste des statuts';
$string['msg:exclusion:cohort:none-selected'] = 'Aucune cohorte n\'a été sélectionnée pour l\'exclusion';
$string['msg:exclusion:record:cohort:inserted'] = 'L\'enregistrement d\'exclusion pour la cohorte « {$a->name} » a été inséré avec succès';
$string['msg:exclusion:record:exists'] = 'L\'enregistrement d\'exclusion existe déjà (aucun enregistrement ajouté)';
$string['msg:exclusion:record:inserted'] = 'Enregistrement d\'exclusion inséré avec succès';
$string['msg:exclusion:record:user:deleted'] = 'L\'enregistrement d\'exclusion pour l\'utilisateur « {$a->fullname} » a été supprimé avec succès';
$string['msg:exclusion:record:user:inserted'] = 'L\'enregistrement d\'exclusion pour l\'utilisateur « {$a->fullname} » a été inséré avec succès';
$string['msg:exclusion:records:cohort:deleted'] = 'Enregistrements d\'exclusion pour les cohortes supprimés avec succès';
$string['msg:exclusion:records:deleted'] = 'Les enregistrements d\'exclusion ont été supprimés avec succès';
$string['msg:exclusion:records:user:deleted'] = 'Enregistrements d\'exclusion pour les utilisateurs supprimés avec succès';
$string['msg:file-not-readable'] = 'Le fichier téléchargé « {$a} » n\'est pas lisible';
$string['msg:file-not-writeable'] = 'Le fichier téléchargé « {$a} » n\'est pas accessible en écriture ou ne peut pas être supprimé';
$string['msg:file-would-delete'] = 'Le fichier téléchargé serait maintenant supprimé (s\'il ne s\'agissait pas d\'une validation de configuration)';
$string['msg:file:upload:fail'] = 'Le fichier téléchargé n\'a pas pu être enregistré avec succès. Traitement interrompu.';
$string['msg:user:not-found'] = 'l\'utilisateur est introuvable';
$string['msg:user:suspend:failed'] = 'L\'utilisateur « {$a->username} » n\'a pas pu être suspendu';
$string['msg:user:suspend:nosuspendmode'] = 'L\'utilisateur « {$a->username} » n\'a pas été suspendu (en cours d\'exécution en mode test)';
$string['msg:user:suspend:success'] = 'L\'utilisateur « {$a->username} » a été suspendu';
$string['msg:user:unsuspend:failed'] = 'L\'utilisateur « {$a->username} » ne peut pas être réintégré';
$string['msg:user:unsuspend:nounsuspendmode'] = 'L\'utilisateur « {$a->username} » n\'a pas été réintégré (en cours d\'exécution en mode test)';
$string['msg:user:unsuspend:success'] = 'L\'utilisateur « {$a->username} » a été réintégré avec succès';
$string['notifications:allok'] = 'otre configuration semble correcte. Il ne semble pas y avoir de problèmes de configuration globaux détectés.';
$string['notify:load-exclude-list'] = 'Chargement de la liste d\'exclusion d\'utilisateurs';
$string['notify:load-file'] = 'Ouverture du fichier « {$a} »';
$string['notify:load-file-fail'] = 'Impossible d\'ouvrir le fichier « {$a} » en lecture';
$string['notify:suspend-excluded-user'] = 'l\'utilisateur : {$a->username} (id={$a->id}) est dans la liste d\'exclusion : pas de suspension';
$string['notify:suspend-user'] = 'utilisateur suspendu : {$a->username} (id={$a->id})';
$string['notify:unknown-suspend-type'] = 'Identifiant de type de suspension inconnu « {$a} »';
$string['page:view:exclude.php:introduction'] = '<p>Cette page affiche les exclusions configurées.<br/>
Les exclusions sont soit des utilisateurs, soit des cohortes complètement exclues de tout traitement automatisé.<br/>
Lorsqu\'une cohorte est exclue, cela signifie que tous les utilisateurs membres de la cohorte ne seront pas traités.
Utilisez les options de cette page pour ajouter des cohortes ou des utilisateurs à la liste d\'exclusion.</p>';
$string['page:view:log.php:introduction'] = 'Le tableau ci-dessous montre les journaux des statuts que les utilisateurs ont reçus ou traversés en tant que
résultant d\'un traitement automatisé ou de l\'utilisation de cet outil. Il affichera, selon votre configuration, l\'état de suspension ou l\'état supprimé de vos utilisateurs Moodle et le moment où certaines actions ont été effectuées.';
$string['page:view:notifications.php:introduction'] = 'Cet onglet affiche tous les problèmes potentiels détectés avec votre configuration de suspension d\'utilisateur.';
$string['page:view:statuslist.php:introduction:delete'] = '<p>Cet aperçu montre les comptes d\'utilisateurs qui seront supprimés d\'ici la période configurée des paramètres de cet outil</p>';
$string['page:view:statuslist.php:introduction:status'] = '<p>Cet aperçu montre les utilisateurs activement surveillés.<br/>
Les utilisateurs activement surveillés sont des utilisateurs qui sont réellement surveillés (ce qui signifie qu\'ils ne sont pas configurés pour être exclus de la surveillance).<br/>
Cette vue d\'ensemble diffère donc de la vue d\'ensemble de l\'administrateur principal en ce sens qu\'elle ne montrera <i>aucun</i> des utilisateurs qui ont été exclus de la surveillance de la suspension en utilisant les capacités de cet outil pour exclure des utilisateurs et des cohortes.</p>';
$string['page:view:statuslist.php:introduction:suspended'] = '<p>Cet aperçu montre les comptes d\'utilisateurs qui ont été suspendus.</p>';
$string['page:view:statuslist.php:introduction:tosuspend'] = '<p>Cet aperçu montre les comptes d\'utilisateurs qui seront suspendus d\'ici la période configurée dans les paramètres de cet outil</p>';
$string['pluginname'] = 'Suspension d\'utilisateur';
$string['privacy:metadata:tool_usersuspension:mailedto'] = 'Adresse de courriel de l\'utilisateur restauré';
$string['privacy:metadata:tool_usersuspension:mailsent'] = 'Si un courriel a été envoyé ou non';
$string['privacy:metadata:tool_usersuspension:status'] = 'Statut de suspension';
$string['privacy:metadata:tool_usersuspension:timecreated'] = 'Heure à laquelle l\'enregistrement a été créé.';
$string['privacy:metadata:tool_usersuspension:userid'] = 'La clé primaire de l\'utilisateur Moodle pour lequel le compte a été restauré.';
$string['privacy:metadata:tool_usersuspension_excl'] = 'Les exclusions des suspensions des utilisateurs stockent les utilisateurs exclus de la suspension automatique';
$string['privacy:metadata:tool_usersuspension_log'] = 'Le statut de suspension des utilisateurs stocke des informations historiques/de journal sur les utilisateurs suspendus';
$string['privacy:metadata:tool_usersuspension_status'] = 'Le statut de suspension des utilisateurs stocke des informations sur les utilisateurs suspendus';
$string['promo'] = 'Plugin de suspension des utilisateurs pour Moodle';
$string['promodesc'] = 'Ce plugin est écrit par Sebsoft Managed Hosting & Software Development
    (<a href=\'http://www.sebsoft.nl/\' target=\'_new\'>http://sebsoft.nl</a>).<br /><br />
    {$a}<br /><br />';
$string['setting:cleanlogsafter'] = 'Fréquence de nettoyage des journaux';
$string['setting:cleanup_deleteafter'] = 'Intervalle de suppression';
$string['setting:cleanup_interval'] = 'Intervalle de nettoyage';
$string['setting:dep:desc:uploaddetect_interval'] = 'Pour modifier l\'intervalle auquel le dossier de téléchargement est vérifié et traité,  veuillez ajuster l\'intervalle d\'exécution de la tâche planifiée dédiée pour faciliter ce processus <a href="{$a}/admin/tool/task/scheduledtasks.php">ici</a>
 (recherchez les tâches « \\tool_usersuspension\\task\\suspend\\fromfolder » et « \\tool_usersuspension\\task\\unsuspend\\fromfolder »).';
$string['setting:dep:uploaddetect_interval'] = 'Intervalle de traitement du dossier de téléchargement';
$string['setting:desc:cleanlogsafter'] = 'Configure la fréquence à laquelle les journaux sont nettoyés. Tous les journaux antérieurs à ce paramètre seront physiquement supprimés.';
$string['setting:desc:cleanup_deleteafter'] = 'Définit l\'intervalle auquel les utilisateurs sont supprimés après la suspension';
$string['setting:desc:cleanup_interval'] = 'Définit l\'intervalle auquel le nettoyage est effectué';
$string['setting:desc:enablecleanlogs'] = 'Active ou désactive le nettoyage automatique du journal d\'historique.';
$string['setting:desc:enablecleanup'] = 'Active ou désactive le nettoyage des utilisateurs';
$string['setting:desc:enabled'] = 'Active ou désactive l\'utilitaire de suspension d\'utilisateur';
$string['setting:desc:enablefromfolder'] = 'Active ou désactive l\'utilitaire de suspension d\'utilisateur pour suspendre automatiquement les utilisateurs d\'un fichier CSV téléchargé';
$string['setting:desc:enablefromupload'] = 'Active ou désactive l\'utilitaire de suspension d\'utilisateur à partir d\'un téléchargement de fichier';
$string['setting:desc:enablesmartdetect'] = 'Active ou désactive la détection intelligente de l\'utilitaire de suspension d\'utilisateur.';
$string['setting:desc:enablesmartdetectwarning'] = 'En cas d\'activation, cela enverra des courriels aux utilisateurs indiquant leur suspension imminente.';
$string['setting:desc:enableunsuspendfromfolder'] = 'Active ou désactive l\'utilitaire d\'annulation de la suspension des utilisateurs pour annuler automatiquement la suspension des utilisateurs d\'un fichier CSV téléchargé';
$string['setting:desc:senddeleteemail'] = 'Envoyer un courriel informant l\'utilisateur après sa suppression ?';
$string['setting:desc:sendsuspendemail'] = 'Envoyer un courriel informant l\'utilisateur après sa suspension ?';
$string['setting:desc:smartdetect_interval'] = 'Définit l\'intervalle auquel la détection intelligente s\'exécute';
$string['setting:desc:smartdetect_suspendafter'] = 'Définit l\'intervalle auquel les utilisateurs sont suspendus lorsqu\'ils sont inactifs';
$string['setting:desc:smartdetect_warninginterval'] = 'Définit le délai avant la suspension auquel un utilisateur reçoit un message d\'avertissement concernant la suspension imminente.';
$string['setting:desc:unsuspenduploadfilename'] = 'Définir le nom de fichier du fichier d\'annulation de la suspension téléchargé';
$string['setting:desc:uploaddetect_interval'] = 'Définit l\'intervalle auquel le dossier de téléchargement est vérifié pour les fichiers';
$string['setting:desc:uploadfilename'] = 'Définit le nom de fichier du fichier suspendu téléchargé';
$string['setting:desc:uploadfolder'] = 'Définit le dossier dans lequel les fichiers doivent être téléchargés, par ex. FTP';
$string['setting:enablecleanlogs'] = 'Activer le nettoyage des journaux';
$string['setting:enablecleanup'] = 'Activer le nettoyage';
$string['setting:enabled'] = 'Activer';
$string['setting:enablefromfolder'] = 'Suspension automatique à l\'aide du fichier CSV stocké';
$string['setting:enablefromupload'] = 'Activer à partir du téléchargement';
$string['setting:enablesmartdetect'] = 'Activer la détection intelligente';
$string['setting:enablesmartdetectwarning'] = 'Envoyer des courriels d\'avertissement concernant une suspension imminente ?';
$string['setting:enableunsuspendfromfolder'] = 'Envoyer des courriels d\'avertissement concernant une suspension imminente ?';
$string['setting:senddeleteemail'] = 'Envoyer un courriel de suppression ?';
$string['setting:sendsuspendemail'] = 'Envoyer un courriels de suspension ?';
$string['setting:smartdetect_interval'] = 'Intervalle de détection intelligent';
$string['setting:smartdetect_suspendafter'] = 'Intervalle de suspension d\'inactivité';
$string['setting:smartdetect_warninginterval'] = 'Période d\'avertissement';
$string['setting:unsuspenduploadfilename'] = 'Annuler la suspension du nom du fichier de téléchargement';
$string['setting:uploaddetect_interval'] = 'Intervalle de traitement du dossier de téléchargement';
$string['setting:uploadfilename'] = 'Nom de fichier de téléchargement des utilisateurs suspendus';
$string['setting:uploadfolder'] = 'Télécharger le dossier';
$string['status:deleted'] = 'supprimé';
$string['status:suspended'] = 'suspendu';
$string['status:unsuspended'] = 'suspension annulée';
$string['suspend'] = 'Suspendre';
$string['suspendmode'] = 'Mode de traitement';
$string['suspendon'] = 'Suspendre en';
$string['suspensionsettings'] = 'Paramètres de suspension d\'utilisateur';
$string['suspensionsettingscleanup'] = 'Nettoyer';
$string['suspensionsettingscleanupdesc'] = 'Configurer les paramètres de nettoyage ci-dessous.<br/>
Le processus de nettoyage est là pour automatiser davantage le nettoyage des utilisateurs, ce qui signifie que les comptes d\'utilisateurs suspendus seront supprimés lorsque cette option est utilisée. Si les comptes d\'utilisateurs doivent être automatiquement supprimés après un certain temps, vous devez configurer ces paramètres.
Si la suppression automatique des comptes d\'utilisateurs n\'est en aucun cas souhaitée, vous devez désactiver cette option.';
$string['suspensionsettingsfolder'] = 'Suspendre du dossier';
$string['suspensionsettingsfolderdesc'] = 'Configurer ci-dessous les paramètres du dossier « Suspendre du dossier ».<br/>
À l\'aide de ces paramètres, vous pouvez automatiser la suspension des utilisateurs en téléchargeant un fichier CSV à un emplacement aléatoire sur le serveur (par exemple un dossier FTP dédié). Le fichier CSV sera traité conformément aux paramètres ci-dessous.
Remarque : Le fichier CSV téléchargé sera supprimé après le traitement !';
$string['suspensionsettingssmartdetect'] = 'Détection intelligente';
$string['suspensionsettingssmartdetectdesc'] = 'Configurer les paramètres de détection intelligente ci-dessous.<br/>
La détection intelligente signifie effectivement que les comptes d\'utilisateurs qui ont été trouvés « inactifs » selon les paramètres ci-dessous seront
automatiquement suspendus. S\'exécutant uniquement à un intervalle configuré, la « détection intelligente » déterminera si un compte utilisateur
est actif selon le paramètre « Intervalle de suspension d\'inactivité » configuré et suspendra tous les comptes d\'utilisateurs jugés inactifs.';
$string['suspensionsettingsupload'] = 'Suspendre du téléchargement';
$string['suspensionsettingsuploaddesc'] = 'Configurer les paramètres « suspendre du téléchargement » ci-dessous';
$string['tab:notifications'] = 'Vérification de la configuration';
$string['table:exclusions'] = 'Exclusions';
$string['table:log:all'] = 'Historique du journal de suspension';
$string['table:log:latest'] = 'Derniers journaux de suspension';
$string['table:logs'] = 'Journaux';
$string['table:status:delete'] = 'Utilisateurs à supprimer';
$string['table:status:status'] = 'Utilisateurs surveillés activement';
$string['table:status:suspended'] = 'Utilisateurs suspendus';
$string['table:status:tosuspend'] = 'Utilisateurs à suspendre';
$string['task:delete'] = 'Tâche de suspension des utilisateurs : suppression automatique des utilisateurs suspendus';
$string['task:fromfolder'] = 'Tâche de suspension des utilisateurs : suspension automatique de l\'utilisateur à partir du fichier téléchargé';
$string['task:logclean'] = 'Nettoyer les journaux pour la suspension de l\'utilisateur';
$string['task:mark'] = 'Tâche de suspension des utilisateurs : suspension automatique des utilisateurs';
$string['task:unsuspendfromfolder'] = 'Tâche de suspension des utilisateurs : activation automatique de l\'utilisateur (annulation de la suspension) à partir du fichier téléchargé';
$string['testfromfolder'] = 'Tester le traitement sans surveillance';
$string['testfromfolder:suspend'] = 'Tester la suspension sans surveillance des utilisateurs (à partir du dossier)';
$string['testfromfolder:unsuspend'] = 'Tester l\'annulation de la suspension sans surveillance des utilisateurs (à partir du dossier)';
$string['testing:suspendfromfolder'] = 'Configuration de test pour « suspendre du dossier »';
$string['testing:unsuspendfromfolder'] = 'Configuration de test pour « annuler la suspension du dossier »';
$string['thead:action'] = 'Action(s)';
$string['thead:deletein'] = 'Supprimer dans';
$string['thead:lastlogin'] = 'Dernière connexion';
$string['thead:mailedto'] = 'Envoyé par courriel à';
$string['thead:mailsent'] = 'Courriel envoyé';
$string['thead:name'] = 'Nom';
$string['thead:status'] = 'Statut';
$string['thead:suspendin'] = 'Suspendre dans';
$string['thead:timecreated'] = 'Heure de création';
$string['thead:timedetect'] = 'Détection basée sur';
$string['thead:timemodified'] = 'Heure de modification';
$string['thead:type'] = 'Type';
$string['thead:userid'] = 'Identifiant d\'utilisateur';
$string['thead:username'] = 'Nom d\'utilisateur';
$string['unsuspend'] = 'Annuler la suspension';
$string['usersuspension:administration'] = 'Administration des suspensions d\'utilisateurs';
$string['usersuspension:viewstatus'] = 'Afficher l\'état de la suspension de l\'utilisateur';
