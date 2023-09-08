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
 * Strings for component 'enrol_autoenrol', language 'fr', version '4.1'.
 *
 * @package     enrol_autoenrol
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alwaysenrol'] = 'Toujours inscrire';
$string['alwaysenrol_help'] = 'Si vous choisissez « Oui », le plugin inscrira toujours les utilisateurs, même s\'ils ont accès au cours par une autre méthode.';
$string['auto'] = 'Auto';
$string['auto_desc'] = 'Ce groupe a été créé automatiquement par le plugin Inscription Automatique. Il sera supprimé si vous retirez le plugin Inscription Automatique du cours.';
$string['autoenrol:config'] = 'Configurer les inscriptions automatiques';
$string['autoenrol:hideshowinstance'] = 'L\'utilisateur peut activer ou désactiver les instances d\'inscription automatique';
$string['autoenrol:manage'] = 'Gérer les utilisateurs inscrits automatiquement';
$string['autoenrol:method'] = 'L\'utilisateur peut inscrire des utilisateurs dans un cours à la connexion';
$string['autoenrol:unenrol'] = 'L\'utilisateur peut désinscrire des utilisateurs inscrits automatiquement';
$string['autoenrol:unenrolself'] = 'L\'utilisateur peut se désinscrire lui-même si son inscription provient de l\'inscription automatique';
$string['autounenrolaction'] = 'Action en cas de désinscription automatique';
$string['autounenrolaction_help'] = 'Sélectionner l\'action à effectuer lorsque la règle de filtrage des utilisateurs ne correspond plus. Veuillez noter que certaines données et paramètres utilisateur sont supprimés du cours lors de la désinscription au cours.';
$string['availabilityplugins'] = 'Plugins de disponibilité activés';
$string['availabilityplugins_help'] = 'Sélectionner les plugins disponibles qui peuvent être utilisés dans le filtre utilisateurs de l\'inscription automatique. Utilisez Ctrl+clic ou Cmd+clic pour une sélection multiple.';
$string['cannotenrol'] = 'Vous ne pouvez pas vous inscrire à ce cours en utilisant l\'inscription automatique.';
$string['checksync'] = 'Vérifier la synchronisation avec les utilisateurs de {$a}';
$string['config'] = 'Configuration';
$string['confirmbulkdeleteenrolment'] = 'Voulez-vous vraiment supprimer ces inscriptions d\'utilisateurs ?';
$string['countlimit'] = 'Limite';
$string['countlimit_help'] = 'Cette instance limitera le nombre d\'inscriptions autorisées pour le cours afin d\'empêcher les utilisateurs de s\'inscrire une fois que la limite d\'inscrits est atteinte. Le réglage par défaut sur 0 signifie que le nombre d\'inscrits au cours est illimité.';
$string['customwelcomemessage'] = 'Message de bienvenue personnalisé';
$string['customwelcomemessage_help'] = 'Un message de bienvenue personnalisé peut être ajouté sous forme de texte brut ou en auto-format Moodle, y compris les balises HTML et les balises multilingues.

Les paramètres suivants peuvent être inclus dans le message :

* Nom du cours {$a->coursename}
* Lien vers la page de profil de l\'utilisateur {$a->profileurl}
* Lien vers le cours {$a->link}
* Courriel de l\'utilisateur {$a->email}
* Nom complet de l\'utilisateur {$a->fullname}';
$string['defaultrole'] = 'Attribution du rôle par défaut';
$string['defaultrole_desc'] = 'Sélectionnez le rôle qui sera donné par défaut à tout utilisateur inscrit automatiquement';
$string['deleteselectedusers'] = 'Supprimer les inscriptions d\'utilisateurs sélectionnés';
$string['editselectedusers'] = 'Modifier les inscriptions d\'utilisateurs sélectionnés';
$string['emptyfield'] = 'Non {$a}';
$string['enrolenddate'] = 'Date de fin';
$string['enrolenddate_help'] = 'Si cette option est activée, les utilisateurs seront inscrits jusqu\'à cette date uniquement.';
$string['enrolme'] = 'M\'inscrire';
$string['enrolperiod'] = 'Durée d\'inscription';
$string['enrolperiod_desc'] = 'Durée par défaut de validité de l\'inscription. Si elle est définie sur zéro, la durée d\'inscription sera illimitée par défaut.';
$string['enrolperiod_help'] = 'Durée de validité de l\'inscription, à partir du moment où l\'utilisateur s\'inscrit. Si elle est désactivée, la durée d\'inscription sera illimitée.';
$string['enrolstartdate'] = 'Date de début';
$string['enrolstartdate_help'] = 'Si cette option est activée, les utilisateurs seront inscrits à partir de cette date uniquement.';
$string['expiredaction'] = 'Action d\'expiration d\'inscription';
$string['expiredaction_help'] = 'Sélectionner l\'action à exécuter lorsque l\'inscription de l\'utilisateur expire. Veuillez noter que certaines données et paramètres utilisateur sont supprimés du cours lors de la désinscription au cours.';
$string['expirymessageenrolledbody'] = 'Bonjour {$a->user},

