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
 * Strings for component 'local_learning_analytics', language 'fr', version '4.1'.
 *
 * @package     local_learning_analytics
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admin_customfield_category'] = 'Renommer la catégorie';
$string['admin_customfield_category_hint'] = 'Cliquer sur l\'icône en forme de crayon à côté de la catégorie à renommer';
$string['admin_customfield_info'] = 'Vous pouvez modifier vous-même le nom de l\'option dans les paramètres du cours, en les renommant sur les pages suivantes :';
$string['admin_customfield_no_manual_delete'] = 'Ne supprimez pas la catégorie ou l\'option. Vous pouvez seulement les renommer. Si vous voulez supprimer l\'option, modifiez plutôt le statut ci-dessus.';
$string['admin_customfield_option'] = 'Renommer l\'option';
$string['admin_customfield_option_hint'] = 'Vous ne pouvez modifier que le nom et le texte de la description';
$string['customfield_category_description'] = 'Cette catégorie a été créée automatiquement par le plugin Learning Analytics (local_learning_analytics). Vous ne devez pas la supprimer manuellement.';
$string['customfield_category_name'] = 'Analyse des apprentissages';
$string['customfield_field_description'] = 'L\'activation ajoute le lien « Statistiques d\'utilisation » à la navigation de votre cours.';
$string['customfield_field_name'] = 'Statistiques d\'utilisation';
$string['dataprivacy_threshold_description'] = 'Cette valeur détermine le nombre de points de données qu\'un ensemble de données doit contenir avant que les données ne soient affichées.';
$string['general_settings'] = 'Paramètres généraux';
$string['help_faq'] = 'Questions fréquemment posées';
$string['help_faq_data_storage_answer'] = 'Les données présentées proviennent de deux sources de données. Les deux sources sont stockées dans la base de données interne de Moodle. La première source de données est la base de données interne de Moodle, comme le nombre d\'étudiants dans le cours (case de gauche dans le tableau de bord). Certains de ces ensembles de données peuvent être visualisés sur d\'autres pages Moodle, et sont simplement visualisés différemment sur ces pages. La deuxième source de données est constituée de données collectées spécifiquement pour être présentées sur la page des statistiques d\'utilisation. Toutes les données collectées à cette fin sont stockées de manière anonyme et ne peuvent être rattachées à des utilisateurs individuels. Plus précisément, les données suivantes sont stockées avec chaque requête dans Moodle :';
$string['help_faq_data_storage_answer_list'] = 'Type d\'action (par exemple : Ressource consultée)
Heure (exacte à la seconde près)
Cours correspondant dans lequel l\'action a eu lieu
Contexte correspondant (par exemple la ressource consultée)
Système d\'exploitation et navigateur (par exemple, Windows 10 et Firefox), les versions détaillées du navigateur ou du système d\'exploitation ne sont pas stockées.';
$string['help_faq_data_storage_question'] = 'Quelles sont les données stockées et affichées par le service ?';
$string['help_faq_datapoints_answer'] = 'Les nombres affichés indiquent le nombre de requêtes. Ces nombres peuvent varier considérablement en fonction du type de matériel.
Par exemple, un seul accès est enregistré lorsqu\'un PDF est téléchargé.
Cependant, dans le cas d\'un test où les questions sont réparties sur plusieurs pages, il y aura également plusieurs accès enregistrés au cours d\'un seul test.';
$string['help_faq_datapoints_question'] = 'Pourquoi le nombre d\'occurrences est-il si différent pour les différents types de matériaux ?';
$string['help_faq_developer_answer'] = 'Le service Analyse des apprentissages est développé par le Centre pour les services d\'enseignement et d\'apprentissage de l\'Université RWTH Aachen (Allemagne). Le développement est Open Source. Vous pouvez donc vérifier vous-même les algorithmes utilisés. Sur les pages suivantes, vous pouvez accéder à des informations supplémentaires :';
$string['help_faq_developer_question'] = 'Qui développe le service et où puis-je obtenir de plus amples informations ?';
$string['help_faq_personal_data_answer'] = 'Pour des raisons de confidentialité des données, le service Analyse des apprentissages ne collecte pas de données personnalisées. Par conséquent, la plupart des statistiques affichées (par exemple, les activités les plus utilisées) contiennent uniquement des informations sur la fréquence d\'accès à une ressource, mais pas sur le nombre de participants. Une exception à cette règle est constituée par les statistiques sur les participants et les Devoirs/Tests, qui affichent également des données internes à Moodle.';
$string['help_faq_personal_data_question'] = 'Pourquoi aucune statistique personnalisée, comme les requêtes par utilisateur, n\'est-elle affichée ?';
$string['help_faq_privacy_threshold_answer'] = 'Pour des raisons de confidentialité des données, les données agrégées ne sont affichées que si au moins {$a} points de données sont présents.';
$string['help_faq_privacy_threshold_question'] = 'Pourquoi certaines valeurs sont-elles affichées comme < {$a} ?';
$string['help_faq_visibility_answer'] = 'Pour garantir une transparence maximale, les données affichées peuvent être consultées par les propriétaires du cours ainsi que par les participants au cours.';
$string['help_faq_visibility_question'] = 'Qui peut accéder aux statistiques d\'utilisation ?';
$string['help_faq_week_start_answer'] = 'Important : la visualisation dans le tableau de bord dépend du paramètre "Date de début du cours" dans les paramètres du cours. Si la date indiquée à cet endroit ne correspond pas au début réel du cours, l\'affichage dans le tableau de bord ne sera pas correct non plus. Le gestionnaire de ce cours peut corriger le paramètre dans les paramètres du cours (dans Général / Date de début du cours).';
$string['help_faq_week_start_question'] = 'Pourquoi la première semaine dans le graphique du tableau de bord ne correspond-elle pas au début réel du cours ?';
$string['help_take_tour'] = 'Démarrer la visite guidée';
$string['help_text'] = 'La page Statistiques d\'utilisation du service Analyse des apprentissages vous montre les principales mesures de votre cours. Les statistiques affichées sont en direct et présentent les données collectées par le service ainsi que les données du cours Moodle lui-même. Toutes les données collectées par le service Analyse des apprentissages le sont de manière totalement anonyme et ne peuvent être rattachées aux utilisateurs.';
$string['help_title'] = 'Aide';
$string['hits'] = 'Vues';
$string['learners'] = 'Apprenants';
$string['learning_analytics'] = 'Analyse des apprentissages';
$string['learning_analytics:view_statistics'] = 'Accéder aux statistiques d\'utilisation des cours';
$string['navigation_position_beforekey_description'] = 'Permet de spécifier où dans la navigation du cours le lien vers la page est ajouté. Par défaut, le lien est ajouté avant le premier nœud (section). Exemple de valeur : <code>grades</code> pour être affiché avant le lien vers les notes. Vous pouvez trouver la clé d\'un nœud dans la navigation en utilisant les outils de développement. Faites un clic droit sur un lien dans la navigation, appuyez sur <em>Inspecter</em> et vérifiez l\'attribut <code>data-key</code> de l\'élément <code>a</code> correspondant.';
$string['navigationlink'] = 'Statistiques d\'utilisation';
$string['pluginname'] = 'Analyse des apprentissages';
$string['privacy:metadata'] = 'Ce plugin ne stocke pas de données personnelles.';
$string['sessions'] = 'Sessions';
$string['setting_course_ids_description'] = 'Utilisez cette option, lorsque vous sélectionnez la deuxième option ci-dessus, pour décider pour quels cours l\'interface utilisateur doit être activée.';
$string['setting_dashboard_boxes'] = 'Détermine les cases qui sont affichées dans le tableau de bord, dans quel ordre et la taille des cases. La spécification se présente sous le format <code>reportname:width</code>, séparé par des virgules. Une ligne a une largeur maximale de 12, après quoi elle s\'interrompt. Exemple : <code>learners:8,activities:4</code> affiche deux cases dans le tableau de bord, où la première est beaucoup plus large que la seconde. Dans la plupart des cas, la valeur ne doit être modifiée que si d\'autres sous-plugins sont installés.';
$string['setting_status_course_customfield'] = 'Ajouter une option dans les paramètres du cours pour que les enseignants puissent l\'activer eux-mêmes.';
$string['setting_status_description'] = 'Cette valeur détermine si l\'interface utilisateur doit être activée et si un lien doit être affiché dans la navigation. Par défaut, le lien et la page sont visibles si la connexion est activée pour le cours. Vous pouvez utiliser cette option si vous souhaitez activer la connexion dans tous les cours, mais ne souhaitez activer l\'interface utilisateur que dans certains cours.';
$string['setting_status_option_disable'] = 'Masquer le lien de navigation et désactiver la page pour tous les cours';
$string['setting_status_option_hidelink'] = 'Cacher le lien de navigation mais garder la page activée pour tous les cours (vous ne pourrez accéder à la page que si vous en connaissez le lien)';
$string['setting_status_option_show_always'] = 'Afficher le lien de navigation pour tous les cours, même si l\'enregistrement est désactivé pour le cours (n\'activez cette option que si vous avez déjà enregistré des données auparavant).';
$string['setting_status_option_show_courseids'] = 'Afficher le lien de navigation si le cours est spécifié ci-dessous via course_ids';
$string['setting_status_option_show_if_enabled'] = 'Afficher le lien de navigation si la connexion est activée pour le cours.';
$string['setting_student_enrols_groupby_description'] = 'Cette option définit quels cours sont fusionnés dans la page (Cours précédent/parallèle) et quelle valeur est utilisée pour afficher les noms.';
$string['setting_student_rolenames_description'] = 'Dans le cas où le(s) rôle(s) des étudiants/utilisateurs dans un cours n\'est pas <code>student</code>, vous pouvez spécifier le nom du rôle correspondant. Dans le cas où les étudiants ont plusieurs rôles, utilisez une seule virgule. Exemple : <code>student,customrole</code>.';
$string['show_full_list'] = 'Développer la liste';
$string['subplugintype_lalog'] = 'Journal L.A.';
$string['subplugintype_lalog_plural'] = 'Journaux L.A.';
$string['subplugintype_lareport'] = 'Rapport L.A.';
$string['subplugintype_lareport_plural'] = 'Rapports L.A.';
$string['tour_activities'] = 'La dernière vue montre les trois activités les plus utilisées au cours des 7 derniers jours. En cliquant sur le lien, vous pouvez voir les détails des activités du cours.';
$string['tour_box_hits'] = 'Cet indicateur montre le nombre d\'actions au cours des 7 derniers jours. Vous pouvez voir ci-dessous les changements survenus au cours des 7 derniers jours. En cliquant sur le lien, vous pouvez ouvrir une carte qui visualise le nombre d\'actions sur l\'ensemble du semestre.';
$string['tour_box_learners'] = 'Le premier indicateur montre le nombre total de participants. Sous le grand nombre, l\'évolution par rapport à la semaine précédente est indiquée.';
$string['tour_box_learnerslink'] = 'Cliquer sur le lien pour obtenir plus d\'informations sur les participants.';
$string['tour_box_quiz_assign'] = 'Cette page montre le nombre de tentatives de tests et de remises de devoirs au cours des 7 derniers jours. En dessous, l\'évolution par rapport aux 7 jours précédents est à nouveau indiquée. En cliquant sur le lien, vous pouvez consulter les détails des quiz et des devoirs du cours.';
$string['tour_dashboard_boxes'] = 'Dans la zone inférieure, les chiffres clés importants du cours sont affichés. En outre, chaque case contient un lien permettant d\'obtenir des informations complémentaires.';
$string['tour_dashboard_graph'] = 'Le graphique montre le nombre de toutes les actions de la semaine en cours. S\'il s\'agit d\'un cours en cours, le début de la semaine en cours est marqué par une ligne pointillée. Les chiffres de la semaine en cours ne sont pas affichés.';
$string['tour_more_information'] = 'La visite interactive est maintenant terminée. Nous espérons avoir pu vous donner un bon aperçu des fonctionnalités. Vous trouverez d\'autres réponses aux questions fréquemment posées sur la page d\'aide.';
$string['tour_title'] = 'Analyse des apprentissages';
