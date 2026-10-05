<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_groups_edition_ui.class.php,v 1.10 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_loans_groups_edition_ui extends list_loans_groups_ui {
    
    public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
        parent::__construct($filters, $pager, $applied_sort);
        $this->is_displayed_go_directly_to_block = true;
    }
    
    protected function get_mail_responsable($responsable) {
        $query = "SELECT empr_mail FROM empr WHERE empr.id_empr=".$responsable."";
        $result = pmb_mysql_query($query);
        $row=pmb_mysql_fetch_array($result);
        return $row['empr_mail'];
    }
    
    protected function get_display_group_header_list($group_label, $level=1, $uid='') {
        global $msg, $charset;
        global $sub;
        
        $display = '';
        if($level == 1) {
            $query = "SELECT id_groupe, resp_groupe FROM groupe WHERE libelle_groupe = '".addslashes(strip_tags($group_label))."'";
            $result = pmb_mysql_query($query);
            $id_groupe = pmb_mysql_result($result, 0, 'id_groupe');
            $responsable = pmb_mysql_result($result, 0, 'resp_groupe');
            // compter les totaux pour ce groupe et les retards
            $sqlcount = "SELECT count(pret_idexpl) as combien , IF(pret_retour>=curdate(),0,1) as retard ";
            $sqlcount .= "FROM exemplaires, empr, pret, empr_groupe, groupe ";
            $sqlcount .= "WHERE pret.pret_idempr = empr.id_empr AND pret.pret_idexpl = exemplaires.expl_id AND empr_groupe.empr_id = empr.id_empr AND groupe.id_groupe = empr_groupe.groupe_id and id_groupe=$id_groupe group by retard order by retard ";
            $reqcount = pmb_mysql_query($sqlcount);
            $nbok=0;
            $nbretard=0;
            while ($datacount = pmb_mysql_fetch_object($reqcount)) {
                if ($datacount->retard==0) $nbok=$datacount->combien;
                if ($datacount->retard==1) $nbretard=$datacount->combien;
            }
            $retard_sur_total = str_replace ("!!nb_retards!!",$nbretard*1,$msg['n_retards_sur_total_de']);
            $retard_sur_total = str_replace ("!!nb_total!!",($nbretard+$nbok)*1,$retard_sur_total);
            
            $display .= "
            <tr id='".$uid."_group_header' class='group_title'>
                <td colspan='".(count($this->columns)-4)."'>
                <input type='checkbox' id='".$this->objects_type."_selection_".$id_groupe."' name='".$this->objects_type."_selection[".$id_groupe."]' class='".$this->objects_type."_selection' value='".$id_groupe."'>
                <b>".$this->get_cell_group_label($group_label, ($level-1))."</b></td>
                <td colspan='3'>".htmlentities($retard_sur_total, ENT_QUOTES, $charset)."</td>";
            
            $display .= "\n<td class='center'>";
            switch ($sub) {
                case "ppargroupe":
                    $imprime_click = "onclick=\"openPopUp('./pdf.php?pdfdoc=liste_pret_groupe&id_groupe=$id_groupe', 'lettre'); return(false) \"";
                    $display .= "<a href=\"#\" ".$imprime_click."><img src='".get_url_icon('new.gif')."' title='".htmlentities($msg['imprimer_liste_prets_groupe'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['imprimer_liste_prets_groupe'], ENT_QUOTES, $charset)."' style='border:0px' /></a>\n";
                    
                    //mail responsable
                    $mail_responsable=$this->get_mail_responsable($responsable);
                    if ($mail_responsable) {
                        $mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_prets&id_groupe=$id_groupe', 'mail');} return(false) \"";
                        $display .= "<a href=\"#\" ".$mail_click."><img src='".get_url_icon('mail.png')."' title='".htmlentities($msg['mail_retard'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['mail_retard'], ENT_QUOTES, $charset)."' style='border:0px' /></a>";
                    }
                    break;
                case "rpargroupe":
                    $imprime_click = "onclick=\"openPopUp('./pdf.php?pdfdoc=lettre_retard_groupe&id_groupe=$id_groupe', 'lettre'); return(false) \"";
                    $display .= "<a href=\"#\" ".$imprime_click."><img src='".get_url_icon('new.gif')."' title='".htmlentities($msg['imprimer_lettres_groupe_relance'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['imprimer_lettres_groupe_relance'], ENT_QUOTES, $charset)."' style='border:0px' /></a>\n";
                    
                    //mail responsable
                    $mail_responsable=$this->get_mail_responsable($responsable);
                    if ($mail_responsable) {
                        $mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_retard_groupe&id_groupe=$id_groupe', 'mail');} return(false) \"";
                        $display .= "<a href=\"#\" ".$mail_click."><img src='".get_url_icon('mail.png')."' title=\"".$msg['mail_retard']."\" alt=\"".$msg['mail_retard']."\" border=\"0\"></a>";
                    }
                    break;
                default:
                    
                    break;
            }
            $display .= "</td>";
            
            $display .= "</tr>";
        } else {
        	$display = parent::get_display_group_header_list($group_label, $level, $uid);
        }
        return $display;
    }
    
    protected function get_selection_actions() {
        global $msg;
        global $base_path, $sub;
        
        if(!isset($this->selection_actions)) {
            $this->selection_actions = array();
            switch($sub) {
                case "ppargroupe" :
                    /*$relance_link = array(
                    'openPopUp' => $base_path."/pdf.php?pdfdoc=mail_liste_pret_groupe",
                    'openPopUpTitle' => 'lettre'
                        );
                    $this->selection_actions[] = $this->get_selection_action('mail_liste_prets_groupes', $msg['imprimer_envoyer_liste_prets_groupes'], '', $relance_link);
                    */
                    $relance_link = array(
                        'openPopUp' => $base_path."/pdf.php?pdfdoc=liste_pret_groupe",
                        'openPopUpTitle' => 'lettre'
                    );
                    $this->selection_actions[] = $this->get_selection_action('liste_prets_groupes', $msg['imprimer_liste_prets_groupes'], 'print.gif', $relance_link);
                    
                    /*$relance_link = array(
                        'openPopUp' => $base_path."/mail.php?type_mail=mail_prets",
                        'openPopUpTitle' => 'mail'
                    );
                    $this->selection_actions[] = $this->get_selection_action('mail_prets_groupes', $msg['envoyer_liste_prets_groupes'], 'mail.gif', $relance_link);
                    */
                    break;
                case "rpargroupe" :
                    /*$relance_link = array(
                    'openPopUp' => $base_path."/pdf.php?pdfdoc=lettre_mail_retard_groupe",
                    'openPopUpTitle' => 'lettre'
                        );
                    $this->selection_actions[] = $this->get_selection_action('lettre_mail_relance_groupe', $msg['lettres_mails_relance_groupe'], '', $relance_link);
                    */
                    $relance_link = array(
                        'openPopUp' => $base_path."/pdf.php?pdfdoc=lettre_retard_groupe",
                        'openPopUpTitle' => 'lettre'
                    );
                    $this->selection_actions[] = $this->get_selection_action('relance_groupe', $msg['lettres_relance_groupe'], 'print.gif', $relance_link);
                    
                    $relance_link = array(
                        'openPopUp' => $base_path."/mail.php?type_mail=mail_retard_groupe",
                        'openPopUpTitle' => 'mail'
                    );
                    $this->selection_actions[] = $this->get_selection_action('mail_relance_groupe', $msg['mails_relance_groupe'], 'mail.gif', $relance_link);
                    break;
                default :
                    break;
            }
        }
        return $this->selection_actions;
    }
    
    protected function get_display_html_content_selection() {
        return "";
    }
    
    protected function init_default_columns() {
        $this->add_column_selection();
        $this->add_column('cb_expl');
        $this->add_column('cote');
        $this->add_column('typdoc');
        $this->add_column('record');
        $this->add_column('author');
        $this->add_column('empr');
        $this->add_column('pret_date');
        $this->add_column('pret_retour');
        $this->add_column('late_letter', '369', '', false);
    }
    
    protected function get_selection_mode() {
        return 'button';
    }
    
    protected function get_cell_content($object, $property) {
        global $msg, $sub;
        global $biblio_email;
        
        $content = '';
        switch($property) {
            case 'late_letter':
                if ($object->retard) {
                    switch ($sub) {
                        case "ppargroupe":
                            $popUpLink = "./pdf.php?pdfdoc=liste_pret&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr;
                            $popUpLabel = $msg['prets_en_cours'];
                            $mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_prets&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'mail'); } return(false) \"";
                            break;
                        case "rpargroupe":
                            $popUpLink = "./pdf.php?pdfdoc=lettre_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr;
                            $popUpLabel = $msg['lettre_retard'];
                            $mail_click = "onclick=\"if (confirm('".$msg["mail_retard_confirm"]."')) {openPopUp('./mail.php?type_mail=mail_retard&cb_doc=".$object->id_expl."&id_empr=".$object->id_empr."', 'mail'); } return(false) \"";
                            break;
                    }
                
                    $imprime_click = "onclick=\"openPopUp('".$popUpLink."', 'lettre'); return(false) \"";
                    $content .= "<a href=\"#\" ".$imprime_click."><img src='".get_url_icon('new.gif')."' title=\"".$popUpLabel."\" alt=\"".$popUpLabel."\" border=\"0\"></a>";
                    if ((emprunteur::get_mail_empr($object->id_empr))&&($biblio_email)) {
                        $content .= "<a href=\"#\" ".$mail_click."><img src='".get_url_icon('mail.png')."' title=\"".$msg['mail_retard']."\" alt=\"".$msg['mail_retard']."\" border=\"0\" /></a>";
                    }
                }
                break;
            default :
                $content .= parent::get_cell_content($object, $property);
                break;
        }
        return $content;
    }
}