Ceci est une notification indiquant que votre inscription au cours \'{$a->course}\' va expirer le {$a->timeend}.

Si vous avez besoin d\'aide, veuillez contacter {$a->enroller}.';
$string['expirymessageenrolledsubject'] = 'Notification d\'expiration de l\'inscription automatique';
$string['expirymessageenrollerbody'] = 'L\'inscription automatique au cours \'{$a->course}\' expirera dans {$a->threshold} pour les utilisateurs suivants :

{$a->users}

Pour prolonger leur inscription, rendez-vous sur {$a->extendurl}';
$string['expirymessageenrollersubject'] = 'Notification d\'expiration de l\'inscription automatique';
$string['expirynotifyall'] = 'Enseignant et utilisateur inscrit';
$string['expirynotifyenroller'] = 'Enseignant seulement';
$string['filter'] = 'Autoriser seulement';
$string['filter_help'] = 'Lorsqu\'un groupe est sélectionné, vous pouvez utiliser ce champ pour filtrer le type d\'utilisateur que vous souhaitez inscrire dans le cours. Par exemple, si vous avez regroupé par authentification et filtré avec « manuel », seuls les utilisateurs qui se sont inscrits manuellement à votre site seront automatiquement inscrits dans ce cours.';
$string['filtering'] = 'Filtre Utilisateur';
$string['g_auth'] = 'Méthode d\'authentification';
$string['g_dept'] = 'Département';
$string['g_email'] = 'Adresse de courriel';
$string['g_inst'] = 'Institution';
$string['g_lang'] = 'Langue';
$string['g_none'] = 'Sélection…';
$string['general'] = 'Général';
$string['groupname'] = 'Nom du groupe';
$string['groupname_help'] = 'Lorsque vous regroupez par Filtre Utilisateur, seul un groupe sera créé et ce sera le nom du groupe.';
$string['groupon'] = 'Grouper par';
$string['groupon_help'] = 'L\'inscription automatique permet d\'ajouter automatiquement des utilisateurs à un groupe quand leur inscription est basée sur un de ces champs.';
$string['instancename'] = 'Nom personnalisé de l\'instance';
$string['instancename_help'] = 'Vous pouvez ajouter un nom personnalisé pour l\'instance de cette méthode d\'inscription pour indiquer clairement ce qu\'elle fait. Cette option est particulièrement utile quand il y a plusieurs instances d\'inscription automatique dans le cours.';
$string['loginenrol'] = 'Autoriser les inscriptions à la connexion';
$string['loginenrol_desc'] = 'Autoriser l\'inscription à la connexion pourrait ralentir les performances de votre site. Comme alternative, vous pouvez utiliser la tâche planifiée pour mettre à jour les inscriptions pour tous les cours, ou la commande cli pour des cours spécifiques.';
$string['longtimenosee'] = 'Désinscrire les inactifs après';
$string['longtimenosee_help'] = 'Si les utilisateurs n\'ont pas accédé à un cours pendant une longue période, ils sont automatiquement désinscrits. Ce paramètre spécifie cette limite de temps.';
$string['m_confirmation'] = 'Confirmation sur l\'écran d\'inscription';
$string['m_course'] = 'Chargement du cours';
$string['m_site'] = 'Connexion au site';
$string['maxenrolled'] = 'Nombre d\'inscrits maximum';
$string['maxenrolled_help'] = 'Spécifie le nombre maximal d\'utilisateurs pouvant s\'inscrire automatiquement. 0 signifie aucune limite.';
$string['messageprovider:expiry_notification'] = 'Notifications d\'expiration de l\'inscription automatique';
$string['method'] = 'Inscrit quand';
$string['method_help'] = 'Les utilisateurs autorisés peuvent utiliser ce paramètre pour changer le comportement du plugin afin que les utilisateurs soient inscrits au cours lors de la connexion, plutôt que d\'attendre d\'accéder eux-mêmes au cours. Ceci est utile pour les cours qui doivent être visibles par défaut dans la liste « Mes cours » du tableau de bord de l\'utilisateur.';
$string['newenrols'] = 'Autoriser les nouvelles inscriptions';
$string['newenrols_desc'] = 'Autoriser les utilisateurs à s\'inscrire automatiquement aux nouveaux cours par défaut.';
$string['newenrols_help'] = 'Ce paramètre détermine si un utilisateur peut s\'inscrire à ce cours.';
$string['nogroupon'] = 'Ne pas créer de groupes';
$string['pluginname'] = 'Inscription automatique';
$string['pluginname_desc'] = 'Le module d\'inscription automatique donne la possibilité aux utilisateurs connectés d\'accéder à un cours et de s\'y inscrire automatiquement. Cela revient à autoriser l\'accès en tant qu\'invité, à la différence que les utilisateurs seront inscrits en permanence et donc en mesure de participer aux forums et aux activités dans le cours.';
$string['pluginnotenabled'] = 'Plugin d\'inscription automatique non activé';
$string['privacy:metadata:core_group'] = 'Le plugin d\'inscription automatique peut créer de nouveaux groupes ou utiliser des groupes existants pour ajouter des participants qui correspondent au filtre d\'inscription automatique.';
$string['removegroups'] = 'Supprimer les groupes';
$string['removegroups_desc'] = 'Lorsqu\'une instance d\'inscription est supprimée, doit-elle tenter de supprimer les groupes qu\'elle a créés ?';
$string['role'] = 'Rôle attribué par défaut';
$string['role_help'] = 'Les utilisateurs autorisés peuvent utiliser ce paramètre pour modifier les permissions d\'accès des utilisateurs inscrits.';
$string['selfunenrol'] = 'Activer l\'auto-désinscription';
$string['selfunenrol_desc'] = 'Autoriser les utilisateurs à se désinscrire par défaut dans les nouvelles instances d\'inscription automatique.';
$string['selfunenrol_help'] = 'Lorsque défini sur « Oui », les utilisateurs peuvent se désinscrire eux-mêmes.';
$string['sendcoursewelcomemessage'] = 'Envoyer un message de bienvenue au cours';
$string['sendcoursewelcomemessage_help'] = 'Lorsqu\'un utilisateur est inscrit automatiquement au cours, un courriel de bienvenue peut lui être envoyé. S\'il est envoyé par le contact du cours (par défaut, l\'enseignant) et que plusieurs utilisateurs ont ce rôle, le courriel est envoyé par le premier utilisateur auquel le rôle a été attribué.';
$string['sendexpirynotificationstask'] = 'Tâche d\'envoi de notifications d\'expiration d\'inscription automatique';
$string['softmatch'] = 'Recherche souple';
$string['softmatch_help'] = 'Si l\'option est activée, l\'inscription automatique inscrira un utilisateur quand le motif recherché correspondra partiellement à la valeur indiquée dans le champ « Autoriser seulement », au lieu d\'exiger une correspondance exacte. Les recherches souples sont également insensibles à la casse. La valeur de « Filtrer par » sera utilisée pour le nom du groupe.';
$string['status'] = 'Autoriser les inscriptions existantes';
$string['status_desc'] = 'Activer la méthode d\'inscription automatique dans les nouveaux cours.';
$string['status_help'] = 'Si cette option est activée alors que l\'option « Autoriser les nouvelles inscriptions » est désactivée, seuls les utilisateurs qui se sont inscrits automatiquement précédemment peuvent accéder au cours. Si désactivée, cette méthode d\'inscription automatique est effectivement désactivée, puisque toutes les inscriptions automatiques existantes sont alors suspendues et les nouveaux utilisateurs ne peuvent alors pas s\'inscrire automatiquement.';
$string['syncenrolmentstask'] = 'Synchroniser la tâche d\'inscription automatique';
$string['syncexpirationstask'] = 'Tâche de vérification des expirations d\'inscription automatique';
$string['unenrolselfconfirm'] = 'Voulez-vous vraiment vous désinscrire du cours « {$a} » ? Vous pouvez revenir dans le cours pour vous réinscrire, mais des informations comme les notes et les rendus de devoir pourront être perdues.';
$string['unenrolusers'] = 'Désinscrire des utilisateurs';
$string['userfilter'] = 'Filtre Utilisateur';
$string['userfilter_help'] = 'Lorsque défini, l\'inscription automatique inscrira les utilisateurs uniquement lorsqu\'ils correspondent aux règles.';
$string['warning'] = 'Attention !';
$string['warning_message'] = 'L\'ajout de ce plugin à votre cours permettra à tous les utilisateurs enregistrés sur Moodle d\'accéder au cours. N\'installez ce plugin que si vous souhaitez autoriser l\'accès libre à votre cours pour les utilisateurs qui sont connectés.';
$string['welcomemessage'] = 'Message de bienvenue';
$string['welcometocourse'] = 'Bienvenue sur le cours {$a}';
$string['welcometocoursetext'] = 'Bienvenue sur le cours {$a->coursename} !

Si vous ne l\'avez pas déjà fait, nous vous invitons à modifier votre page de profil afin que nous puissions en savoir plus sur vous :

  {$a->profileurl}';
