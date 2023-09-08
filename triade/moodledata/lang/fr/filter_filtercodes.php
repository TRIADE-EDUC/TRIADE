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
 * Strings for component 'filter_filtercodes', language 'fr', version '4.1'.
 *
 * @package     filter_filtercodes
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['brief'] = 'Résumé';
$string['categorycardshowpic'] = 'Afficher l\'arrière-plan de {categorycards}';
$string['categorycardshowpic_desc'] = 'Si activé, affichera une couleur/un motif d\'arrière-plan pour la balise {categorycards} similaire aux images du cours lorsqu\'aucune image n\'a été spécifiée.';
$string['chartprogressbarlabel'] = '{$a->label} : {$a->value}';
$string['contentmissing'] = '<h1>Le contenu est manquant.</h1><p>Veuillez en informer l\'administrateur.</p>';
$string['coursecardsbyenrol'] = 'Maximum de cartes pour {coursecardsbyenrol} .';
$string['coursecardsbyenrol_desc'] = 'Nombre maximum de cartes de cours à afficher pour la balise {coursecardsbyenrol}. Mettre à zéro pour illimité (non recommandé).';
$string['coursecontactlinktype'] = 'Type de lien de contact';
$string['coursecontactlinktype_desc'] = 'Choisissez le type de lien pour le lien du contact dans les balises {coursecontacts}.';
$string['coursecontactshowdesc'] = 'Afficher la description du profil du contact.';
$string['coursecontactshowdesc_desc'] = 'Si activé, affichera la description du profil du contact dans les balises {coursecontacts}.';
$string['coursecontactshowpic'] = 'Afficher la photo du contact';
$string['coursecontactshowpic_desc'] = 'Si activé, affichera la photo de profil du contact dans les balises {coursecontacts}.';
$string['disabled_customnav_description'] = '<strong>Remarque concernant la prise en charge du menu personnalisé</strong> - Pour activer les balises FilterCodes dans le menu personnalisé de votre site Moodle, vous devrez possiblement personnaliser votre thème ou votre noyau Moodle. <a href="https://github.com/michael-milette/moodle-filter_filtercodes#can-i-use-filtercodes-in-moodles-custom-menus"> Informations sur la façon d\'ajouter des balises FilterCodes dans les menus personnalisés</a>.';
$string['enable_customnav'] = 'Support pour navigation personnalisée';
$string['enable_customnav_description'] = '<strong>Expérimental</strong> : activer le support pour les balises FilterCode dans le menu de navigation personnalisé de Moodle.
Remarque : cette option est compatible avec les thèmes basés sur Clean et Boost dans Moodle 3.2 à 3.4 seulement. Ne filtre pas les balises sur la page Réglages thème de Moodle.';
$string['enable_scrape'] = 'Support pour les étiquettes de raclage';
$string['enable_scrape_description'] = 'Prise en charge des étiquettes de raclage';
$string['enable_sesskey'] = 'Prise en charge de la balise sesskey';
$string['enable_sesskey_description'] = 'Active globalement la balise sesskey. Cette fonctionnalité est désactivée dans les forums, même lorsqu\'elle est activée globalement.';
$string['escapebraces'] = 'Balises d\'échappement';
$string['escapebraces_desc'] = 'Lorsque cette option est cochée, vous pourrez afficher les balises FilterCodes sans qu\'elles soient interprétées en les enveloppant entre des [crochets]. Cela peut vous être très utile lors de la création de la documentation FilterCodes pour les enseignants et les créateurs de cours sur votre site Moodle.<br><br>Exemple : [{fullname}] n\'affichera pas le nom complet de l\'utilisateur mais affichera la balise {fullname}.';
$string['filtername'] = 'Filter Codes';
$string['formcheckin'] = '<form action="{wwwroot}/local/contact/index.php" method="post" class="cf check-in">
    <fieldset>
        <input type="hidden" id="subject" name="subject" value="Présent !">
        <input type="hidden" id="sesskey" name="sesskey" value="">
        <script>document.getElementById("sesskey").value = M.cfg.sesskey;</script>
         {recaptcha}
   </fieldset>
    <div>
        <input type="submit" name="submit" id="submit" value="Je suis ici&nbsp;!">
    </div>
