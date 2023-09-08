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
 * Strings for component 'scheduler', language 'fr', version '4.1'.
 *
 * @package     scheduler
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['action'] = 'Action';
$string['actions'] = 'Actions';
$string['addappointment'] = 'Ajouter un autre étudiant';
$string['addcommands'] = 'Ajouter des créneaux';
$string['addondays'] = 'Ajouter des rendez-vous';
$string['addsession'] = 'Ajouter des créneaux consécutifs';
$string['addsingleslot'] = 'Ajouter un créneau';
$string['addslot'] = 'Vous pouvez ajouter des créneaux horaires à tout moment.';
$string['addstudenttogroup'] = 'Ajouter un étudiant au groupe';
$string['allappointments'] = 'Tous les rendez-vous';
$string['allononepage'] = 'Tous les créneaux sur une page';
$string['allowgroup'] = 'Créneau exclusif - cliquer pour changer';
$string['alreadyappointed'] = 'Impossible de fixer un entretien. Le créneau est déjà plein.';
$string['appointagroup_help'] = 'Choisir si vous voulez prendre rendez-vous pour vous seul ou pour un groupe entier.';
$string['appointfor'] = 'Prendre rendez-vous pour';
$string['appointforgroup'] = 'Prendre un rendez-vous pour : {$a}';
$string['appointingstudent'] = 'Ajouter un rendez-vous';
$string['appointingstudentinnew'] = 'Ajouter un rendez-vous';
$string['appointment'] = 'Rendez-vous';
$string['appointmentmode'] = 'Option sur le mode de rendez-vous';
$string['appointmentmode_help'] = '<p>Vous pouvez choisir différentes méthodes de prise de rendez-vous. </p>
<p><ul>
<li><strong>« <emph>n</emph> rendez-vous » :</strong> L\'étudiant peut choisir autant de rendez-vous qu\'il souhaite, dans la limite du nombre de rendez-vous défini dans l\'activité. Même si l\'enseignant les marque comme « vu », l\'étudiant ne sera pas autorisé à choisir un autre rendez-vous. La seule manière de redonner la possibilité à l\'étudiant de choisir un autre rendez-vous est de supprimer l\'ancien rendez-vous.</li>
<li><strong>« <emph>n</emph> rendez-vous à la fois » :</strong> L\'étudiant peut choisir parmi un nombre limité de rendez-vous. Une fois le rendez-vous passé et que l\'enseignant a noté comme ayant « vu » l\'étudiant, l\'étudiant peut alors choisir un autre rendez-vous. Cependant l\'étudiant est limité aux <emph>n</emph> créneaux disponibles (non vu) restants.
</li>
</ul>
</p>';
$string['appointmentno'] = 'Séance {$a}';
$string['appointmentnote'] = 'Commentaire d\'entretien (visible pour l\'étudiant)';
$string['appointments'] = 'Réservations';
$string['appointmentsgrouped'] = 'Rendez-vous groupés par créneaux';
$string['appointmentsummary'] = 'Rendez-vous le {$a->startdate} de {$a->starttime} à {$a->endtime} avec {$a->teacher}';
$string['appointsolo'] = 'moi seulement';
$string['appointsomeone'] = 'Ajouter un étudiant';
$string['areaappointmentnote'] = 'Fichiers dans l\'annotation d\'entretien';
$string['areaslotnote'] = 'Fichiers dans l\'annotation de créneau';
$string['areateachernote'] = 'Fichiers dans l\'annotation confidentielle';
$string['attendable'] = 'Étudiants potentiels';
$string['attendablelbl'] = 'Nombre d\'étudiants total pouvant être reçus';
$string['attended'] = 'Étudiants reçus';
$string['attendedlbl'] = 'Nombre d\'étudiants total ayant été reçus';
$string['attendedslots'] = 'Créneaux réservés';
$string['availableslots'] = 'Créneaux ouverts';
$string['availableslotsall'] = 'Tous les créneaux';
$string['availableslotsnotowned'] = 'Au nom des autres';
$string['availableslotsowned'] = 'Pour soi-même';
$string['bookaslot'] = 'Réserver un créneau';
$string['bookingdetails'] = 'Détails de la réservation';
$string['bookingformoptions'] = 'Formulaire de réservation et données fournies par l\'étudiant';
$string['bookinginstructions'] = 'Instructions de réservation';
$string['bookinginstructions_help'] = 'Ce texte sera affiché aux étudiants avant de faire une réservation. Il peut, par exemple, indiquer aux étudiants comment remplir le champ de message facultatif ou les fichiers à télécharger.';
$string['bookslot'] = 'Réserver un créneau';
$string['bookwithteacher'] = 'Enseignant';
$string['bookwithteacher_help'] = 'Choisir un enseignant pour le rendez-vous.';
$string['break'] = 'Pause entre les créneaux';
$string['breaknotnegative'] = 'La durée de la pause ne peut pas être négative';
$string['canbook1appointment'] = 'Vous pouvez choisir un rendez-vous de plus.';
$string['canbooknappointments'] = 'Vous pouvez choisir {$a} rendez-vous de plus.';
$string['canbooknofurtherappointments'] = 'Vous ne pouvez plus choisir de rendez-vous.';
$string['canbooksingleappointment'] = 'Vous pouvez choisir un rendez-vous.';
$string['canbookunlimitedappointments'] = 'Vous pouvez choisir un certain nombre de rendez-vous.';
$string['cancelbooking'] = 'Annuler le rendez-vous';
$string['chooseexisting'] = 'Choisir un créneau existant';
$string['choosingslotstart'] = 'Choisir le début';
$string['choosingslotstart_help'] = 'Changer (ou choisir) la date de début du rendez-vous. Si le rendez-vous chevauche avec d\'autres créneaux, il vous sera demandé si le créneau doit remplacer tous les autres rendez-vous conflictuels. À noter que les paramètres du nouveau créneau remplacera tous les paramètres précédents.';
$string['comments'] = 'Commentaires';
$string['complete'] = 'Complet';
$string['confirmbooking'] = 'Confirmer la réservation';
$string['confirmdelete-all'] = 'Cette action supprimera <b>tous</b> les créneaux définis. La suppression est irréversible. Voulez-vous continuer ?';
$string['confirmdelete-mine'] = 'Cette action supprimera tous vos créneaux. La suppression est irréversible. Voulez-vous continuer ?';
$string['confirmdelete-myunused'] = 'Cette action supprimera tous vos créneaux non utilisés. La suppression est irréversible. Voulez-vous continuer ?';
$string['confirmdelete-one'] = 'Supprimer le créneau ?';
$string['confirmdelete-selected'] = 'Cette action supprimera les créneaux sélectionnés. La suppression est irréversible. Voulez-vous continuer?';
$string['confirmdelete-unused'] = 'Cette action supprimera tous les créneaux non utilisés. La suppression est irréversible. Voulez-vous continuer?';
$string['confirmrevoke'] = 'Révoquer toutes les rendez-vous dans le créneau actuel ?';
$string['conflictingslots'] = 'Il y a des créneaux horaires incompatibles :<br/>';
$string['conflictlocal'] = '{$a->datetime} ({$a->duration} minutes) dans ce planificateur de rendez-vous';
$string['conflictremote'] = '{$a->datetime} ({$a->duration} minutes) dans le cours {$a->courseshortname}, planificateur de rendez-vous {$a->schedulername}';
$string['contentformat'] = 'Format';
$string['contentformat_help'] = '<p>Il y a trois choix de base pour le format d\'exportation, présentant de façons différentes la manipulation des créneaux de plusieurs rendez-vous.
     <dl>
          <dt>Une ligne par créneau</ dt> :
          <dd>
