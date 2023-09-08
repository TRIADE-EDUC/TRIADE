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
 * Strings for component 'local_boostnavigation', language 'fr', version '4.1'.
 *
 * @package     local_boostnavigation
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['inc_bottomnodes'] = 'nœuds inférieurs';
$string['inc_bottomnodeslocation'] = 'en dessous de la section principale du tiroir de navigation (similaire au nœud « administration du site » )';
$string['inc_coursenode'] = 'nœud de cours';
$string['inc_coursenodes'] = 'nœuds de cours';
$string['inc_coursenodeslocation'] = 'dans la section cours du tiroir de navigation (à l\'endroit où se trouve les nœuds de section du cours)';
$string['inc_custombottomnodes'] = 'nœuds inférieurs personnalisés';
$string['inc_customcoursenodes'] = 'nœuds de cours personnalisés';
$string['inc_customrootnodes'] = 'nœuds racines personnalisés';
$string['inc_customwhoadmins'] = 'les administrateurs';
$string['inc_customwhousers'] = 'tous les utilisateurs';
$string['inc_mycoursesrootnode'] = 'nœud racine « Mes cours »';
$string['inc_notselectedhomenode'] = 'Accueil du site / Tableau de bord non sélectionné';
$string['inc_rootnode'] = 'nœud racine';
$string['inc_rootnodes'] = 'nœuds racines';
$string['inc_rootnodeslocation'] = 'dans la section principale du tiroir de navigation (à l\'endroit où se trouvent le nœud « Mes cours » et les autres)';
$string['inc_selectedhomenode'] = 'Accueil du site / Tableau de bord sélectionné';
$string['mycoursesrootnodefilterhintenabledcourselist'] = 'Liste des cours filtrée par :';
$string['mycoursesrootnodefilterhintenabledcurrentfilter'] = 'Filtre de cours actif :';
$string['mycoursesrootnodefilterlink'] = 'Changer de filtre';
$string['pluginname'] = 'Personnaliser le tiroir de navigation';
$string['privacy:metadata:preference:collapse'] = 'État (déplié/replié) d\'un nœud dépliable dans le tiroir de navigation';
$string['privacy:request:preference:collapse'] = 'L\'état du nœud « {$a->nodename} » dans le tiroir de navigation est {$a->collapse}';
$string['setting_collapsecustomnodes'] = 'Replier {$a->what} pour {$a->who}';
$string['setting_collapsecustomnodes_desc'] = 'L\'activation de ce paramètre permettra aux utilisateurs de replier le {$a->what} pour {$a->who}.<br /><em>Ce paramètre n\'est traité que lorsque le paramètre du nœud personnalisé correspondant a au moins un nœud personnalisé avec au moins un nœud enfant ajouté.</em>';
$string['setting_collapsecustomnodesaccordion'] = 'Replier {$a->what} pour {$a->who} en accordéon';
$string['setting_collapsecustomnodesaccordion_desc'] = 'L\'activation de se paramètre va replier le {$a->what} pour {$a->who} en accordéon.';
$string['setting_collapsecustomnodesdefault'] = 'Replier {$a->what} pour {$a->who} par défaut';
$string['setting_collapsecustomnodesdefault_desc'] = 'L\'activation de ce paramètre va replier le {$a->what} pour {$a->who} par défaut.';
$string['setting_collapsecustomnodesicon'] = 'Afficher l\'icône du nœud parent dans {$a->what} pour {$a->who}';
$string['setting_collapsecustomnodesicon_desc'] = 'Avec ce paramètre, vous contrôlez si l\'icône du nœud parent sera affichée dans le {$a->what} pour {$a->who} et / ou si les nœuds parents seront indentés';
$string['setting_collapsecustomnodessession'] = 'Se souvenir de l\'état de repli du {$a->what} pour {$a->who} seulement pour la session en cours';
$string['setting_collapsecustomnodessession_desc'] = 'L\'activation de ce paramètre retiendra le statut de repli du {$a->what} pour {$a->who} seulement pour la session en cours de l\'utilisateur.';
$string['setting_collapsemycoursesnodeperformancehint'] = 'Attention : ceci ne fonctionnera que si le paramètre <a href="{$a->url}">Afficher les catégories de cours</a> est désactivé. Dans le cas contraire, ce paramètre sera ignoré.';
$string['setting_collapsenode'] = 'Replier {$a->what} « {$a->which} »';
$string['setting_collapsenode_desc'] = 'L\'activation de ce paramètre permettra aux utilisateurs de replier le {$a->what} « {$a->which} ».';
$string['setting_collapsenodeaccordionexplanation'] = 'Cela signifie qu\'un seul nœud parent pourra être développé à la fois. Si un second nœud parent est développé, l\'actuel nœud parent développé se repliera automatiquement.';
$string['setting_collapsenodedefault'] = 'Replier {$a->what} « {$a->which} » par défaut';
$string['setting_collapsenodedefault_desc'] = 'L\'activation de ce paramètre va replier le {$a->what} « {$a->which} » par défaut.';
$string['setting_collapsenodeicon'] = 'Afficher l\'icône du nœud parent dans {$a->what} « {$a->which} »';
$string['setting_collapsenodeicon_desc'] = 'Avec ce paramètre, vous contrôlez si l\'icône d\'un nœud parent sera affichée dans le {$a->what} « {$a->which} » et / ou si le nœud parent sera indenté.';
$string['setting_collapsenodeicon_justindent'] = 'Ne pas afficher l\'icône mais indenté le nœud parent repliable';
$string['setting_collapsenodeicon_none'] = 'Ne pas afficher l\'icône et ne pas indenter le nœud parent repliable';
$string['setting_collapsenodeicon_yes'] = 'Afficher l\'icône et indenter le nœud parent repliable';
$string['setting_collapsenodesdefaultexplanation'] = 'Sinon, ils seront développés par défaut. Ce paramètre contrôle uniquement la valeur par défaut de chaque utilisateur lorsque les nœuds sont affichés pour lui pour la première fois.';
$string['setting_collapsenodesession'] = 'Se souvenir de l\'état de repli de {$a->what} « {$a->which} » seulement pour la session en cours.';
$string['setting_collapsenodesession_desc'] = 'L\'activation de ce paramètre retiendra le statut de repli du {$a->what} « {$a->which} »  seulement pour la session en cours de l\'utilisateur.';
$string['setting_collapsenodesheading'] = 'Replier {$a->what} dans le tiroir de navigation de Boost.';
$string['setting_collapsenodessessionexplanation'] = 'Après la prochaine déconnexion et connexion d\'un utilisateur, l\'état de repli sera réinitialisé à la valeur définie par défaut ci-dessus. Si ce paramètre est désactivé, l\'état de repli de ces nœuds sera mémorisé au-delà de la fin de la session de l\'utilisateur.';
$string['setting_collapsenodestechnicalhint'] = 'Aspect technique : du code JavaScript et CSS est ajouté à la page pour afficher/masquer les nœuds de second niveau lorsque l\'utilisateur clique sur ce nœud. L\'état (déplié/replié) du nœud est enregistré dans les préférences de l\'utilisateur. Ainsi, les nœuds ne seront masqués dans le tiroir de navigation qu\'à l\'exécution tout en restant dans l\'arbre de navigation et donc accessibles par d\'autres composants de Moodle.';
$string['setting_customnodesusageadminsexample'] = 'le site Moodle.org|http://www.moodle.org|en,fr<br />
Lister tous les utilisateurs|/admin/user.php';
$string['setting_customnodesusageadminsintro'] = 'Chaque ligne se compose d\'un titre de lien, d\'une URL de lien et de la (des) langue(s) prise(s) en charge (facultatif) séparés par des caractères pipe. Chaque nœud personnalisé doit être écrit dans une nouvelle ligne.';
$string['setting_customnodesusageadminsparameternote'] = 'Les nœuds personnalisés pour les administrateurs sont traités par la même fonction que les nœuds personnalisés des utilisateurs. Ainsi, en plus des paramètres indiqués ci-dessus, les mêmes paramètres que les nœuds personnalisés pour les utilisateurs sont pris en charge ici. N\'hésitez pas à les personnaliser si vous en avez vraiment besoin pour un scénario particulier, même s\'ils ne sont pas redétaillés ici par souci de simplicité.';
$string['setting_customnodesusagechildnodes'] = 'Les nœuds personnalisés peuvent être imbriqués avec un niveau de hiérarchie, c\'est-à-dire qu\'un nœud personnalisé peut avoir des nœuds enfants. Pour créer un nœud enfant au lieu d\'un nœud parent, préfixez simplement le titre du nœud personnalisé avec un trait d\'union.';
$string['setting_customnodesusagechildnodesexample'] = 'Administration du site|/admin/index.php<br />
-Lister tous les utilisateurs|/admin/user.php<br />
-Gérer les cours|/course/management.php';
$string['setting_customnodesusagechildnodespleasenoterecursive'] = 'Si un nœud parent n\'est pas affiché car une restriction (langue, cohorte, rôle ...) s\'applique, ses nœuds enfants ne seront pas non plus affichés.';
$string['setting_customnodesusagechildnodespleasenoteurl'] = 'Pour des raisons techniques, un nœud parent a toujours besoin d\'une URL valide, même si le nœud sera affiché replié par la suite.';
$string['setting_customnodesusageexamples'] = 'Exemples :';
$string['setting_customnodesusageparameterbeforenodedd'] = 'Ce paramètre peut être utilisé pour spécifier la clé du nœud en dessous du quel le nœud personnalisé doit être placé. Vous trouvez la clé de nœud dans l\'attribut HTML \'data-key\' du nœud que vous souhaitez référencer ici. Dans le cas d\'un ensemble de nœuds composé d\'un parent et d\'un ou plusieurs enfants, ce paramètre doit être donné à chaque nœud de l\'ensemble. <br/> Veuillez noter que vous ne pouvez utiliser que les clés des nœuds du même contexte que le nœud personnalisé. Par exemple, vous pouvez utiliser «fichiers personnels» ou «calendrier» dans le contexte du nœud racine ou vous pouvez utiliser «participants» ou «notes» dans le contexte du nœud de cours. Si la clé du nœud avant est inconnue ou ne peut pas être traitée pour une raison inconnue, le nœud sera ajouté à la fin du contexte du nœud. <br /> Veuillez également noter que ce paramètre est considéré comme expérimental car il ne sera peut-être pas toujours possible d\'utiliser certains nœuds comme nœud précédent avec ce paramètre.';
$string['setting_customnodesusageparameterbeforenodedt'] = 'Clé du nœud précédent (facultatif) :';
$string['setting_customnodesusageparameterclassdd'] = 'Ce paramètre peut être utilisé pour spécifier une ou plusieurs classes CSS au nœud personnalisé.';
$string['setting_customnodesusageparameterclassdt'] = 'Classes CSS (optionnel) :';
$string['setting_customnodesusageparametercohortdd'] = 'Ce paramètre peut être utilisé pour afficher le nœud personnalisé uniquement aux membres de la cohorte spécifiée. Utilisez l\'ID de la cohorte, et non le nom de la cohorte, pour ce paramètre. Séparez plus d\'une cohorte prise en charge par des virgules. Si le nœud personnalisé doit être affiché pour tous les utilisateurs indépendamment de l\'appartenance à une cohorte, laissez ce champ vide.';
$string['setting_customnodesusageparametercohortdt'] = 'Cohorte(s) supporté(s) (facultatif) :';
$string['setting_customnodesusageparametericondd'] = 'Cette icône sera utilisée comme icône pour le nœud personnalisé, par exemple fa-flag. Utilisez un identifiant d\'icône Font Awesome (<a href="https://fontawesome.com/v4.7.0/icons/"> Consultez la liste d\'icônes sur fontawesome.com </a>) pour identifier l\'icône à utiliser. Font Awesome est inclus dans Boost, les icônes classiques de Moodle (pix) ne sont pas prises en charge ici. Si vous souhaitez simplement utiliser une icône de puce standard pour le nœud personnalisé, laissez ce champ vide. Les nœuds personnalisés qui n\'obtiennent pas automatiquement une icône de puce standard ne peuvent pas non plus obtenir d\'icône personnalisée pour le moment.';
$string['setting_customnodesusageparametericondt'] = 'Identifiant de l\'Icône (facultatif) :';
$string['setting_customnodesusageparameteriddd'] = 'Ce paramètre peut être utilisé pour définir l\'attribut data-key du nœud personnalisé sur la valeur donnée. L\'attribut data-key donné est nettoyé pour les caractères alphanumériques uniquement et préfixé par la section de nœud personnalisée, par exemple, l\'attribut data-key d\'un nœud dans la section des nœuds pour les administrateurs avec la valeur \'my_node\' se traduira par l\'attribut data-key suivant «localboostnavigationcustombottomadminsmynode». Ce paramètre vous permet de référencer un nœud personnalisé dans les sélecteurs CSS ou même dans le code JavaScript personnalisé. La définition du même attribut data-key sur plusieurs nœuds n\'est pas interdite ici, mais veuillez noter que les attributs data-key en double peuvent créer des problèmes à long terme. Si ce champ est vide, le nœud personnalisé obtiendra une data-key générée automatiquement qui n\'est pas vraiment adaptée au référencement.';
$string['setting_customnodesusageparameteriddt'] = 'Attribut data-key (facultatif) :';
$string['setting_customnodesusageparameterlanguagedd'] = 'Ce paramètre peut être utilisé pour afficher le nœud personnalisé uniquement dans la langue spécifiée. Séparez plusieurs langues prises en charge par des virgules. Si le nœud personnalisé doit être affiché dans toutes les langues, laissez ce champ vide.';
$string['setting_customnodesusageparameterlanguagedt'] = 'Langue(s) prise(s) en charge (facultatif) :';
$string['setting_customnodesusageparameterlinkdd'] = 'La cible du lien peut être définie par une URL Web complète (par exemple https://moodle.org) ou un chemin relatif dans votre instance Moodle (par exemple /login/logout.php).';
$string['setting_customnodesusageparameterlinkdt'] = 'Lien :';
$string['setting_customnodesusageparameterlogicaldd'] = 'Ce paramètre peut être utilisé pour modifier la combinaison logique des paramètres facultatifs pour la cohorte, le rôle et le rôle système. Si vous définissez ce paramètre sur AND ou laissez ce paramètre vide, les paramètres facultatifs pour la cohorte, le rôle et le rôle système seront combinés logiquement avec AND et le nœud personnalisé ne sera affiché que si TOUS les paramètres s\'appliquent. Si vous définissez ce paramètre sur OR, les paramètres facultatifs pour la cohorte, le rôle et le rôle système seront combinés logiquement avec OR et le nœud personnalisé sera affiché si AU MOINS UN des paramètres s\'applique. Cela vous donne la flexibilité de créer, par exemple, un nœud qui est affiché aux utilisateurs avec un rôle de cours donné ainsi qu\'aux utilisateurs avec un rôle système donné, comme indiqué dans notre exemple ci-dessus.';
$string['setting_customnodesusageparameterlogicaldt'] = 'Opérateur de combinaison logique (facultatif) :';
$string['setting_customnodesusageparameterroledd'] = 'Ce paramètre peut être utilisé pour afficher le nœud personnalisé uniquement aux membres avec le rôle spécifié dans chaque contexte. Utilisez le nom court du rôle pour ce paramètre. Séparez plusieurs rôles pris en charge par des virgules. Si le nœud personnalisé doit être affiché pour tous les utilisateurs quel que soit leur rôle, laissez ce champ vide.';
$string['setting_customnodesusageparameterroledt'] = 'Rôle(s) pris en charge (facultatif) :';
$string['setting_customnodesusageparameters'] = 'Informations complémentaires sur les paramètres :';
$string['setting_customnodesusageparametersystemroledd'] = 'Ce paramètre peut être utilisé pour afficher le nœud personnalisé uniquement aux utilisateurs avec un rôle système. Utilisez le nom court du rôle pour ce paramètre. Le nom court «admin» est pris en charge pour vérifier si l\'utilisateur est un administrateur du site. Séparez plusieurs rôles pris en charge par des virgules. Si le nœud personnalisé doit être affiché pour tous les utilisateurs quel que soit le rôle système, laissez ce champ vide.';
$string['setting_customnodesusageparametersystemroledt'] = 'Rôle(s) système pris en charge (facultatif) :';
$string['setting_customnodesusageparametertitledd'] = 'Ce texte sera affiché comme texte / étiquette cliquable du nœud personnalisé.';
$string['setting_customnodesusageparametertitledt'] = 'Titre :';
$string['setting_customnodesusagepleasenote'] = 'Veuillez noter :';
$string['setting_customnodesusagepleasenotecheck'] = 'Si le nœud personnalisé n\'apparaît pas dans le tiroir de navigation de Boost, veuillez vérifier si tous les paramètres obligatoires sont correctement définis, si le paramètre de langue facultatif correspond à la langue de votre utilisateur actuel et si vos autres paramètres facultatifs s\'appliquent à votre utilisateur.';
$string['setting_customnodesusagepleasenotecss'] = 'En raison de la façon dont le tiroir de navigation de Boost est construit dans le noyau Moodle, les nœuds personnalisés ne peuvent pas être construit entièrement librement. L\'ajout de classes CSS personnalisées, d\'ID HTML personnalisés ou d\'un attribut cible pour ouvrir le lien dans une nouvelle fenêtre est impossible.';
$string['setting_customnodesusagepleasenotelink'] = 'Le paramètre de lien peut contenir des espaces réservés, par exemple /course/edit.php?id={courseid} pour créer un nœud reliant la page des paramètres du cours actuel.';
$string['setting_customnodesusagepleasenotemultilang'] = 'Le paramètre titre peut contenir <a href="https://docs.moodle.org/en/Multi-language_content_filter">multilanguage strings</a> pour créer un nœud avec un label en fonction de la langue actuelle de l\'utilisateur.';
$string['setting_customnodesusagepleasenotephavailable'] = 'Les espaces réservés disponibles sont :';
$string['setting_customnodesusagepleasenotephcoursefullnamedd'] = 'Libellé long du cours';
$string['setting_customnodesusagepleasenotephcoursefullnamedt'] = '{coursefullname} :';
$string['setting_customnodesusagepleasenotephcourseiddd'] = 'ID (interne) du cours';
$string['setting_customnodesusagepleasenotephcourseiddt'] = '{courseid} :';
$string['setting_customnodesusagepleasenotephcourseshortnamedd'] = 'Libellé court du cours';
$string['setting_customnodesusagepleasenotephcourseshortnamedt'] = '{courseshortname} :';
$string['setting_customnodesusagepleasenotepheditinglinkdd'] = 'La valeur « on » ou « off » nécessaire pour basculer en mode édition';
$string['setting_customnodesusagepleasenotepheditinglinkdt'] = '{editingtoggle} :';
$string['setting_customnodesusagepleasenotepheditingtitledd'] = 'La valeur « Activer l\'édition » ou « Désactiver l\'édition » dans le pack de langue actuellement utilisé';
$string['setting_customnodesusagepleasenotepheditingtitledt'] = '{editingtoggle} :';
$string['setting_customnodesusagepleasenotephexplanation'] = 'Les espaces réservés sont encapsulés entre accolades et seront remplacés automatiquement lors de la création du nœud personnalisé.';
$string['setting_customnodesusagepleasenotephpagecontextiddd'] = 'L\'ID du contexte de la page actuelle';
$string['setting_customnodesusagepleasenotephpagecontextiddt'] = '{pagecontextid} :';
$string['setting_customnodesusagepleasenotephpagepathdd'] = 'Le chemin de l\'URL de la page en cours';
$string['setting_customnodesusagepleasenotephpagepathdt'] = '{pagepath} :';
$string['setting_customnodesusagepleasenotephsesskeydd'] = 'La clé de session à utiliser dans les URL sécurisées';
$string['setting_customnodesusagepleasenotephsesskeydt'] = '{sesskey} :';
$string['setting_customnodesusagepleasenotephuserfullnamedd'] = 'Le nom complet de l\'utilisateur connecté';
$string['setting_customnodesusagepleasenotephuserfullnamedt'] = '{userfullname} :';
$string['setting_customnodesusagepleasenotephuseriddd'] = 'L\'identifiant (interne) de l\'utilisateur connecté';
$string['setting_customnodesusagepleasenotephuseriddt'] = '{userid} :';
$string['setting_customnodesusagepleasenotephuserusernamedd'] = 'Le nom d\'utilisateur de l\'utilisateur connecté';
$string['setting_customnodesusagepleasenotephuserusernamedt'] = '{userusername} :';
$string['setting_customnodesusagepleasenotepipes'] = 'Le pipe séparant les paramètres facultatifs est toujours nécessaire s\'ils sont situés entre d\'autres options. Cela signifie que vous devez séparer les paramètres avec le caractère pipe même si ceux-ci sont vides. Voir également l\'exemple du nœud personnalisé "Faculty of mathematics" ci-dessus.';
$string['setting_customnodesusagepleasenotetitle'] = 'Le paramètre title peut contenir des espaces réservés, par exemple {coursefullname} pour créer un nœud étiqueté avec le nom complet du cours actuel.';
$string['setting_customnodesusageusersexample'] = '';
$string['setting_customnodesusageusersintro'] = 'Chaque ligne se compose d\'un titre de lien, d\'une URL de lien, de langue(s) prise(s) en charge (facultatif), de cohorte(s) prise(s) en charge (facultatif), de rôle(s) pris en charge (facultatif), de rôle(s) système pris en charge (facultatif), d\'un opérateur de combinaison logique (facultatif), d\'une icône (facultatif), d\'un attribut data-key (facultatif) et d\'une \'clé avant nœud\' (facultatif) - séparés par des caractères pipe. Chaque nœud personnalisé doit être écrit dans une nouvelle ligne.';
$string['setting_insertactivitiescoursenodeexplanation'] = 'Ce nœud contiendra des nœuds liés aux pages de présentation de l\'activité. Il apporte essentiellement la fonctionnalité existante du bloc « Activités » au tiroir de navigation de Boost.';
$string['setting_insertactivitiescoursenoderealicons'] = 'Utiliser des icônes d\'activités individuelles';
$string['setting_insertactivitiescoursenoderealicons_desc'] = 'L\'activation de ce paramètre utilisera les icônes d\'activité individuelles de Moodle pour les nœuds de cours d\'activité. Ces icônes sont colorées et plus détaillées que les icônes FontAwesome du reste du tiroir de navigation de Boost. La désactivation de ce paramètre utilisera une icône FontAwesome unique pour les nœuds du cours d\'activité';
$string['setting_insertcoursesectionscoursenodecorehint'] = 'Attention : ceci ne fonctionnera que si le paramètre <a href="{$a->url}">Toujours lier les sections de cours</a> est activé. Dans le cas contraire, ce paramètre sera ignoré.';
$string['setting_insertcoursesectionscoursenodeexplanation'] = 'Ce nœud sera placé au-dessus de la première section du cours actuel.';
$string['setting_insertcustomnodes'] = 'Insérer {$a->what} pour {$a->who}';
$string['setting_insertcustomnodes_desc'] = 'L\'activation de ce paramètre insérera un {$a->what} dans le tiroir de navigation de Boost qui sera ajouté au {$a->where} et qui sera visible à {$a->who}.';
$string['setting_insertnode'] = 'Insérer {$a->what} « {$a->which} »';
$string['setting_insertnode_desc'] = 'Enabling this setting will insert a {$a->what} « {$a->which} » to Boost\'s nav drawer.';
$string['setting_insertnodescollapsehint'] = 'Remarque: le nœud inséré a un lien d\'action qui mène à la page d\'accueil du cours car Boost ne prend pas en charge l\'ajout de nœuds sans lien d\'action. Le lien d\'action sera remplacé dès que vous activerez également le paramètre pour réduire le nœud en même temps.';
$string['setting_insertnodesheading'] = 'Insérer {$a->what} au tiroir de navigation de Boost';
$string['setting_insertresourcescoursenodeexplanation'] = 'Ce nœud sera lié à la page de présentation des ressources. <br /> <em> Ce paramètre est associé au paramètre « Insertion du nœud \'Activités\' ». Si vous activez les deux paramètres, vous obtiendrez un nœud « Activités » et un nœud « Ressources » comme demandé. Si vous activez uniquement le paramètre « Activités », le nœud « Activités » contiendra également un nœud menant à la page de présentation des ressources. </em>';
$string['setting_modifymycoursesrootnodefilterhint'] = 'Ajouter un nœud d\'indication du filtre de cours';
$string['setting_modifymycoursesrootnodefilterhint_desc'] = 'L\'activation de ce paramètre ajoutera un nœud à la fin de la liste « Mes cours » du tiroir de navigation de Boost, indiquant a l\'utilisateur pourquoi la liste « Mes cours » est rempli de cette façon (c.a.d quel filtre de cours à produit la liste « Mes cours » actuelle)';
$string['setting_modifymycoursesrootnodefilterlink'] = 'Ajouter un lien au nœud d\'indication du filtre de cours';
$string['setting_modifymycoursesrootnodefilterlink_desc'] = 'L\'activation de ce paramètre ajoutera un nœud à la fin de la liste « Mes cours » du tiroir de navigation de Boost, indiquant à l\'utilisateur où il doit changer le filtre (c.a.d que cela affiche un lien vers le Tableau de bord).<br/><em>Ce paramètre est associé au paramètres « Ajouter un nœud d\'indication du filtre de cours ». Si les deux paramètres sont activés, ils seront combinés en un seul nœud.</em>';
$string['setting_modifymycoursesrootnodeshowfiltered'] = 'Afficher les cours filtrés';
$string['setting_modifymycoursesrootnodeshowfiltered_desc'] = 'L\'activation de ce paramètre changera la liste « Mes cours » du tiroir de navigation de Boost pour n\'afficher que les cours qui sont actuellement filtrés dans le bloc « Vue d\'ensemble des cours » du Tableau de bord. Si ce paramètres est désactivé,  la liste « Mes cours » du tiroir de navigation de Boost montrera tous les cours classé « en cours » ce qui est le fonctionnement par défaut de Moodle.';
$string['setting_modifymycoursesrootnodeshowfilterednavcourselimit'] = 'Veuillez noter : l\'activation de ce paramètre modifiera la valeur du paramètre <a href=« {$a->url} »>navcourselimit</a> à 100 000 à chaque chargement de page. Le réglage de cette valeur à un niveau élevé est nécessaire pour éviter que la liste des cours ne soit raccourcie d\'une manière qui pourrait casser les mécanismes de filtrage.';
$string['setting_modifynodesheading'] = 'Modifier {$a->what} dans le tiroir de navigation de Boost';
$string['setting_movecontentbanknodeincoursecontext'] = 'Déplacer {$a->rootnode} « {$a->contentbank} » dans le contexte de cours';
$string['setting_movecontentbanknodeincoursecontext_desc'] = 'Activer ce paramètre déplacera le {$a->rootnode} « {$a->contentbank} » vers la section {$a->coursenode} dans le tiroir de navigation de Boost lors de la visualisation d\'une page Moodle qui se trouve à l\'intérieur d\'un cours.';
$string['setting_movecontentbanknodeincoursecontextbefore'] = 'Déplacer {$a->rootnode} « {$a->contentbank} » avant ce {$a->coursenode}';
$string['setting_movecontentbanknodeincoursecontextbefore_desc'] = 'Avec ce paramètre, vous pouvez définir devant quel {$a->coursenode} le {$a->rootnode} « {$a->contentbank} » sera déplacé. Veuillez noter qu\'il vous appartient de vous assurer que le {$a->coursenode} configuré est affiché au même groupe d\'utilisateurs qui voient le « {$a->contentbank} » {$a->rootnode}. Si le {$a->coursenode} configuré n\'est pas affiché pour un utilisateur particulier, le « {$a->contentbank} » {$a->rootnode} est ajouté à la fin de la section {$a->coursenode} et un avertissement de débogage est affiché / ajouté aux journaux.';
$string['setting_movenodesheading'] = 'Déplacer {$a->what} dans le tiroir de navigation de Boost';
$string['setting_movenodestechnicalhint'] = 'Informations techniques : Pour ce faire, il suffit de retirer le nœud à l\'emplacement d\'origine dans le tiroir de navigation de Boost et d\'ajouter à nouveau le même nœud à un autre emplacement. Par conséquent, il ne sera déplacé que dans le tiroir de navigation, mais restera fondamentalement non enchaîné et pourra toujours être accessible par d\'autres parties de Moodle.';
$string['setting_removebadgescoursenodeexplanation'] = 'Le nœud n\'est supprimé que s\'il n\'y a pas de badges dans le cours. Les enseignants peuvent toujours accéder à la page de gestion des badges dans le menu du cours (menu engrenage). <br /> <em> Ce paramètre n\'est traité que lorsque le sous-système des badges est activé dans Moodle. </em>';
$string['setting_removecompetenciescoursenodeexplanation'] = 'Le nœud n\'est supprimé que s\'il n\'y a pas de compétences dans le cours. Pour les enseignants, un autre nœud "Compétences" sera ajouté au menu du cours (menu engrenage). <br /> <em> Ce paramètre n\'est traité que lorsque le sous-système de compétences est activé dans Moodle. </em>';
$string['setting_removecoursenodestechnicalhint'] = 'Aspect technique: le nœud est supprimé de l\'arbre de navigation. Par conséquent il n\'est plus accessible par d\'autres composants de Moodle. Dans des configurations classiques de Moodle, cela ne devrait pas poser de problèmes.';
$string['setting_removegradescoursenodeexplanation'] = 'Le nœud est supprimé pour tous les utilisateurs, quelles que soient les capacités des utilisateurs et quel que soit le paramètre « Afficher le carnet de notes aux étudiants » dans les paramètres du cours.';
$string['setting_removemycoursesnodeperformancehint'] = 'À noter : Si vous activez ce paramètre ainsi que <a href="/admin/search.php?query=navshowmycoursecategories">navshowmycoursecategories</a>, supprimer « Mes cours » prend plus de temps et vous devriez peut-être désactiver le paramètre navshowmycoursecategories.';
$string['setting_removenode'] = 'Supprime {$a->what} « {$a->which} »';
$string['setting_removenode_desc'] = 'L\'activation de ce paramètre supprimera le {$a->what} « {$a->which} » du tiroir de navigation de Boost.';
$string['setting_removenodeincoursecontext'] = 'Supprimer {$a->what} « {$a->which} » dans le contexte du cours';
$string['setting_removenodeincoursecontext_desc'] = 'L\'activation de ce paramètre supprimera le {$a->what} « {$a->which} » du tiroir de navigation de Boost lors de l\'affichage d\'une page Moodle qui se trouve dans un cours.';
$string['setting_removenodeinnoncoursecontext'] = 'Supprimer {$a->what} « {$a->which} » en dehors du contexte de cours';
$string['setting_removenodeinnoncoursecontext_desc'] = 'L\'activation de ce paramètre supprimera le {$a->what} « {$a->which} » du tiroir de navigation de Boost lors de l\'affichage d\'une page Moodle qui se trouve en dehors d\'un cours.';
$string['setting_removenodesheading'] = 'Supprime {$a->what} du tiroir de navigation de Boost.';
$string['setting_removenotselectedhomerootnodeexplanation'] = 'Le nœud Accueil / Tableau de bord non sélectionné est défini par ce que l\'utilisateur a choisi de ne pas être sa page d\'accueil.';
$string['setting_removeparticipantscoursenodeexplanation'] = 'Le nœud est supprimé pour tous les utilisateurs, quelles que soient les capacités des utilisateurs.';
$string['setting_removerootnodestechnicalhint'] = 'Contexte technique : cela se fait en définissant l\'attribut showinflatnavigation du nœud sur false. Ainsi, le nœud ne sera masqué que dans le tiroir de navigation, mais il restera dans l\'arborescence de navigation et sera toujours accessible par d\'autres parties de Moodle.';
$string['setting_removeselectedhomerootnodeexplanation'] = 'Le nœud Accueil du site / Tableau de bord sélectionné est défini par ce que l\'utilisateur a choisi comme page d\'accueil.';
$string['settingspage_bottomnodes'] = 'Nœuds inférieurs';
$string['settingspage_coursenodes'] = 'Nœuds de cours';
$string['settingspage_custombottomnodes'] = 'nœuds inférieurs personnalisés';
$string['settingspage_customcoursenodes'] = 'nœuds de cours personnalisés';
$string['settingspage_customrootnodes'] = 'nœuds racines personnalisés';
$string['settingspage_mycoursesrootnode'] = 'Nœud racine « Mes cours »';
$string['settingspage_rootnodes'] = 'Nœuds globaux';