</form>';
$string['formcontactus'] = '<form action="{wwwroot}/local/contact/index.php" method="post" class="cf contact-us">
    <fieldset>
        <div class="form-group">
            <label for="nom" id="namelabel" class="d-block">Votre nom <strong class="required">(obligatoire)</strong></label>
            <input id="nom" name="nom" type="text" size="57" maxlength="45" pattern="[A-zÀ-ž]([A-zÀ-ž\\s]){2,}"
                    title="Minimum de 3 lettres/espaces." required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{fullname}">
        </div>
        <div class="form-group">
            <label for="courriel" id="emaillabel" class="d-block">Adresse courriel <strong class="required">(obligatoire)</strong></label>
            <input id="courriel" name="courriel" type="email" size="57" maxlength="60"
                    required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{email}">
        </div>
        <div class="form-group">
            <label for="objet" id="subjectlabel" class="d-block">Objet <strong class="required">(obligatoire)</strong></label>
            <input id="objet" name="objet" type="text" size="57" maxlength="80" minlength="5"
                    title="Minimum de 5 caractères." required="required">
        </div>
        <div class="form-group">
            <label for="message" id="messagelabel" class="d-block">Message <strong class="required">(obligatoire)</strong></label>
            <textarea id="message" name="message" rows="5" cols="58" minlength="5"
                    title="Minimum de 5 caractères." required="required"></textarea>
        </div>
        <input type="hidden" id="sesskey" name="sesskey" value="">
        <script>document.getElementById("sesskey").value = M.cfg.sesskey;</script>
        {recaptcha}
    </fieldset>
    <div>
        <input type="submit" name="submit" id="submit" value="Envoyer">
    </div>
</form>';
$string['formcourserequest'] = '<form action="{wwwroot}/local/contact/index.php" method="post" class="cf new-course-request">
    <fieldset>
        <div class="form-group">
            <label for="nom" id="namelabel" class="d-block">Votre nom <strong class="required">(obligatoire)</strong></label>
            <input id="nom" name="nom" type="text" size="57" maxlength="45" pattern="[A-zÀ-ž]([A-zÀ-ž\\s]){2,}"
                    title="Minimum de 3 lettres/espaces." required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{fullname}">
        </div>
        <div class="form-group">
            <label for="courriel" id="emaillabel" class="d-block">Adresse courriel <strong class="required">(obligatoire)</strong></label>
            <input id="courriel" name="courriel" type="email" size="57" maxlength="60" required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{email}">
        </div>
        <div class="form-group">
            <label for="new_course_name" id="new_course_namelabel" class="d-block">Nom proposé du nouveau cours <strong class="required">(obligatoire)</strong></label>
            <input id="new_course_name" name="new_course_name" type="text" size="57" maxlength="80" minlength="5"
                    title="Minimum 5 characters." required="required">
        </div>
        <div class="form-group">
            <label for="description" id="messagelabel" class="d-block">Description du cours <strong class="required">(obligatoire)</strong></label>
            <textarea id="description" name="description" rows="5" cols="58" minlength="5"
                    title="Minimum de 5 caractères." required="required"></textarea>
        </div>
        <input type="hidden" id="sesskey" name="sesskey" value="">
        <script>document.getElementById("sesskey").value = M.cfg.sesskey;</script>
    </fieldset>
    <div>
        <input type="submit" name="submit" id="submit" value="Soumettre la demande pour ce cours">
    </div>