Le fichier de sortie contient une ligne pour chaque créneau. Si un créneau contient plusieurs
rendez-vous, un marqueur « (multiple) » sera affiché à la place du nom de l\'étudiant, etc.
         </dd>
          <dt> Une ligne par rendez-vous</dt> :
          <dd>
Le fichier de sortie contient une ligne pour chaque rendez-vous. Si un créneau contient plusieurs
rendez-vous, alors il apparaît plusieurs fois dans la liste (avec ses données répétées).
          </dd>
          <dt>Nominations regroupées par emplacement</dt> :
          <dd>
Tous les rendez-vous d\'un créneau sont regroupés, précédés par une ligne d\'en-tête
indiquant l\'emplacement en question. Cela peut ne pas bien fonctionner avec le format de fichier de sortie CSV, comme le nombre de colonnes non constant.
          </dd>
     </dl>
Vous pouvez vérifier l\'effet de ces options en utilisant le bouton « Prévisualisation ». </ P>';
$string['copytomyself'] = 'M\'envoyer une copie';
$string['course'] = 'Cours';
$string['createexport'] = 'Créer le fichier d\'exportation';
$string['csvfieldseparator'] = 'Séparateur de champs CSV';
$string['csvformat'] = 'CSV';
$string['cumulatedduration'] = 'Durée totale des rendez-vous';
$string['datatoinclude'] = 'Données à inclure';
$string['datatoinclude_help'] = 'Sélectionnez les champs qui doivent être inclus dans l\'exportation. Chacun apparaîtra dans une colonne du fichier de sortie.';
$string['date'] = 'Date';
$string['datelist'] = 'Synthèse';
$string['defaultslotduration'] = 'Durée du créneau par défaut';
$string['defaultslotduration_help'] = 'La durée par défaut (en minutes) pour les créneaux que vous ajoutez.';
$string['deleteallslots'] = 'Supprimer tous les créneaux';
$string['deleteallunusedslots'] = 'Supprimer les créneaux non utilisés';
$string['deletecommands'] = 'Supprimer les créneaux';
$string['deletedconflictingslots'] = 'Pour le créneau de {$a}, des créneaux entrant en conflit ont été supprimés';
$string['deletemyslots'] = 'Supprimer tous mes créneaux';
$string['deleteonsave'] = 'Supprimer ce rendez-vous (lors de la sauvegarde du formulaire)';
$string['deleteselection'] = 'Supprimer les créneaux sélectionnés';
$string['deletetheseslots'] = 'Supprimer ces créneaux';
$string['deleteunusedslots'] = 'Supprimer mes créneaux non utilisés';
$string['department'] = 'D\'où ?';
$string['disengage'] = 'Annuler mes rendez-vous';
$string['displayfrom'] = 'Afficher les créneaux de rendez-vous aux étudiants à partir de';
$string['distributetoslot'] = 'Distribuer à tout le groupe';
$string['divide'] = 'Diviser en créneaux ?';
$string['duration'] = 'Durée';
$string['durationrange'] = 'La durée d\'un créneau doit se trouver entre {$a->min} et {$a->max} minutes.';
$string['editbooking'] = 'Modifier la réservation';
$string['email_applied_html'] = '<p>Un rendez-vous a été choisi le {$a->date} à {$a->time},<br/>
par l\'étudiant <a href="{$a->attendee_url}">{$a->attendee}</a> pour le cours :

<p>{$a->course_short}: <a href="{$a->course_url}">{$a->course}</a></p>

<p>dans le planning intitulé « <em><a href="{$a->scheduler_url}">{$a->module}</a></em> » sur le site : <a href="{$a->site_url}">{$a->site}</a>.</p>';
$string['email_applied_plain'] = 'Un rendez-vous a été pris le {$a->date} à {$a->time},
par l\'étudiant {$a->attendee} pour le cours :

{$a->course_short}: {$a->course}

