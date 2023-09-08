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
 * Strings for component 'theme_boost_campus', language 'fr', version '4.1'.
 *
 * @package     theme_boost_campus
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addablockpositionsetting'] = 'Position du widget « Ajouter un bloc »';
$string['addablockpositionsetting_desc'] = 'Avec ce paramètre, vous pouvez gérer la position du widget « Ajouter un bloc ». Si vous sélectionnez « En bas de la barre de navigation », le widget pour ajouter de nouveaux blocs s\'affiche dans le panneau de navigation coulissant de gauche comme dans le thème Boost. Si vous sélectionnez « En bas de la zone de bloc par défaut », le widget pour ajouter de nouveaux blocs sera affiché dans la zone de bloc par défaut. Remarque: cela a pour effet secondaire que ce bloc sera également affiché dans la zone du bloc sur les pages de paramètres d\'activité lorsque le mode d\'édition est activé.';
$string['addablockwidgetheadingsetting'] = 'Position du widget « Ajouter un bloc »';
$string['additionallayoutsettings'] = 'Paramètres de mise en page supplémentaires';
$string['additionalresourcesheadingsetting'] = 'Ressources supplémentaires';
$string['additionalresourcessetting'] = 'Ajouter des ressources supplémentaires';
$string['additionalresourcessetting_desc'] = 'Avec ce paramètre, vous pouvez télécharger des ressources supplémentaires au thème. Vous pouvez référencer ces ressources en utilisant un lien. L\'URL ressemblera à ceci: « /pluginfile.php/1/theme_boost_campus/additionalresources/0/yourfilename.someextention » <br/>
L\'avantage du téléchargement de fichiers dans cette zone de fichiers est que ces fichiers peuvent être fournis sans vérification si l\'utilisateur est connecté.
C\'est aussi pourquoi vous ne devez ajouter que des fichiers non critiques pour lesquels tout le monde doit être autorisé à y accéder et qui n\'ont pas besoin d\'être protégés par une connexion valide. <br/>
Un exemple de cas d\'utilisation peut être trouvé dans le fichier README.md.';
$string['backgroundimage_desc_note'] = '<br/> Remarque: cela ne remplacera pas le paramètre « theme_boost_campus | loginbackgroundimage », ce qui signifie que les images téléchargées en arrière-plan de la page de connexion seront affichées de toute façon.';
$string['backtotop'] = 'Retour en haut';
$string['bcbttbuttonheadingsetting'] = 'Bouton « Retour en haut » de Boost Campus';
$string['bcbttbuttonsetting'] = 'Bouton « Retour en haut » de Boost Campus';
$string['bcbttbuttonsetting_desc'] = 'Depuis Moodle 3.9, un bouton « Aller en haut » a été intégré dans le thème Boost et généralement cela rendrait notre solution fournie obsolète.<br/>
Cependant, la solution de base a encore quelques petites envies et c\'est la raison pour laquelle nous aimerions fournir le bouton « Retour en haut » de Boost Campus en tant que paramètre facultatif. <br/>
Si vous activez ce paramètre, le bouton principal « Aller en haut » ne s\'affichera pas et le bouton « Retour en haut » de Boost Campus apparaîtra à la place. Les différences sont :<br/>
<ul>
<li>Défilement fluide : le bouton « Retour en haut » de Boost Campus utilise un défilement fluide tandis que le bouton principal « Aller en haut » saute directement vers le haut.</li>
<li>Implications sur l\'URL : le bouton « Retour en haut » de Boost Campus utilise uniquement Javascript et n\'a aucune implication sur l\'URL, tandis que le bouton principal « Aller en haut » ajoute un « # » à la fin de l\'URL en cliquant sur le bouton. Si vous copiez l\'URL, le « # » sera copié le long.</li>
<li>Style : le style des deux boutons diffère.</li>
<li>Accessibilité : le bouton « Retour en haut » de Boost Campus n\'est pas amélioré en termes d\'accessibilité, contrairement au bouton principal « Aller en haut ».</li>
</ul>
Veuillez noter :<br/>
Ce paramètre sera supprimé dès que la fonctionnalité principale sera améliorée afin qu\'aucune solution de thème individuel ne soit plus nécessaire.';
$string['blockcolumnwidthdashboardsetting'] = 'Figer la largeur des colonnes sur le tableau de bord';
$string['blockcolumnwidthdashboardsetting_desc'] = 'Avec ce paramètre, vous pouvez modifier la largeur (en pixels) de la colonne de bloc sur le tableau de bord.';
$string['blockcolumnwidthdefault'] = 'La largeur par défaut depuis Moodle 3.6 est de 360px, jusqu\'à Moodle 3.5 la largeur était de 250px.';
$string['blockcolumnwidthsetting'] = 'Figer la largeur des colonnes sur toutes les autres pages';
$string['blockcolumnwidthsetting_desc'] = 'Avec ce paramètre, vous pouvez modifier la largeur (en pixels) de la colonne de bloc sur toutes les autres pages à l\'exception du tableau de bord.';
$string['blockdesignheadingsetting'] = 'Blocs';
$string['blockiconsetting'] = 'Icône de bloc';
$string['blockiconsetting_desc'] = 'Avec ce paramètre, vous pouvez ajouter une icône Font Awesome par défaut devant le titre du bloc. Si cette case est cochée, nous proposons en outre des remplacements d\'icônes individuels pour de nombreux blocs de base Moodle et également pour certains blocs largement utilisés. Vous pouvez également changer les icônes facilement pour chaque bloc individuellement dans votre SCSS brut via la modification du contenu Font Awesome. Pour toutes les icônes disponibles, veuillez visiter http://fontawesome.io/icons/ et utiliser la valeur Unicode de l\'icône pour remplacer celle par défaut. <br/> Le code pour changer l\'icône ressemble à cet exemple de changement pour le bloc « People »: <br/>';
$string['blockiconsetting_desc_code'] = '.block_people .card-block .card-title::avant { content: \'\\f0c0\' ; }';
$string['boost_campus:viewhintcourseselfenrol'] = 'Pour pouvoir voir un indice pour une auto-inscription sans restriction dans un cours visible.';
$string['boost_campus:viewhintinhiddencourse'] = 'Pour pouvoir voir un indice dans un cours caché.';
$string['bootstrapdangercolor'] = 'Couleur de danger';
$string['bootstrapdarkcolor'] = 'Couleur sombre';
$string['bootstrapinfocolor'] = 'Couleur des informations';
$string['bootstraplightcolor'] = 'Couleur claire';
$string['bootstrapnone'] = 'Pas de couleur Bootstrap';
$string['bootstrapprimarycolor'] = 'Couleur primaire';
$string['bootstrapsecondarycolor'] = 'Couleur secondaire';
$string['bootstrapsuccesscolor'] = 'Couleur de réussite';
$string['bootstrapwarningcolor'] = 'Couleur d\'avertissement';
$string['brandcolorheadingsetting'] = 'Couleurs de marque';
$string['branddangercolorsetting'] = 'Couleur de l\'étiquette de danger';
$string['branddangercolorsetting_desc'] = 'Cette couleur est utilisée par exemple pour les validations de formulaire.';
$string['brandinfocolorsetting'] = 'Couleur de l\'étiquette d\'info';
$string['brandinfocolorsetting_desc'] = 'Cette couleur est utilisée par exemple pour les informations de disponibilité des activités du cours ou des ressources.';
$string['brandsuccesscolorsetting'] = 'Couleur de l\'étiquette de succès';
$string['brandsuccesscolorsetting_desc'] = 'Cette couleur est utilisée par exemple pour les validations de formulaire.';
$string['brandwarningcolorsetting'] = 'Couleur de l\'étiquette d\'avertissement';
$string['brandwarningcolorsetting_desc'] = 'Cette couleur est utilisée par exemple pour les textes d\'avertissement.';
$string['breakpointheadingsetting'] = 'Point d\'arrêt';
$string['breakpointsetting'] = 'Modifier le point d\'arrêt';
$string['breakpointsetting_desc'] = 'Dans le thème Boost, la colonne de bloc de droite se décomposera même sur les appareils d\'une largeur allant jusqu\'à 1200 pixels (la résolution d\'écran large de l\'iPad est de 1024 pixels, par exemple).
En effet, le point d\'arrêt est défini sur <a href="https://getbootstrap.com/docs/4.0/layout/overview/#responsive-breakpoints"> media-breakpoint-down (lg) </a>. <br/>
Si vous pensez qu\'il y a suffisamment d\'espace pour afficher le contenu plus la colonne de blocs côte à côte sur une largeur d\'écran de 992 pixels et plus, activez ce paramètre. Il changera le point d\'arrêt en media-breakpoint-down (md). Cela supprimera la colonne des blocs uniquement sur les écrans avec des largeurs inférieures à 992 pixels.';
$string['cachedef_imagearea'] = 'Cache pour les éléments imagearea';
$string['catchcmdarrowdownsetting'] = 'Raccourci Cmd + Flèche vers le bas';
$string['catchcmdarrowdownsetting_desc'] = 'Ce paramètre interceptera le raccourci « Cmd + Flèche vers le bas » (MAC),';
$string['catchctrlarrowdownsetting'] = 'Raccourci Ctrl + Flèche vers le bas';
$string['catchctrlarrowdownsetting_desc'] = 'Ce paramètre interceptera le raccourci « Ctrl + Flèche vers le bas » (Windows),';
$string['catchendkeysetting'] = 'Touche Fin';
$string['catchendkeysetting_desc'] = 'Ce paramètre interceptera la touche « Fin » (devrait fonctionner sur tous les principaux navigateurs et systèmes d\'exploitation),';
$string['catchkeyboardcommandsheadingsetting'] = 'Intercepter les commandes du clavier';
$string['catchkeyboardcommandsheadingsetting_desc'] = 'Les paramètres suivants sont destinés à répondre aux besoins des utilisateurs avancés, en particulier si votre instance Moodle a un grand pied de page. Les utilisateurs avancés sont susceptibles d\'utiliser des raccourcis clavier pour naviguer sur les sites. Ils peuvent les utiliser pour atteindre le bas de la page dans l\'intention d\'accéder rapidement au sujet le plus récent du cours (pour ajouter du contenu ou noter les dernières activités). Si le pied de page n\'est pas assez petit, ils devraient à nouveau défiler vers le haut. Avec ces paramètres, vous pouvez activer que les raccourcis suivants soient capturés et ne défileront qu\'en bas du contenu du cours principal.';
$string['catchkeys_desc_addition'] = 'empêche le défilement par défaut vers le bas de la page Web et modifie le comportement pour faire défiler uniquement vers le bas du contenu du cours principal.';
$string['choosereadme'] = 'Theme Boost Campus est un thème enfant à utiliser sur les campus universitaires.';
$string['close'] = 'Fermer';
$string['closingperpetualinfobanner'] = 'Voulez-vous vraiment ignorer ces informations ? Une fois cela fait, cela ne se reproduira plus !';
$string['configtitle'] = 'Paramètres de Boost Campus';
$string['confirmation'] = 'Confirmation';
$string['coursehintsheadingsetting'] = 'Conseils relatifs au cours.';
$string['courselayoutsettings'] = 'Paramètres de disposition du cours';
$string['coursesettingsheadingsetting'] = 'Paramètres du cours';
$string['darknavbarsetting'] = 'Barre de navigation foncée';
$string['darknavbarsetting_desc'] = 'En vérifiant ce paramètre, vous pouvez inverser la barre de navigation claire par défaut en une barre foncée avec des liens blancs.';
$string['dashboardontopsetting'] = 'Élément de menu du tableau de bord en haut';
$string['dashboardontopsetting_desc'] = 'En cochant ce paramètre, l\'élément de menu du Tableau de bord sera toujours situé en haut de la barre de navigation. Par défaut, c\'est déjà le cas sur toutes les pages Moodle à l\'exception des pages de cours. Là, le cours actuel et son contenu sont placés en haut. Cela pourrait perturber les attentes des utilisateurs pour la position du lien de page d\'accueil par défaut.';
$string['defaulthomepageontopsetting'] = 'Page d\'accueil par défaut en haut';
$string['defaulthomepageontopsetting_desc'] = 'En cochant ce paramètre, le lien par défaut de la page d\'accueil (tableau de bord ou page d\'accueil du site) sera toujours situé en haut de la barre de navigation. Par défaut, c\'est déjà le cas sur toutes les pages Moodle à l\'exception des pages de cours. Là, le cours actuel et son contenu sont placés en haut. Cela pourrait perturber les attentes des utilisateurs pour la position du lien de page d\'accueil par défaut.';
$string['designsettings'] = 'Paramètres de conception';
$string['faviconheadingsetting'] = 'Favicon';
$string['faviconsetting'] = 'Favicon';
$string['faviconsetting_desc'] = 'Vous pouvez télécharger une image (format .ico ou .png) que le navigateur affichera comme le favicon de votre site Web Moodle.';
$string['fontdesignheadingsetting'] = 'Polices';
$string['fontfilessetting'] = 'Fichiers de polices';
$string['fontfilessetting_desc'] = 'Avec cette boîte de dialogue, vous pouvez télécharger vos propres fichiers de polices. Le téléchargement est limité aux fichiers de polices de type .eot, .woff, .woff2, .ttf et .svg. <br/>
Important : pour pouvoir utiliser les polices téléchargées dans ce thème, vous devez ajouter le code associé à votre zone « SCSS brut » dans l\'onglet « Paramètres avancés ». Un exemple complet peut être trouvé dans le fichier README.md.';
$string['footerblocks0columnssetting'] = 'Aucun bloc dans le pied de page';
$string['footerblocks1columnssetting'] = 'Une colonne de blocs dans le pied de page';
$string['footerblocks2columnssetting'] = 'Deux colonnes de blocs dans le pied de page';
$string['footerblocks3columnssetting'] = 'Trois colonnes de blocs dans le pied de page';
$string['footerblocksheadingsetting'] = 'Blocs de pied de page';
$string['footerblockssetting'] = 'Blocs de pied de page';
$string['footerblockssetting_desc'] = 'Vous pouvez choisir si vous souhaitez activer la possibilité de placer des blocs dans le pied de page. Si elle est activée, vous pouvez choisir entre une, deux ou trois colonnes de blocs. Ces colonnes ne s\'affichent que sur de grands écrans. Sur les petits écrans, les colonnes seront automatiquement réduites à une colonne pour une meilleure lisibilité et mise en page.';
$string['footerhidehelplinksetting'] = 'Masquer le lien vers les documents Moodle';
$string['footerhidehomelinksetting'] = 'Masquer le lien vers la page Web de démarrage';
$string['footerhidelogininfosetting'] = 'Masquer les informations de connexion';
$string['footerhideusertourslinksetting'] = 'Masquer le lien pour réinitialiser la visite utilisateur';
$string['footerlayoutsettings'] = 'Paramètres de disposition du pied de page';
$string['footerlinks_desc'] = 'Si coché, le lien ne sera pas affiché dans le pied de page. S\'il n\'est pas coché (par défaut), il sera affiché.';
$string['footerlinksheadingsetting'] = 'Liens de pied de page par défaut';
$string['footerlinksheadingsetting_desc'] = 'Moodle fournit des liens par défaut dans le pied de page: un lien vers les documents Moodle, des informations de connexion et un lien vers la page Web de démarrage. <br/> Avec les trois paramètres suivants, vous pouvez décider si vous souhaitez masquer des liens spécifiques, car vous pensez que vos utilisateurs n\'en auront pas besoin dans votre instance.';
$string['footnoteheadingsetting'] = 'Note de pied de page';
$string['footnoteheadingsetting_desc'] = 'Le paramètre suivant permet d\'ajouter une zone supplémentaire pour afficher une note de pied de page.';
$string['footnotesetting'] = 'Note de pied de page';
$string['footnotesetting_desc'] = 'Tout ce que vous ajoutez à cette zone de texte sera affiché à la fin du pied de page sur chaque page qui fournit le pied de page standard du thème (pour les mises en page « colonnes2 » et « connexion »). Le contenu de cette zone peut être par exemple le droit d\'auteur, les conditions d\'utilisation et le nom de votre organisation. <br/> Si vous souhaitez supprimer à nouveau la note de bas de page, videz simplement la zone de texte.';
$string['helptextheadingsetting'] = 'Textes d\'aide';
$string['helptextmodalsetting'] = 'Afficher les textes d\'aide dans une zone de texte dédiée';
$string['helptextmodalsetting_desc'] = 'La solution par défaut pour afficher les textes d\'aide dans des bulles contextuelles entraîne différents problèmes. Par exemple, les bulles contextuelles ne peuvent pas défiler et peuvent dépasser sur la fenêtre d\'affichage.<br/>
Pour cette raison, avec ce paramètre, vous pouvez décider que les textes d\'aide doivent être affichés dans une zone de texte dédiée qui apparaît au milieu de la page avec suffisamment d\'espace pour contenir même les longs textes d\'aide.';
$string['hidefooterheadingsetting'] = 'Masquer le pied de page';
$string['hidefooteronloginpagesetting'] = 'Masquer le pied de page sur la page de connexion';
$string['hidefooteronloginpagesetting_desc'] = 'En activant ce paramètre, vous pouvez masquer le pied de page sur la page de connexion. Veuillez noter que cela ne masquera que la section de pied de page, pas la section de note de bas de page (si utilisée).';
$string['ibcsssetting_nobootstrap'] = 'Si vous choisissez l\'option \'{$a->bootstrapnone}\', la bannière d\'informations sera affichée sans aucune couleur Bootstrap particulière.';
$string['imageareaheadingsetting'] = 'Zone d\'image';
$string['imageareaheadingsetting_desc'] = 'Les paramètres suivants permettent d\'ajouter une zone supplémentaire pour afficher les images comme des logos. Cette zone sera ajoutée sous le pied de page standard et au-dessus de la zone de note de pied de page facultative. Si des images sont téléchargées, cette zone sera affichée sur tous les sites utilisant la disposition colonnes2.';
$string['imageareaitemsattributessetting'] = 'Attributs supplémentaires de l\'élément de zone d\'image';
$string['imageareaitemsattributessetting_desc'] = 'Avec ce paramètre facultatif, vous pouvez ajouter des attributs supplémentaires à vos images téléchargées :
<ul>
<li> un lien </li>
<li> un attribut alt qui décrit l\'image </li>
</ul>
Chaque ligne se compose de l\'identifiant du fichier (le nom du fichier), de l\'URL du lien et du texte alternatif, séparés par des caractères de canal. Chaque déclaration de lien doit être écrite sur une nouvelle ligne. <br/>
Par exemple: <br/>
```
moodle.jpg | https: //moodle.org | Logo Moodle
```<br/> <br/>
Vous pouvez déclarer les attributs supplémentaires pour un nombre quelconque de vos images téléchargées. Les attributs seront ajoutés uniquement aux images qui correspondent à leur nom de fichier avec l\'identifiant déclaré dans ce paramètre.';
$string['imageareaitemsmaxheightsetting'] = 'Hauteur maximale des éléments de la zone d\'image';
$string['imageareaitemsmaxheightsetting_desc'] = 'Avec ce paramètre, vous pouvez modifier la hauteur en pixels de vos images téléchargées. Toutes les images auront la même hauteur maximale et leur largeur sera redimensionnée proportionnellement. La valeur par défaut est définie sur 100 pixels.';
$string['imageareaitemssetting'] = 'Éléments de la zone d\'image';
$string['imageareaitemssetting_desc'] = 'Avec ce widget, vous pouvez télécharger vos images qui seront affichées dans la zone d\'image supplémentaire. Les images seront triées et affichées alphabétiquement par nom de fichier. Pour supprimer cette zone, supprimez simplement toutes les images téléchargées.';
$string['incoursesettingsswitchtorolepositionsetting'] = 'Emplacement(s) « Prendre le rôle de ... »';
$string['incoursesettingsswitchtorolepositionsetting_desc'] = 'Avec ce paramètre, vous pouvez choisir l\'endroit où les informations vers quel rôle un utilisateur est passé sont affichées. S\'il est défini sur « Juste dans le menu utilisateur » (valeur par défaut), les informations sur le rôle seront affichées juste sous le nom de l\'utilisateur dans le menu utilisateur (comme dans le thème Boost). Si elle est définie sur « Juste dans les paramètres du cours », ces informations - ainsi qu\'un lien pour revenir en arrière - seront affichées sous le cours, car cette fonctionnalité est liée au cours. S\'il est réglé sur « Les deux dans le menu utilisateur et dans les paramètres du cours », il sera affiché aux deux endroits.';
$string['incoursesettingsswitchtorolesettingboth'] = 'Aux deux endroits: dans le menu utilisateur et dans les paramètres du cours';
$string['incoursesettingsswitchtorolesettingjustcourse'] = 'Juste dans les paramètres du cours';
$string['incoursesettingsswitchtorolesettingjustmenu'] = 'Juste dans le menu utilisateur';
$string['infobannersettings'] = 'Paramètres de la bannière d\'informations';
$string['login_page'] = 'Page de connexion';
$string['loginbackgroundimagesetting'] = 'Images d\'arrière-plan de la page de connexion';
$string['loginbackgroundimagesetting_desc'] = 'Les images téléchargées dans ce paramètre seront affichées au hasard sur la page de connexion en tant qu\'images d\'arrière-plan.';
$string['loginbackgroundimagetextsetting'] = 'Afficher le texte des images d\'arrière-plan de connexion';
$string['loginbackgroundimagetextsetting_desc'] = 'Avec ce paramètre facultatif, vous pouvez ajouter du texte, par exemple un avis de droit d\'auteur sur vos images d\'arrière-plan téléchargées. <br/>
Chaque ligne se compose de l\'identifiant du fichier (le nom du fichier) et du texte à afficher, séparés par un caractère de canal. Chaque déclaration doit être écrite sur une nouvelle ligne. <br/>
Par exemple: <br/>
background-image-1.jpg | Copyright: CC0 <br/>
Vous pouvez déclarer des textes pour un nombre quelconque de vos images d\'arrière-plan téléchargées. Les textes seront ajoutés uniquement aux images qui correspondent à leur nom de fichier avec l\'identifiant déclaré dans ce paramètre.';
$string['loginform'] = 'Formulaire de connexion';
$string['loginform_desc'] = 'Avec ce paramètre, vous pouvez optimiser le formulaire de connexion pour l\'adapter à une plus grande variété d\'images d\'arrière-plan (si cochée). Cela signifie que le formulaire de connexion sera déplacé vers la gauche de la page de connexion, sa largeur diminuera et son arrière-plan laissera transparaître l\'image d\'arrière-plan. Le formulaire de connexion sera placé à gauche car de nombreuses images ont leur contenu principal plutôt au centre et nous gardons donc ce contenu visible. Remarque : Vous pouvez également activer ce paramètre si aucune image d\'arrière-plan n\'est téléchargée, bien sûr.';
$string['loginpagedesignheadingsetting'] = 'Page de connexion';
$string['navbardesignheadingsetting'] = 'Barre de navigation';
$string['navdrawerfullwidthsetting'] = 'Barre de navigation pleine largeur sur petits écrans';
$string['navdrawerfullwidthsettings_desc'] = 'En cochant ce paramètre, vous pouvez agrandir le menu de la barre de navigation déroulé sur toute la largeur de la page sur de petits écrans. Cela peut être souhaité car sur les petits écrans, très peu de la zone de contenu principal en arrière-plan est visible. Et un menu pleine largeur pourrait répondre aux attentes des utilisateurs quant à l\'affichage des menus sur de petits écrans.';
$string['navdrawerheadingsetting'] = 'Menu de la barre de navigation';
$string['perpetualinfobannerheadingsetting'] = 'Bannière d\'information permanente';
$string['perpetualinfobannerheadingsetting_desc'] = 'Les paramètres suivants permettent d\'afficher certaines informations importantes dans une bannière permanente bien visible.';
$string['perpetualinfobannerresetvisiblitysetting'] = 'Réinitialiser la visibilité pour la bannière d\'informations permanente';
$string['perpetualinfobannerresetvisiblitysetting_desc'] = 'En cochant cette case, la visibilité des bannières d\'informations permanentes rejetées individuellement sera à nouveau visible. Vous pouvez utiliser ce paramètre si vous avez apporté des modifications importantes au contenu et souhaitez afficher à nouveau les informations à tous les utilisateurs.<br/><br/>
Veuillez noter : <br/>
Après avoir enregistré cette option, les opérations de la base de données pour réinitialiser la visibilité seront déclenchées et cette case à cocher sera à nouveau décochée. La prochaine activation et l\'enregistrement de cette fonctionnalité déclencheront les opérations de base de données pour réinitialiser à nouveau la visibilité.';
$string['perpibconfirmsetting'] = 'Boîte de dialogue de confirmation';
$string['perpibconfirmsetting_desc'] = 'Lorsque vous activez ce paramètre, vous pouvez afficher une boîte de dialogue de confirmation à un utilisateur lorsqu\'il ferme la bannière d\'informations.
<br/>Le texte est enregistré dans la chaîne avec le nom « closingperpetualinfobanner »:<br/><br/>
Voulez-vous vraiment ignorer ces informations ? Une fois cela fait, cela ne se reproduira plus !<br/><br/>
Vous pouvez remplacer cela dans la personnalisation de votre langue si vous avez besoin d\'un autre texte dans cette boîte de dialogue.';
$string['perpibcontent'] = 'Contenu de la bannière d\'information permanente';
$string['perpibcontent_desc'] = 'Entrez ici vos informations qui doivent apparaître dans la bannière.';
$string['perpibcsssetting'] = 'Classe css Bootstrap pour la bannière d\'informations permanente';
$string['perpibcsssetting_desc'] = 'Avec ce paramètre, vous pouvez sélectionner le style Bootstrap avec lequel la bannière d\'information permanente doit être affichée.';
$string['perpibdismisssetting'] = 'Bannière d\'information permanente desactivable';
$string['perpibdismisssetting_desc'] = 'Avec cette case à cocher, vous pouvez rendre la bannière invisible de façon permanente. Si l\'utilisateur clique sur le bouton X, une boîte de dialogue de confirmation apparaîtra et ce n\'est qu\'après que l\'utilisateur aura confirmé cette boîte de dialogue que la bannière sera définitivement masquée pour cet utilisateur.
<br/><br/>Remarque : <br/> Ce paramètre n\'a aucun effet sur les bannières affichées sur la page de connexion. Étant donné que les bannières de la page de connexion ne peuvent pas être supprimées en permanence, nous n\'offrons pas du tout la possibilité de cliquer sur la bannière de la page de connexion.';
$string['perpibenablesetting'] = 'Activer la bannière d\'informations permanente';
$string['perpibenablesetting_desc'] = 'Avec cette case à cocher, vous pouvez décider si la bannière d\'information permanente doit être affichée ou masquée sur les pages sélectionnées.';
$string['perpibshowonpagessetting'] = 'Mises en page sur lesquelles afficher la bannière d\'informations';
$string['perpibshowonpagessetting_desc'] = 'Avec ce paramètre, vous pouvez sélectionner les pages sur lesquelles la bannière d\'information permanente doit être affichée.';
$string['pluginname'] = 'Boost Campus';
$string['presetheadingsetting'] = 'Préréglages de thème';
$string['privacy:metadata:preference:infobanner_dismissed'] = 'La préférence de l\'utilisateur pour le statut si la bannière d\'informations permanente a été supprimée.';
$string['privacy:metadata:request:infobanner_dismissed_no'] = 'La bannière d\'informations permanente n\'a pas été supprimée.';
$string['privacy:metadata:request:infobanner_dismissed_yes'] = 'La bannière d\'informations permanente a été supprimée.';
$string['region-footer-left'] = 'Pied de page (gauche)';
$string['region-footer-middle'] = 'Pied de page (milieu)';
$string['region-footer-right'] = 'Pied de page (droite)';
$string['region-side-pre'] = 'Droite';
$string['resetperpetualinfobannersuccess'] = 'Victoire ! Toutes les instances de bannière d\'informations permanentes sont à nouveau visibles.
<br/>Le paramètre « Réinitialiser la visibilité pour la bannière d\'informations permanente » a été réinitialisé.';
$string['resetperpetualinfobannervisibilityerror'] = 'Oups... Une erreur s\'est produite lors de la mise à jour des tables de la base de données. La préférence utilisateur « theme_boost_campus_infobanner_dismissed » aurait dû être réinitialisée dans la table « user_preferences ».
<br/>Exception levée : {$a->message}.
<br/>Traçage de la pile :
<br/>{$a->stacktrace}.
<br/>Le paramètre « Réinitialiser la visibilité pour la bannière d\'information permanente » a néanmoins été réinitialisé.';
$string['section0titlesetting'] = 'Section 0 : Titre';
$string['section0titlesetting_desc'] = 'Ce paramètre peut modifier le comportement de Moodle pour afficher le titre de la première section du cours. Moodle ne l\'affiche pas tant que le titre par défaut de cette section est défini. Dès qu\'un utilisateur change de titre, il apparaît. Avec ce paramètre (l\'option est cochée), vous pouvez obtenir un comportement cohérent en affichant toujours le titre de la section 0.';
$string['settingsaddablockpositionbottomblockregion'] = 'Au bas de la zone de bloc par défaut';
$string['settingsaddablockpositionbottomnavdrawer'] = 'Au bas de la barre de navigation';
$string['showhintcourseguestaccessgeneral'] = 'Vous visualisez actuellement ce cours comme <strong>{$a->role}</strong>.';
$string['showhintcourseguestaccesslink'] = 'Pour avoir un accès complet au cours, vous pouvez <a href="{$a->url} "> vous inscrire à ce cours </a>.';
$string['showhintcourseguestaccesssetting_desc'] = 'Avec ce paramètre, un avertissement apparaîtra dans l\'en-tête du cours lorsqu\'un utilisateur y accède avec la fonction d\'accès invité. Si le cours propose une auto-inscription active, un lien vers cette page est également présenté à l\'utilisateur.';
$string['showhintcoursehiddengeneral'] = 'Ce cours est actuellement <strong>caché</strong>. Seuls les enseignants inscrits peuvent accéder à ce cours lorsqu\'il est caché.';
$string['showhintcoursehiddensetting'] = 'Afficher un avertissement dans les cours cachés';
$string['showhintcoursehiddensetting_desc'] = 'Avec ce paramètre, un avertissement apparaîtra dans l\'en-tête du cours tant que la visibilité du cours est masquée. Cela permet d\'identifier l\'état de visibilité d\'un cours en un coup d\'œil sans avoir à regarder les paramètres du cours.';
$string['showhintcoursehiddensettingslink'] = 'Vous pouvez modifier la visibilité dans les <a href="{$a->url}">paramètres du cours</a>.';
$string['showhintcourseselfenrolfrom'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet l\'auto-inscription sans restriction à partir de {$a->from}.';
$string['showhintcourseselfenrolfromuntil'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet une auto-inscription sans restriction de {$a->from} jusqu\'à {$a->until}.';
$string['showhintcourseselfenrolinstancecallforaction'] = 'Si vous ne voulez pas qu\'un utilisateur de Moodle puisse s\'inscrire librement à ce cours, veuillez restreindre les paramètres d\'auto-inscription.';
$string['showhintcourseselfenrolsetting'] = 'Afficher l\'astuce pour l\'auto-inscription sans clé d\'inscription';
$string['showhintcourseselfenrolsetting_desc'] = 'Avec ce paramètre, un indice apparaîtra dans l\'en-tête du cours si le cours est visible et qu\'une inscription sans clé d\'inscription est actuellement possible.';
$string['showhintcourseselfenrolsince'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet actuellement l\'auto-inscription sans restriction.';
$string['showhintcourseselfenrolsinceuntil'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet l\'auto-inscription sans restriction jusqu\'au {$a->until}.';
$string['showhintcourseselfenrolstartcurrently'] = 'Ce cours est actuellement visible et l\'<strong>auto-inscription sans clé d\'inscription</strong> est actuellement possible.';
$string['showhintcourseselfenrolstartfuture'] = 'Ce cours est actuellement visible et l\'<strong>auto-inscription sans clé d\'inscription</strong> devrait devenir possible.';
$string['showhintcourseselfenrolunlimited'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet une auto-inscription illimitée à l\'infini.';
$string['showhintcourseselfenroluntil'] = 'L\'instance d\'inscription <strong>{$a->name}</strong> permet l\'auto-inscription sans restriction jusqu\'au {$a->until}.';
$string['showhintcoursguestaccesssetting'] = 'Afficher un avertissement pour l\'accès des invités';
$string['showsettingsincoursesetting'] = 'Dans le menu des paramètres du cours';
$string['showsettingsincoursesetting_desc'] = 'Avec ce paramètre, vous pouvez modifier l\'affichage des menus contextuels. Dans Boost, il y a un menu contextuel contextuel juste à côté de l\'icône de rouage. En activant ce paramètre, les paramètres apparaissent directement sous l\'en-tête du cours. Les paramètres sont organisés dans des onglets, il est donc plus facile pour l\'utilisateur d\'accéder au paramètre souhaité au lieu de parcourir une longue liste d\'éléments de menu. Avec ce paramètre, nous masquons également l\'icône des paramètres sur la page des participants car les entrées sur cette page sont dupliquées avec le menu du cours en cours et ne sont donc pas nécessaires. <br/>
Veuillez noter que cette modification n\'affecte pas les utilisateurs qui ont désactivé javascript dans leur navigateur - ils obtiendront toujours le comportement du noyau Moodle avec un menu contextuel de cours.';
$string['showswitchedroleincoursesetting'] = 'Position des informations de rôle de commutateur';
$string['showswitchedroleincoursesetting_desc'] = 'Avec ce paramètre, vous pouvez choisir l\'endroit où les informations de changement de rôle d\'un utilisateur sont affichées. Si elle n\'est pas cochée (valeur par défaut), les informations sur le rôle seront affichées juste sous le nom de l\'utilisateur dans le menu utilisateur (comme dans le thème Boost). Si cette case est cochée, ces informations - ainsi qu\'un lien pour revenir en arrière - seront affichées sous le cours, car cette fonctionnalité est liée au cours.';
$string['sitehomeontopsetting'] = 'Élément de menu d\'accueil du site en haut';
$string['sitehomeontopsetting_desc'] = 'En cochant ce paramètre, l\'élément de menu d\'accueil du site sera toujours situé en haut de la barre de navigation. Par défaut, c\'est déjà le cas sur toutes les pages Moodle à l\'exception des pages de cours. Là, le cours actuel et son contenu sont placés en haut. Cela pourrait perturber les attentes des utilisateurs pour le placement du lien de page d\'accueil par défaut.';
$string['switchedroleto'] = 'Vous visualisez actuellement ce cours avec le rôle :';
$string['switchroleto'] = 'Prendre le rôle';
$string['timedibcontent'] = 'Contenu de la bannière d\'information à durée contrôlée';
$string['timedibcontent_desc'] = 'Entrez ici vos informations qui devraient être affichées dans la bannière de contrôle du temps.';
$string['timedibcsssetting'] = 'Classe css Bootstrap pour la bannière d\'informations contrôlée par le temps';
$string['timedibcsssetting_desc'] = 'Avec ce paramètre, vous pouvez sélectionner le style Bootstrap avec lequel la bannière d\'informations contrôlée par le temps doit être affichée.';
$string['timedibenablesetting'] = 'Activer la bannière d\'informations contrôlée par le temps';
$string['timedibenablesetting_desc'] = 'Avec cette case à cocher, vous pouvez décider si la bannière d\'information contrôlée par le temps doit être affichée ou masquée sur les pages sélectionnées.';
$string['timedibendsetting'] = 'Heure de fin de la bannière d\'information contrôlée par le temps';
$string['timedibendsetting_desc'] = 'Avec ce paramètre, vous pouvez définir quand la bannière d\'informations contrôlées par le temps doit être masquée sur les pages sélectionnées.
<br/>Veuillez saisir une date valide au format suivant : AAAA-MM-JJ HH:MM:SS. Par exemple : « 2020-01-07 08:00:00 ». Le fuseau horaire sera le fuseau horaire que vous avez défini dans le paramètre « Fuseau horaire par défaut ».
<br/>Si vous laissez ce paramètre vide mais entrez une date dans le pour le début, la bannière ne se cachera pas une fois l\'heure de début atteinte.';
$string['timedibshowonpagessetting'] = 'Mises en page sur lesquelles afficher la bannière d\'informations';
$string['timedibshowonpagessetting_desc'] = 'Avec ce paramètre, vous pouvez sélectionner les pages sur lesquelles la bannière d\'informations contrôlée par le temps doit être affichée.
<br/> Si les deux bannières d\'informations sont actives sur une mise en page sélectionnée, la bannière d\'informations contrôlée par le temps apparaîtra toujours au-dessus de la bannière d\'informations permanente !';
$string['timedibstartsetting'] = 'Heure de début de la bannière d\'informations contrôlée par le temps';
$string['timedibstartsetting_desc'] = 'Avec ce paramètre, vous pouvez définir à quel moment la bannière d\'information contrôlée par le temps doit être affichée sur les pages sélectionnées.
<br/>Veuillez entrer une valeur valide dans ce format : AAAA-MM-JJ HH:MM:SS. Par exemple : « 2020-01-01 08:00:00 ». Le fuseau horaire sera le fuseau horaire que vous avez défini dans le paramètre « Fuseau horaire par défaut ».
<br/>Si vous laissez ce paramètre vide mais que vous avez entré une date dans le pour la fin, c\'est la même chose que si vous aviez entré une date loin dans le passé.';
$string['timedinfobannerheadingsetting'] = 'Bannière d\'information contrôlée par le temps';
$string['timedinfobannerheadingsetting_desc'] = 'Les paramètres suivants permettent d\'afficher certaines informations importantes dans une bannière bien en vue, contrôlée par le temps.';
$string['userdefinedontopsetting'] = 'Page d\'accueil définie par l\'utilisateur en haut';
$string['userdefinedontopsetting_desc'] = 'En cochant ce paramètre, l\'élément de menu d\'accueil du tableau de bord ou du site (selon les préférences de l\'utilisateur) sera toujours situé en haut de la barre de navigation. Par défaut, c\'est déjà le cas sur toutes les pages Moodle à l\'exception des pages de cours. Là, le cours actuel et son contenu sont placés en haut. Cela pourrait perturber les attentes des utilisateurs pour le placement du lien de page d\'accueil par défaut.';
$string['yes_close'] = 'Oui, fermer !';