</form>';
$string['formquickquestion'] = '<form action="{wwwroot}/local/contact/index.php" method="post" class="cf contact-us">
<fieldset>
    <div class="form-group">
        <label for="objet" id="subjectlabel" class="d-block">Objet <strong class="required">(obligatoire)</strong></label>
        <input class="block" id="objet" name="objet" type="text" size="57" maxlength="80" minlength="5"
                title="Minimum de 5 caractères." required="required">
    </div>
    <div class="form-group">
        <label for="message" id="messagelabel" class="d-block">Message <strong class="required">(obligatoire)</strong></label>
        <textarea id="message" name="message" rows="5" cols="58" minlength="5"
                title="Minimum de 5 caractères." required="required"></textarea>
    </div>
    <input type="hidden" id="sesskey" name="sesskey" value="">
    <script>document.getElementById("sesskey").value = M.cfg.sesskey;</script>
        {recaptcha}
</fieldset>
<div>
    <input type="submit" name="submit" id="submit" value="Send">
</div>
</form>';
$string['formsupport'] = '<form action="{wwwroot}/local/contact/index.php" method="post" class="cf support-request">
    <fieldset>
        <div class="form-group">
            <label for="nom" id="namelabel" class="d-block">Votre nom <strong class="required">(obligatoire)</strong></label>
            <input id="nom" name="nom" type="text" size="57" maxlength="45" pattern="[A-zÀ-ž]([A-zÀ-ž\\s]){2,}"
                    title="Minimum de 3 lettres/espaces." required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{fullname}">
        </div>
        <div class="form-group">
            <label for="courriel" id="emaillabel" class="d-block">Adresse courriel <strong class="required">(obligatoire)</strong></label>
            <input id="courriel" name="courriel" type="email" size="57" maxlength="60" required="required" {readonly}{ifloggedin} disabled{/ifloggedin} value="{email}">
        </div>
        <div class="form-group">
            <label for="objet" id="subjectlabel" class="d-block">Objet <strong class="required">(obligatoire)</strong></label>
            <select id="objet" name="objet" required="required">
                <option label="Choisir un objet"></option>
                <option>Je ne peux pas réinitialiser mon mot de passe</option>
                <option>Je n\'arrive pas à ouvrir une session</option>
                <option value="Suggestion">J\'ai une suggestion</option>
                <option value="Message d\'erreur">Je reçois un message d\'erreur</option>
                <option value="Erreur système">Quelque chose ne fonctionne pas comme prévu</option>
                <option value="Cours">J\'éprouve des difficultés à accéder à un cours en particulier ou à son contenu</option>
                <option value="Autre raison">Autre (veuillez préciser)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="objet_specifique" id="specifylabel" class="d-block">Objet spécifique ou nom du cours <strong class="required">(obligatoire)</strong></label>
            <input type="text" id="objet_specifique" name="objet_specifique" size="57" maxlength="80" required="required">
        </div>
        <div class="form-group">
            <label for="url" id="urllabel" class="d-block">Spécifiez l\'adresse URL </label>
            <input type="url" id="url" name="url" size="57" maxlength="80" value="{referer}">
        </div>
        <div class="form-group">
            <label for="description" id="descriptionlabel" class="d-block">Description et détails étape par étape sur la façon de reproduire le problème. <strong class="required">(obligatoire)</strong></label>
            <textarea id="description" name="description" rows="5" cols="58" minlength="5"
                    title="Minimum de 5 caractères." required="required"></textarea>
        </div>
        <input type="hidden" id="sesskey" name="sesskey" value="">
        <script>document.getElementById("sesskey").value = M.cfg.sesskey;</script>
        {recaptcha}
    </fieldset>
    <div>
        <input type="submit" name="submit" id="submit" value="Soumettre votre demande d\'aide">
    </div>