dans le planning intitulé « {$a->module} » sur le site : {$a->site}.';
$string['email_applied_subject'] = '{$a->course_short}: Nouveau rendez-vous';
$string['email_cancelled_html'] = '<p>Votre rendez-vous le <strong>{$a->date}</strong> à <strong>{$a->time}</strong>,<br/>
avec l\'étudiant <strong><a href="{$a->attendee_url}">{$a->attendee}</a></strong> pour le cours :</p>

<p><strong>{$a->course_short} : <a href="{$a->course_url}">{$a->course}</a></strong></p>

<p>dans le planning intitulé « <em><a href="{$a->scheduler_url}">{$a->module}</a>}</em> » sur le site : <strong><a href="{$a->site_url}">{$a->site}</a></strong></p>

<p><strong><span class="error">a été annulé ou déplacé</span></strong>.</p>';
$string['email_cancelled_plain'] = 'Votre rendez-vous du {$a->date} à {$a->time},
avec l\'étudiant {$a->attendee} pour le cours&nbsp;:

{$a->course_short}&nbsp;: {$a->course}

dans le planning de «&nbsp;{$a->module}&nbsp;» sur le site&nbsp;: {$a->site}

a été annulé ou déplacé.';
$string['email_cancelled_subject'] = '{$a->course_short}: Rendez-vous annulé ou déplacé par un étudiant';
$string['email_invite_html'] = '<p>Veuillez choisir un créneau horaire pour un rendez-vous à l\'adresse:</p> <p>{$a->scheduler_url}</p>';
$string['email_invite_subject'] = 'Invitation: {$a->module}';
$string['email_invitereminder_html'] = '<p>Rappel: vous n\'avez pas encore fixé votre rendez-vous. Veuillez choisir un créneau horaire dès que possible à l\'adresse: </p><p>{$a->scheduler_url}</p>';
$string['email_invitereminder_subject'] = 'Rappel: {$a->module}';
$string['email_reminder_html'] = '<p>Vous avez un prochain rendez-vous le <strong>{$a->date}</strong>
de <strong>{$a->time}</strong> à <strong>{$a->endtime}</strong><br/>
avec <strong><a href="{$a->attendant_url}">{$a->attendant}</a></strong>.</p>

<p>Lieu: <strong>{$a->location}</strong></p>';
$string['email_reminder_plain'] = 'Vous avez un rendez-vous prévu prochainement
le {$a->date} de {$a->time} à {$a->endtime}
avec {$a->attendant}.

Lieu : {$a->location}';
$string['email_reminder_subject'] = '{$a->course_short}: Rappel de rendez-vous';
$string['email_teachercancelled_html'] = '<p>Votre rendez-vous le <strong>{$a->date}</strong> à <strong>{$a->time} </strong>,<br/>
avec le rôle {$a->staffrole} <strong><a href="{$a->attendant_url}">{$a->attendant}</a></strong> pour le cours :</p>

<p><strong>{$a->course_short} : <a href="{$a->course_url}">{$a->course}</a></strong></p>

<p>dans le planning intitulé « <em><a href="{$a->site_url}">{$a->site}</a></em> » sur le site : <strong><a href="{$a->site_url}">{$a->site}</a></strong></p>

<p><strong><span class="error">a été annulé</span></strong>. Choisissez un nouveau créneau horaire.</p>';
$string['email_teachercancelled_plain'] = 'Votre rendez-vous du {$a->date} à {$a->time},
avec le {$a->staffrole} {$a->attendant} pour le cours :

{$a->course_short} : {$a->course}

dans le planning intitulé « {$a->module} » sur le site : {$a->site}

a été annulé. Veuillez choisir un nouveau créneau.';
$string['email_teachercancelled_subject'] = '{$a->course_short}: Rendez-vous annulé par l\'enseignant';
$string['emailreminder'] = 'Rappel par courriel';
$string['emailreminderondate'] = 'Envoyer un rappel le';
$string['end'] = 'Fin';
$string['enddate'] = 'Répéter les créneaux jusqu\'au';
$string['event_appointmentlistviewed'] = 'Liste des rendez-vous consultée';
$string['event_bookingadded'] = 'Réservation ajoutée';
$string['event_bookingformviewed'] = 'Formulaire de réservation consulté';
$string['event_bookingremoved'] = 'Réservation supprimée';
$string['event_slotadded'] = 'Créneau ajouté';
$string['event_slotdeleted'] = 'Créneau supprimé';
$string['everyone'] = 'Tout le monde';
$string['excelformat'] = 'Excel';
$string['exclusive'] = 'Exclusif';
$string['exclusivity'] = 'Exclusivité';
$string['exclusivity_help'] = '<p>Vous pouvez fixer un nombre de places limité pour un créneau horaire donné.</p>
<p>Fixer la limite à 1 (par défaut) limitera le créneau à un seul étudiant.</p>
<p>Fixer une limite par ex. à 3 autorisera 3 étudiants à réserver ce créneau.</p>
<p>Si cette option est désactivée, un nombre illimité d\'étudiants pourront réserver le créneau ; il ne sera jamais considéré comme « complet ».</p>';
$string['exclusivityoverload'] = 'Le créneau possède {$a} étudiants inscrits, ce qui est plus que la valeur permise par ce paramètre.';
$string['exclusivitypositive'] = 'Le nombre d\'étudiants par créneau doit être de 1 ou plus.';
$string['explaingeneralconfig'] = 'Ces options sont réglables au niveau site et sont applicables à tous les Rendez-vous actifs sur cette plate-forme.';
$string['export'] = 'Exporter';
$string['exporthdr'] = 'Exporter les créneaux et les rendez-vous';
$string['exporttimerange'] = 'Période de référence';
$string['exporttimerangeall'] = 'Créneaux horaires passés et futurs';
$string['exporttimerangefuture'] = 'Seulement les créneaux horaires futurs';
$string['exporttimerangepast'] = 'Seulement les créneaux horaires passés';
$string['field-appointmentnote'] = 'Commentaire sur l\'entretien (pour l\'étudiant)';
$string['field-attended'] = 'Présent';
$string['field-date'] = 'Date';
$string['field-endtime'] = 'Heure de fin';
$string['field-filecount'] = 'Nombre de fichiers téléversés';
$string['field-grade'] = 'Note';
$string['field-groupsmulti'] = 'Groupes (plusieurs colonnes)';
$string['field-groupssingle'] = 'Groupes';
$string['field-groupssingle-label'] = 'Groupes (une colonne)';
$string['field-location'] = 'Lieu';
$string['field-maxstudents'] = 'Étudiants max.';
$string['field-slotnotes'] = 'Notes du créneau';
$string['field-starttime'] = 'Heure de début';
$string['field-studentemail'] = 'Adresse courriel de l\'étudiant';
$string['field-studentfirstname'] = 'Prénom de l\'étudiant';
$string['field-studentfullname'] = 'Nom complet de l\'étudiant';
$string['field-studentidnumber'] = 'Numéro d\'identification de l\'étudiant';
$string['field-studentlastname'] = 'Nom de famille de l\'étudiant';
$string['field-studentnote'] = 'Message par étudiant';
$string['field-studentusername'] = 'Nom d\'usager de l\'étudiant';
$string['field-teachernote'] = 'Annotation sur l\'entretien (pour l\'enseignant uniquement)';
$string['fileformat'] = 'Format de fichier';
$string['fileformat_help'] = 'Les formats de fichier suivants sont disponibles :
     <ul>
          <li> Format texte séparé par virgule (CSV). Le séparateur de champ, par défaut, une virgule, peut être choisi ci-dessous.