</form>';
$string['globaltagcontentdesc'] = 'C’est le contenu que votre balise globale remplacera. Exemple : si votre balise s’appelle « {global_adresse} », cette balise sera remplacée par le contenu entré dans ce champ.';
$string['globaltagcontenttitle'] = 'Contenu';
$string['globaltagcount'] = 'Nombre de balises globales.';
$string['globaltagcountdesc'] = 'Sélectionner le nombre de balises que vous souhaitez définir. Pour des performances optimales, sélectionner uniquement le nombre dont vous aurez besoin.';
$string['globaltagheadingdesc'] = 'Définir vos propres balises globales, parfois également appelées blocs globaux.';
$string['globaltagheadingtitle'] = 'Balises personnalisées globales';
$string['globaltagnamedesc'] = 'Cela fera partie du nom de votre balise, précédé par « global_ ». Exemple : si vous saisissez « adresse » ici, votre balise s\'appellera {global_adresse}. Elle doit être constituée d\'une seule chaîne composée uniquement de lettres. Aucun espace, chiffre ou caractère spécial n\'est autorisé.';
$string['globaltagnametitle'] = 'Balise&nbsp;: global_';
$string['hidecompletedcourses'] = 'Masquer les cours achevés';
$string['hidecompletedcourses_desc'] = 'Activer pour filtrer les cours achevés dans la liste des balises {mycoursesmenu}.';
$string['ifprofilefiedonlyvisible'] = '{ifprofile_field_} uniquement visible.';
$string['ifprofilefiedonlyvisible_desc'] = 'Lorsque cette case est cochée, la balise {ifprofile_field_...} accède uniquement aux champs de profil utilisateur visibles. Les champs cachés se comporteront comme s\'ils étaient vides. Si elle n\'est pas cochée, cette balise pourra également vérifier les champs masqués.';
$string['narrowpage'] = 'Page étroite';
$string['narrowpage_desc'] = 'Activez cette option pour optimiser l\'affichage des informations si Moodle utilise un thème avec une largeur de page limitée (par exemple, Boost dans Moodle 4.0).';
$string['pagebuilder'] = 'Constructeur de page';
$string['pagebuilderlink'] = 'https://www.layoutit.com/build';
$string['photoeditor'] = 'Éditeur de photos';
$string['photoeditorlink'] = 'https://pixlr.com/editor/';
$string['pluginname'] = 'Filter Codes';
$string['privacy:metadata'] = 'Le plugin FilterCodes n\'enregistre aucune donnée personnelle.';
$string['screenrec'] = 'Enregistreur d\'écran';
$string['screenreclink'] = 'https://screenapp.io/#/recording';
$string['showhiddenprofilefields'] = 'Afficher les champs de profil cachés';
$string['showhiddenprofilefields_desc'] = 'Active la balise {profile_field_...} pour traiter tous les champs de profil, y compris ceux masqués pour l\'utilisateur.';
$string['sizeb'] = 'o';
$string['sizeeb'] = 'Eo';
$string['sizegb'] = 'Go';
$string['sizekb'] = 'ko';
$string['sizemb'] = 'Mo';
$string['sizetb'] = 'To';
$string['sizeyb'] = 'Yo';
$string['sizezb'] = 'Zo';
$string['teamcardsformat'] = 'Format des cartes d\'équipe';
$string['teamcardsformat_desc'] = 'Choisissez comment les membres de l\'équipe apparaîtront dans la balise {teamcards}.<br>
<ul>
<li>Aucun : affiche uniquement l\'image et le nom sous forme de carte sans la description de l\'utilisateur.</li>
<li>Icône : identique à aucun, sauf que la description de l\'utilisateur apparaît dans une bulle d\'informations.</li>
<li>Bref : identique à aucun, mais affiche la description sous la photo et le nom de l\'utilisateur.</li>
<li>Verbeux : format de liste. Recommandé si les membres de votre équipe ont tendance à avoir de longues descriptions d\'utilisateurs.</li>
</ul>';
$string['teamcardslinktype'] = 'Type de lien d\'équipe';
$string['teamcardslinktype_desc'] = 'Choisissez le type de lien pour le lien du membre de l\'équipe dans la balise {teamcards}. Remarque : La photo sera automatiquement liée au profil lorsque l\'utilisateur est connecté, quel que soit votre choix ici.';
$string['verbose'] = 'Verbeux';