Les fichiers CSV peuvent être ouverts dans la plupart des tableurs ; </li>
          <li> Format Microsoft Excel (Excel format 2007) ; </li>
          <li> Feuilles de calcul Open Document (ODS) ; </li>
          <li> Format HTML - une page Web affichant la table de sortie,
qui peut être imprimée en utilisant la fonction d\'impression du navigateur ; </li>
          <li> Documents PDF. Vous pouvez choisir entre orientation paysage ou portrait. </li>
     </ul>';
$string['finalgrade'] = 'Note de l\'activité';
$string['firstslotavailable'] = 'Le premier créneau horaire sera ouvert le : {$a}';
$string['forbidgroup'] = 'Créneau de groupe - cliquer pour changer';
$string['forcewhenoverlap'] = 'Forcer si recouvrements';
$string['forcewhenoverlap_help'] = '<h3>Forcer la création d\'un créneau lors de chevauchement de créneaux</h3>
<p>Ce paramètre fixe le traitement des nouveaux créneaux lorsque ceux-ci chevauchent des créneaux existants.</p>
<p>Lorsqu\'activé, le créneau existant qui se fait chevaucher est supprimé et le nouveau créneau créé.</p>
<p>Lorsque désactivé, le créneau existant qui se fait chevaucher est conservé et le nouveau créneau <em>n\'est pas</em> créé.</p>';
$string['forcewhenoverlap_link'] = 'mod/scheduler/conflict';
$string['forcourses'] = 'Cours concernés';
$string['friday'] = 'Vendredi';
$string['generalconfig'] = 'Configuration générale';
$string['grade'] = 'Note';
$string['gradeingradebook'] = 'Note dans le carnet';
$string['gradingstrategy'] = 'Stratégie de notation';
$string['gradingstrategy_help'] = 'Dans un planning où les étudiants peuvent avoir plusieurs entretiens, choisissez comment la notation doit être affichée.<br/> Le carnet de notes peut afficher soit <ul><li>la note moyenne ou</li><li>la note maximale</li></ul> que l\'étudiant a reçue.';
$string['group'] = 'groupe';
$string['groupbookings'] = 'Réservation en groupes';
$string['groupbookings_help'] = 'Permettre aux élèves de réserver un créneau pour tous les membres de leur groupe.
(Notez que ceci est différent du réglage « mode de groupe », qui contrôle la visibilité d\'un créneau par un étudiant.)';
$string['groupbreakdown'] = 'Par taille de groupe';
$string['groupmodeyourgroups'] = 'Mode de groupe : {$a->groupmode}. Seuls les étudiants de {$a->grouplist} peuvent prendre rendez-vous avec vous.';
$string['groupmodeyourgroupsempty'] = 'Mode de groupe : {$a->groupmode}. Vous n\'êtes pas membre d\'un groupe, donc les étudiants ne peuvent prendre rendez-vous avec vous.';
$string['groupscheduling'] = 'Autoriser les rendez-vous collectifs';
$string['groupscheduling_desc'] = 'Autoriser la planification simultanée de groupes entiers.
(Outre l\'option globale, le paramètre « Réservation en groupes » doit être activé dans  chaque module Prise de rendez-vous.)';
$string['groupsession'] = 'Rendez-vous collectif';
$string['groupsize'] = 'Taille du groupe';
$string['guardtime'] = 'Délai d\'annulation';
$string['guardtime_help'] = 'Le délai d\'annulation empêche l\'étudiant de changer son créneau horaire peu avant le début de son rendez-vous.
<p>Si le délai d\'annulation est activé et défini, par exemple, à 2 heures, alors l\'étudiant ne pourra plus modifier son rendez-vous 2 heures avant le début de celui-ci.</p>';
$string['guestscantdoanything'] = 'Les invités ne peuvent pas utiliser cette activité.';
$string['howtoaddstudents'] = 'Pour ajouter des étudiants à un planificateur global, utilisez le réglage des rôles du module.<br/>Vous pouvez également définir par les rôles les personnes qui pourront accueillir vos étudiants.';
$string['htmlformat'] = 'HTML';
$string['ignoreconflicts'] = 'Ignorer les conflits de plannings';
$string['ignoreconflicts_help'] = 'Si cette case est cochée, alors le créneau sera déplacé à la date et l\'heure demandées, même si d\'autres créneaux existent au même moment.
Cela peut provoquer des chevauchements d\'entretiens pour certains enseignants ou étudiants, et doit donc être utilisé avec précaution.';
$string['ignoreconflicts_link'] = 'mod/scheduler/conflict';
$string['includeemptyslots'] = 'Inclure les créneaux vides';
$string['includeslotsfor'] = 'Inclure les créneaux pour';
$string['incourse'] = 'dans le cours';
$string['introduction'] = 'Introduction';
$string['isnonexclusive'] = 'Non exclusif';
$string['landscape'] = 'Paysage';
$string['lengthbreakdown'] = 'Par longueur de créneau';
$string['limited'] = 'Limité (reste {$a})';
$string['location'] = 'Lieu';
$string['location_help'] = 'Spécifier le lieu prévu pour le rendez-vous.';
$string['markasseennow'] = 'Marquer comme lu maintenant';
$string['markseen'] = 'Après le rendez-vous avec un étudiant, veuillez le marquer comme « Vu » en cochant la case adéquate dans le tableau ci-dessus.';
$string['maxgrade'] = 'Prendre la plus haute des notes';
$string['maxstudentlistsize'] = 'Taille maximum de la liste des étudiants';
$string['maxstudentlistsize_desc'] = 'La taille maximum de la liste d\'étudiants qui doivent prendre un rendez-vous, affichée dans la vue Enseignant. S\'il y a plus d\'étudiants que cela, aucune liste ne sera affichée.';
$string['maxstudentsperslot'] = 'Nombre maximum d\'étudiants par créneau';
$string['maxstudentsperslot_desc'] = 'Les créneaux de groupes / les groupes non-exclusif peuvent contenir au plus ce nombre d\'étudiant. Notez, en outre, que le paramètre « illimité » peut toujours être défini pour un créneau en particulier.';
$string['meangrade'] = 'Prendre la moyenne des notes';
$string['meetingwith'] = 'Rendez-vous avec votre';
$string['meetingwithplural'] = 'Rendez-vous avec vos';
$string['message'] = 'Message';
$string['messagebody'] = 'Corps du message';
$string['messageprovider:bookingnotification'] = 'Notification lorsqu\'un rendez-vous est pris ou annulé';
$string['messageprovider:invitation'] = 'Invitation à réserver un créneau';
$string['messageprovider:reminder'] = 'Rappel d\'un entretien à venir';
$string['messagesent'] = 'Message envoyé à {$a} destinataires';
$string['messagesubject'] = 'Sujet';
$string['minutes'] = 'minutes';
$string['minutesperslot'] = 'minutes par créneau';
$string['missingstudents'] = '{$a} étudiant(s) doi(ven)t encore prendre rendez-vous';
$string['missingstudentsmany'] = '{$a} étudiant(s) doi(ven)t encore prendre rendez-vous. La liste n\'est pas affichée à cause de sa taille trop importante.';
$string['mixindivgroup'] = 'Mélanger les réservations individuelles et collectives';
$string['mixindivgroup_desc'] = 'Lorsque la planification des groupes est activée, cela permet également les réservations individuelles.';
$string['mode'] = 'Mode';
$string['modeappointments'] = 'rendez-vous';
$string['modeintro'] = 'Les étudiants peuvent enregistrer';
$string['modeoneatatime'] = 'à la fois';
$string['modeoneonly'] = 'dans ce planning';
$string['modulename'] = 'Rendez-vous';
$string['modulename_help'] = 'L\'activité rendez-vous vous aide à planifier vos rendez-vous avec vos étudiants.

Les enseignants définissent des créneaux horaires, puis les étudiants en choisissent un sur Moodle. À leur tour, les enseignants peuvent exporter la liste des rendez-vous et optionnellement définir une note.

Les rendez-vous par groupe est disponible ; chaque créneau horaire peut accueillir plusieurs étudiants, et éventuellement il est possible de prendre rendez-vous pour des groupes entiers en même temps.';
$string['modulename_link'] = 'mod/scheduler/view';
$string['modulenameplural'] = 'Rendez-vous';
$string['monday'] = 'Lundi';
$string['multiple'] = '(multiple)';
$string['myappointments'] = 'Mes rendez-vous';
$string['myself'] = 'Moi-même';
$string['name'] = 'Titre du planning';
$string['needteachers'] = 'Les créneaux ne peuvent pas être ajoutés car ce cours n\'a pas d\'enseignant';
$string['negativerange'] = 'Plage négative impossible';
$string['never'] = 'Jamais';
$string['nfiles'] = '{$a} fichier(s)';
$string['noappointments'] = 'Aucun rendez-vous enregistré';
$string['noexistingstudents'] = 'Aucun étudiant disponible pour la prise de rendez-vous';
$string['nogroups'] = 'Aucun groupe disponible';
$string['noresults'] = 'Pas de rendez-vous.';
$string['noschedulers'] = 'Personne dans le planning';
$string['noslots'] = 'Aucun créneau horaire n\'est disponible pour un rendez-vous. Veuillez contacter votre enseignant pour fixer un rendez-vous.';
$string['noslotsavailable'] = 'Aucun rendez-vous exigé, ou tous les créneaux définis sont complets.';
$string['noslotsopennow'] = 'Aucun créneau n\'est ouvert à la réservation pour le moment.';
$string['nostudents'] = 'Aucun étudiant prévu';
$string['nostudenttobook'] = 'Aucun étudiant à planifier';
$string['note'] = 'Note';
$string['noteacherforslot'] = 'Vous n\'avez pas choisi d\'enseignant';
$string['noteachershere'] = 'Aucun enseignant disponible';
$string['notenoughplaces'] = 'Désolé, il n\'y a plus de créneaux disponibles à cette date.';
$string['notesrequired'] = 'Vous devez écrire du texte dans ce champ avant de réserver le créneau';
$string['notifications'] = 'Notifications';
$string['notifications_help'] = 'Lorsque cette option est cochée, les enseignants et les étudiants reçoivent des notifications lorsque des rendez-vous sont pris ou annulés.';
$string['notseen'] = 'Pas vu';
$string['now'] = 'Maintenant';
$string['occurrences'] = 'Occurrences';
$string['odsformat'] = 'ODS';
$string['on'] = 'le';
$string['onedaybefore'] = '1 jour avant le rendez-vous';
$string['onelineperappointment'] = 'Une ligne par rendez-vous';
$string['onelineperslot'] = 'Une ligne par créneau';
$string['oneslotadded'] = '1 créneau ajouté';
$string['oneslotdeleted'] = '1 créneau supprimé';
$string['oneweekbefore'] = '1 semaine avant le rendez-vous';
$string['onthemorningofappointment'] = 'Le matin du rendez-vous';
$string['options'] = 'Options';
$string['otherstudents'] = 'Autres participants';
$string['outlineappointments'] = '{$a->attended} entretiens réalisés, {$a->upcoming} à venir';
$string['outlinegrade'] = 'Note: {$a}';
$string['overall'] = 'Vue d\'ensemble';
$string['overlappings'] = 'Collisions';
$string['pageperteacher'] = 'Une page pour chaque {$a}';
$string['pagination'] = 'Pagination';
$string['pagination_help'] = 'Choisissez si l\'exportation doit contenir une page distincte pour chaque enseignant.
   Au format Excel et au format de fichier ODS, ces pages correspondent à des onglets (feuilles de calcul) dans le classeur.';
$string['pdfformat'] = 'PDF';
$string['pdforientation'] = 'Orientation de la page PDF';
$string['pluginadministration'] = 'Administration Rendez-vous';
$string['pluginname'] = 'Rendez-vous';
$string['portrait'] = 'Portrait';
$string['preview'] = 'Aperçu';
$string['previewlimited'] = '(L\'aperçu est limité à {$a} lignes.)';
$string['privacy:metadata:filepurpose'] = 'Dossier utilisé dans les annotations pour le créneau ou le rendez-vous';
$string['privacy:metadata:scheduler_appointment'] = 'Représente un rendez-vous d\'étudiant dans un planning';
$string['privacy:metadata:scheduler_appointment:appointmentnote'] = 'Annotation de l\'enseignant (visible par l\'élève)';
$string['privacy:metadata:scheduler_appointment:appointmentnoteformat'] = 'Format de l\'annotation de l\'enseignant';
$string['privacy:metadata:scheduler_appointment:attended'] = 'Si le rendez-vous a eu lieu';
$string['privacy:metadata:scheduler_appointment:grade'] = 'Note pour le rendez-vous';
$string['privacy:metadata:scheduler_appointment:studentid'] = 'L\'étudiant qui a pris le rendez-vous';
$string['privacy:metadata:scheduler_appointment:studentnote'] = 'Annotation par étudiant';
$string['privacy:metadata:scheduler_appointment:studentnoteformat'] = 'Format de l\'annotation de l\'étudiant';
$string['privacy:metadata:scheduler_appointment:teachernote'] = 'Annotation de l\'enseignant (privée)';
$string['privacy:metadata:scheduler_appointment:teachernoteformat'] = 'Format de l\'annotation privée de l\'enseignant';
$string['privacy:metadata:scheduler_slots'] = 'Représente un créneau dans un planning';
$string['privacy:metadata:scheduler_slots:appointmentlocation'] = 'Lieu de rendez-vous';
$string['privacy:metadata:scheduler_slots:duration'] = 'Durée du créneau en minutes';
$string['privacy:metadata:scheduler_slots:exclusivity'] = 'Nombre maximum d\'étudiants sur le créneau';
$string['privacy:metadata:scheduler_slots:notes'] = 'Annotations sur le créneau horaire';
$string['privacy:metadata:scheduler_slots:notesformat'] = 'Format des annotations';
$string['privacy:metadata:scheduler_slots:starttime'] = 'Heure de début du créneau';
$string['privacy:metadata:scheduler_slots:teacherid'] = 'Enseignant associé au créneau';
$string['purgeunusedslots'] = 'Supprimer les créneaux passés inutilisés';
$string['recipients'] = 'Destinataires';
$string['registeredlbl'] = 'Elèves enregistrés en rendez-vous';
$string['reminder'] = 'Rappel';
$string['requireupload'] = 'Un fichier à téléverser est requis';
$string['resetappointments'] = 'Effacer les séances et les notes';
$string['resetslots'] = 'Effacer les créneaux';
$string['return'] = 'Revenir au cours';
$string['revealteachernotes'] = 'Révéler les annotations des enseignants dans l\'exportation de la vie privée';
$string['revealteachernotes_desc'] = 'Si cette option est sélectionnée, alors les annotations confidentielles de l\'enseignant (qui ne sont normalement pas visibles par les étudiants)
seront révélées aux étudiants dans les demandes d\'exportation de données, c\'est-à-dire via l\'API privay. Vous devez décider en fonction de l\'utilisation individuelle de ce champ
si elle doit être incluse dans les exportations de données sur les étudiants dans le cadre du RGPD.';
$string['revoke'] = 'Révoquer le rendez-vous';
$string['saturday'] = 'Samedi';
$string['save'] = 'Enregistrer';
$string['savechoice'] = 'Enregistrer mon choix';
$string['saveseen'] = 'Mémoriser les présences';
$string['schedule'] = 'Planifier';
$string['scheduleappointment'] = 'Planifier un rendez-vous pour {$a}';
$string['schedulecancelled'] = '{$a} : Votre rendez-vous annulé ou déplacé';
$string['schedulegroups'] = 'Planifier par groupe';
$string['scheduleinnew'] = 'Planifier dans un nouveau créneau';
$string['scheduleinslot'] = 'Planifier dans un créneau';
$string['scheduler'] = 'Carnet';
$string['scheduler:addinstance'] = 'Ajouter un nouveau carnet de rendez-vous';
$string['scheduler:appoint'] = 'Créneau de rendez-vous';
$string['scheduler:attend'] = 'Recevoir les rendez-vous';
$string['scheduler:canscheduletootherteachers'] = 'Prendre des rendez-vous pour d\'autres membres de l\'encadrement';
$string['scheduler:canseeotherteachersbooking'] = 'Voir les rendez-vous des autres membres de l\'encadrement';
$string['scheduler:disengage'] = 'Cette fonctionnalité est obsolète et ne fait rien';
$string['scheduler:editallattended'] = 'Marquez les étudiants dans tous les rendez-vous comme étant présents / non présents.';
$string['scheduler:editallgrades'] = 'Modifier les notes dans toutes les rendez-vous.';
$string['scheduler:editallnotes'] = 'Modifier les annotations de rendez-vous dans tous les rendez-vous.';
$string['scheduler:manage'] = 'Gérer ses données de rendez-vous';
$string['scheduler:manageallappointments'] = 'Gérer toutes les données de rendez-vous';
$string['scheduler:seeotherstudentsbooking'] = 'Voir les autres étudiants ayant réservés sur ce créneau';
$string['scheduler:seeotherstudentsresults'] = 'Voir les résultats des autres étudiants du créneau';
$string['scheduler:seeoverviewoutsideactivity'] = 'Utilisez l\'écran d\'aperçu pour voir les créneaux en dehors de l\'activité-même Rendez-vous.';
$string['scheduler:viewfullslots'] = 'Afficher les créneaux même s\'ils ne peuvent plus être choisis (vue étudiant)';
$string['scheduler:viewslots'] = 'Afficher les créneaux qui peuvent encore être choisis (vue étudiant)';
$string['schedulestudents'] = 'Planifier par étudiant';
$string['scopemenu'] = 'Voir les créneaux dans : {$a}';
$string['scopemenuself'] = 'Voir mes créneaux dans : {$a}';
$string['search:activity'] = 'Planificateur d\'entretiens - statistiques';
$string['seen'] = 'Vu';
$string['selectedtoomany'] = 'Vous avez sélectionné trop de créneaux. Vous ne pouvez pas en sélectionner plus de {$a}.';
$string['sendinvitation'] = 'Envoyer une invitation';
$string['sendmessage'] = 'Envoyer un message';
$string['sendreminder'] = 'Envoyer un rappel';
$string['sendreminders'] = 'Envoyer un rappel par e-mail pour les entretiens à venir';
$string['sepcolon'] = 'Deux-points';
$string['sepcomma'] = 'Virgule';
$string['sepsemicolon'] = 'Point-virgule';
$string['septab'] = 'Tabulation';
$string['showemailplain'] = 'Afficher les adresses de courriel en texte clair';
$string['showemailplain_desc'] = 'Dans la vue enseignant du planning, afficher les adresses e-mails des étudiants voulant un entretien de façon textuelle, en plus des liens « mailto: ».';
$string['showparticipants'] = 'Afficher les participants';
$string['slot'] = 'Créneau';
$string['slot_is_just_in_use'] = 'Désolé, ce créneau vient juste d\'être choisi par un autre étudiant ! Veuillez réessayer.';
$string['slotdatetime'] = '{$a->shortdatetime} pour {$a->duration} minutes';
$string['slotdatetimelabel'] = 'Jour et heure';
$string['slotdatetimelong'] = '{$a->date}, {$a->starttime} &ndash; {$a->endtime}';
$string['slotdescription'] = '{$a->status} le {$a->startdate} de {$a->starttime} à {$a->endtime}, à {$a->location} avec {$a->facilitator}.';
$string['slots'] = 'Créneaux';
$string['slotsadded'] = '{$a} créneau(x) ajouté(s)';
$string['slotsdeleted'] = '{$a} créneaux ont été supprimés';
$string['slottype'] = 'Exclusivité';
$string['slotupdated'] = '1 créneau modifié';
$string['slotwarning'] = '<strong>Attention :</strong> l\'horaire sélectionné pour ce créneau entre en conflit avec le ou les créneaux définis ci-dessous. Cochez la case « Ignorer les conflits de plannings » si vous voulez déplacer quand même le créneau.';
$string['staffbreakdown'] = 'Par {$a}';
$string['staffrolename'] = 'Nom du rôle de l\'interlocuteur';
$string['staffrolename_help'] = 'L’étiquette pour la personne qui assiste les étudiantes et étudiants. Il ne s’agit pas nécessairement d’un « enseignant ».';
$string['start'] = 'Début';
$string['startpast'] = 'Vous ne pouvez pas ouvrir un créneau horaire dans le passé';
$string['statistics'] = 'Statistiques';
$string['student'] = 'Étudiant';
$string['studentbreakdown'] = 'Par étudiant';
$string['studentcomments'] = 'Message de l\'étudiant';
$string['studentdetails'] = 'Détail de l\'étudiant';
$string['studentfiles'] = 'Fichier(s) téléversé(s)';
$string['studentmultiselect'] = 'Chaque étudiant ne peut être sélectionné qu\'une seule fois dans ce créneau';
$string['studentnote'] = 'Message par étudiant';
$string['studentprovided'] = 'Élève pourvu : {$a}';
$string['students'] = 'Étudiants';
$string['sunday'] = 'Dimanche';
$string['tab-otherappointments'] = 'Tous les rendez-vous de cet étudiant';
$string['tab-otherstudents'] = 'Tous les étudiants dans ce créneau';
$string['tab-thisappointment'] = 'Ce rendez-vous';
$string['teacher'] = 'Enseignant';
$string['teachernote'] = 'Commentaire confidentiel (visible pour l\'enseignant seulement)';
$string['teachersmenu'] = 'Afficher les créneaux pour : {$a}';
$string['thiscourse'] = 'ce cours';
$string['thisscheduler'] = 'ce planificateur';
$string['thissite'] = 'ce site en entier';
$string['thursday'] = 'Jeudi';
$string['timefrom'] = 'De:';
$string['timerange'] = 'Plage horaire';
$string['timeto'] = 'A:';
$string['totalgrade'] = 'Note finale';
$string['tuesday'] = 'Mardi';
$string['unattended'] = 'Elèves à recevoir';
$string['unlimited'] = 'Sans limite';
$string['unregisteredlbl'] = 'Elèves sans rendez-vous';
$string['upcomingslots'] = 'Prochains créneaux horaires';
$string['updategrades'] = 'Enregistrer les notes';
$string['updatesingleslot'] = '';
$string['uploadmaxfiles'] = 'Nombre maximum de fichiers téléversés';
$string['uploadmaxfiles_help'] = 'Le nombre maximum de fichiers qu\'un étudiant peut téléverser dans le formulaire de réservation. Le téléchargement de fichier est facultatif sauf si la case « Fichier à téléverser requis » est cochée. Si cette option est définie sur 0, les étudiants ne verront pas la boîte de téléchargement de fichier.';
$string['uploadmaxfilesglobal'] = 'Nombre maximum de fichiers téléversés';
$string['uploadmaxfilesglobal_desc'] = 'Le nombre maximum de fichiers qu\'un étudiant peut téléverser dans un formulaire de réservation. Cela peut être réduit davantage au niveau des rendez-vous individuels.';
$string['uploadmaxsize'] = 'Taille maximale de fichier';
$string['uploadmaxsize_help'] = 'Taille maximale du fichier pour les téléversements par les étudiants. Cette limite s\'applique par fichier.';
$string['uploadrequired'] = 'Vous devez téléverser des fichiers avant de réserver ce créneau.';
$string['uploadstudentfiles'] = 'Téléverser des fichiers';
$string['usebookingform'] = 'Utiliser le formulaire de réservation';
$string['usebookingform_help'] = 'Si activé, l\'étudiant visualise un écran de réservation séparé avant de pouvoir réserver un créneau. L\'écran de réservation peut lui demander de saisir des données, de téléverser des fichiers ou de saisir un captcha (voir les options ci-dessous).';
$string['usebookingform_link'] = 'mod/scheduler/bookingform';
$string['usecaptcha'] = 'Utiliser le CAPTCH pour les nouvelles réservations';
$string['usecaptcha_help'] = 'Si activé, les étudiants devront résoudre une question de sécurité CAPTCHA avant de procéder à une nouvelle réservation.
Utilisez ce paramètre si vous pensez que les étudiants utilisent des programmes automatisés pour bloquer les emplacements disponibles.
<p> Aucun captcha ne sera affiché si l\'étudiant modifie une réservation existante. </p>';
$string['usenotes'] = 'Commentaires d\'entretiens';
$string['usenotesboth'] = 'Les deux types d\'annotation';
$string['usenotesnone'] = 'Aucun';
$string['usenotesstudent'] = 'Annotation d\'entretien, visible pour l\'enseignant et l\'étudiant';
$string['usenotesteacher'] = 'Annotation confidentielle, visible pour l\'enseignant seulement';
$string['usestudentnotes'] = 'Autoriser l\'étudiant à écrire un message';
$string['usestudentnotes_help'] = 'Si activé, l\'écran de réservation contiendra une zone de texte dans laquelle les étudiants peuvent écrire un message. Utilisez les « instructions de réservation » ci-dessus pour informer les étudiants de l\'information qu\'ils devraient fournir.';
$string['viewbooking'] = 'Afficher les détails';
$string['wednesday'] = 'Mercredi';
$string['welcomebackstudent'] = 'Vous pouvez réserver des créneaux horaires supplémentaires en cliquant sur le bouton « Réserver un emplacement » ci-dessous.';
$string['welcomenewstudent'] = 'Le tableau ci-dessous montre tous les créneaux horaires disponibles pour fixer un rendez-vous. Faites votre choix en cliquant sur le bouton de réservation associé. Si vous devez par la suite effectuer une modification, vous pouvez revenir sur cette page.';
$string['welcomenewteacher'] = 'Cliquez sur le bouton ci-dessous pour ajouter des créneaux horaires';
$string['what'] = 'Pourquoi ?';
$string['whathappened'] = 'Notes';
$string['whatresulted'] = 'Résultat';
$string['when'] = 'Quand ?';
$string['where'] = 'Où ?';
$string['who'] = 'Avec qui ?';
$string['whosthere'] = 'Qui est là ?';
$string['xdaysbefore'] = '{$a} jours avant le créneau';
$string['xweeksbefore'] = '{$a} semaines avant le créneau';
$string['yesallgroups'] = 'Oui, pour tous les groupes';
$string['yesingrouping'] = 'Oui, dans les groupes {$a}';
$string['yesoptional'] = 'Oui, facultatif pour l\'étudiant';
$string['yesrequired'] = 'Oui, l\'étudiant doit écrire un message';
$string['yourappointmentnote'] = 'Commentaire sur votre prestation individuelle';
$string['yourslotnotes'] = 'Commentaires généraux de séance';
$string['yourstudentnote'] = 'Votre message';
$string['yourtotalgrade'] = 'Votre note finale pour cette activité est <strong>{$a}</ strong>.';
