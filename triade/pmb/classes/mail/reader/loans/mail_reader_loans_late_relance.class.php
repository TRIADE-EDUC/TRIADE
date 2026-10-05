<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_late_relance.class.php,v 1.11.4.4 2024/12/04 11:06:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_loans_late_relance extends mail_reader_loans_late {
    
    protected static $hide_fines;
    
    protected $total_amendes = 0;
    
    protected function get_mail_expl_content($expl_cb) {
        global $msg, $current_module;
        
        $mail_expl_content = '';
        
        $expl = $this->get_expl_informations($expl_cb);
        
        $header_aut = "" ;
        $responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
        $header_aut = gen_authors_header($responsabilites);
        $header_aut ? $auteur=" / ".$header_aut : $auteur="";
        
        // récupération du titre de série
        $tit_serie="";
        if ($expl->tparent_id && $expl->m_id) {
            $parent = new serie($expl->tparent_id);
            $tit_serie = $parent->name;
            if ($expl->tnvol)
                $tit_serie .= ', '.$expl->tnvol;
        }
        if ($tit_serie) {
            $expl->tit = $tit_serie.'. '.$expl->tit;
        }
        
        $mail_expl_content.=$expl->tit.$auteur."\r\n";
        if($current_module == 'edit') {
            $mail_expl_content.="    - ".$msg['fpdf_date_pret']." ".$expl->aff_pret_date." ".$msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour."\r\n";
        } else {
            $mail_expl_content.="    - ".sprintf($msg["relance_mail_retard_dates"],$expl->aff_pret_date,$expl->aff_pret_retour)."\r\n";
        }
        $mail_expl_content.="    -".$expl->location_libelle.": ".$expl->section_libelle." (".$expl->expl_cb.")";
        return $mail_expl_content;
    }
    
    protected function get_fine_from_expl_data($data) {
        $fine=0;
        $amende=new amende($data["pret_idempr"]);
        $amd=$amende->get_amende($data["expl_id"]);
        if ($amd["valeur"]) {
            $fine=$amd["valeur"];
        }
        return $fine;
    }
    
    protected function get_data_from_cb_expl($cb_expl) {
        $query = "select expl_id from exemplaires where expl_cb='".$cb_expl."'";
        $result = pmb_mysql_query($query);
        return array(
            'expl_cb' => $cb_expl,
            'pret_idempr' => $this->mail_to_id,
            'expl_id' => pmb_mysql_result($result, '0', 'expl_id')
        );
    }
    
    protected function get_frais_relance($id_empr) {
        $frais_relance = 0;
        $id_compte=comptes::get_compte_id_from_empr($this->mail_to_id,2);
        if ($id_compte) {
            $cpte=new comptes($id_compte);
            $frais_relance=$cpte->summarize_transactions("","",0,-1);
            if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
        }
        return $frais_relance;
    }
    
    protected function get_mail_content_expl_list($liste_r, $level=0) {
        global $msg;
        global $mailretard_hide_fine;
        
        $mail_content = '';
        if (!empty($liste_r) ) {
            if ($level && static::$niveau_relance != $level) {
                $mail_content .= $this->get_mail_content_title_list($level);
            }
            foreach($liste_r as $cb_expl) {
                //Calcul des amendes
                $data = $this->get_data_from_cb_expl($cb_expl);
                $valeur=$this->get_fine_from_expl_data($data);
                $this->total_amendes+=$valeur;
                $mail_content .= $this->get_mail_expl_content($cb_expl);
                if ($valeur && !$mailretard_hide_fine && empty(static::$hide_fines)) {
                    $mail_content.=" ".sprintf($msg["relance_mail_retard_amende"],comptes::format_simple($valeur));
                }
                $mail_content.="\r\n\r\n";
            }
        }
        return $mail_content;
    }
    
    protected function get_mail_content() {
        
        global $msg;
        global $mailretard_hide_fine;
        
        $mail_content = '';
        
        //Récupération des exemplaires
        $expl_list = $this->get_expl_list($this->mail_to_id);
        $this->total_amendes=0;
        
        //Calcul des frais de relance
        $frais_relance = $this->get_frais_relance($this->mail_to_id);
        
        if($this->get_parameter_value('madame_monsieur')) {
            $mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
        }
        $mail_content .= $this->get_mail_content_title_list();
        if($this->get_parameter_value('before_list')) {
            $mail_content .= $this->get_parameter_value('before_list')."\r\n\r\n";
        }
        switch (static::$niveau_relance) {
            case 3 :
                $liste_r = $expl_list['r'];
                $liste_r1 = $expl_list['r1'];
                $liste_r2 = $expl_list['r2'];
                $liste_r3 = $expl_list['r3'];
                $level_order = intval($this->get_parameter_value('level_order'));
                switch ($level_order) {
                    case 0: // Niveau 1, 2 puis 3
                        //Affichage des retards niveau 1
                        $mail_content .= $this->get_mail_content_expl_list($liste_r1, 1);
                        //Affichage des retards niveau 2
                        $mail_content .= $this->get_mail_content_expl_list($liste_r2, 2);
                        
                        //Affichage des retards de niveau 3
                        if( !empty($liste_r3) ) {
                            //Texte avant liste des recouvrements
                            if($this->get_parameter_value('before_recouvrement')) {
                                $mail_content .= $this->get_parameter_value('before_recouvrement')."\r\n\r\n";
                            }
                            $mail_content .= $this->get_mail_content_expl_list($liste_r3, 3);
                            //Texte après liste des recouvrements si pas de retards niveau 1 ou 2
                            if( empty($liste_r) ) {
                                if($this->get_parameter_value('after_recouvrement')) {
                                    $mail_content .= $this->get_parameter_value('after_recouvrement')."\r\n\r\n";
                                }
                            }
                        }
                        break;
                    case 1: // Niveau 3, 2 puis 1
                        //Affichage des retards de niveau 3
                        if( !empty($liste_r3) ) {
                            //Texte avant liste des recouvrements
                            if($this->get_parameter_value('before_recouvrement')) {
                                $mail_content .= $this->get_parameter_value('before_recouvrement')."\r\n\r\n";
                            }
                            $mail_content .= $this->get_mail_content_expl_list($liste_r3, 3);
                            //Texte après liste des recouvrements si pas de retards niveau 1 ou 2
                            if( empty($liste_r) ) {
                                if($this->get_parameter_value('after_recouvrement')) {
                                    $mail_content .= $this->get_parameter_value('after_recouvrement')."\r\n\r\n";
                                }
                            }
                        }
                        //Affichage des retards niveau 2
                        $mail_content .= $this->get_mail_content_expl_list($liste_r2, 2);
                        //Affichage des retards niveau 1
                        $mail_content .= $this->get_mail_content_expl_list($liste_r1, 1);
                        break;
                }
                break;
            default :
                $displayed_title_level_1 = false;
                foreach ($expl_list as $data) {
                    if (empty($displayed_title_level_1) && static::$niveau_relance == 2 && ($data['niveau_relance'] == 0 || $data['niveau_relance'] == 1)) {
                        $mail_content .= $this->get_mail_content_title_list(1);
                        $displayed_title_level_1 = true;
                    }
                    //Calcul des amendes
                    $valeur=$this->get_fine_from_expl_data($data);
                    $this->total_amendes+=$valeur;
                    $mail_content .= $this->get_mail_expl_content($data['expl_cb']);
                    if ($valeur && !$mailretard_hide_fine && empty(static::$hide_fines)) {
                        $mail_content.=" ".sprintf($msg["relance_mail_retard_amende"],comptes::format_simple($valeur));
                    }
                    $mail_content.="\r\n\r\n";
                }
                break;
        }
        
        if (!$mailretard_hide_fine  && empty(static::$hide_fines)) {
            if ($this->total_amendes) {
                $mail_content.= sprintf($msg["relance_mail_retard_total_amendes"],comptes::format_simple($this->total_amendes))."\r\n";
            }
            if ($frais_relance) {
                $mail_content.= $msg["relance_lettre_retard_frais_relance"].comptes::format_simple($frais_relance)."\r\n";
            }
            if (($frais_relance)&&($this->total_amendes)) {
                $mail_content.= $msg["relance_lettre_retard_total_du"].comptes::format_simple($this->total_amendes+$frais_relance)."\r\n";
            }
            if (($frais_relance)||($this->total_amendes)) {
                $mail_content.= "\r\n";
            }
        }
        
        if($this->get_parameter_value('after_list')) {
            $mail_content .= $this->get_parameter_value('after_list')."\r\n\r\n";
        }
        if($this->get_parameter_value('fdp')) {
            $mail_content .= $this->get_parameter_value('fdp')."\r\n\r\n";
        }
        $mail_content .= $this->get_mail_bloc_adresse();
        
        $coords = $this->get_empr_coords();
        $mail_content=str_replace("!!empr_name!!", $coords->empr_nom,$mail_content);
        $mail_content=str_replace("!!empr_first_name!!", $coords->empr_prenom,$mail_content);
        
        return $mail_content;
    }
    
    public function send_mail() {
        //Tableau contenant le destinataire (emprunteur) ou les destinataires (tous les responsables de groupe dont l'emprunteur est membre)
        $to_nom = array();
        $to_mail = array();
        $to_lang = array();
        
        /* Récupération du nom, prénom et mail du lecteur concerné */
        $requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_lang, empr_cb from empr where id_empr=".$this->mail_to_id;
        $res=pmb_mysql_query($requete);
        $coords=pmb_mysql_fetch_object($res);
        $to_nom[0] = $coords->empr_prenom." ".$coords->empr_nom;
        $to_mail[0] = $coords->empr_mail;
        $to_lang[0] = $coords->empr_lang;
        
        //Si mail de rappel affecté au responsable du groupe : on envoie à tous les responsables des groupes (concernés par l'emprunteur)
        $requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$this->mail_to_id." and resp_groupe and mail_rappel";
        $res=pmb_mysql_query($requete);
        if(pmb_mysql_num_rows($res) > 0) {
            $qt_to = 0;
            while ($row = pmb_mysql_fetch_object($res)) {
                $requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_lang from empr where id_empr='".$row->resp_groupe."'";
                $result=pmb_mysql_query($requete);
                $coords_dest=pmb_mysql_fetch_object($result);
                $to_nom[$qt_to] = $coords_dest->empr_prenom." ".$coords_dest->empr_nom;
                $to_mail[$qt_to] = $coords_dest->empr_mail;
                $to_lang[$qt_to] = $coords_dest->empr_lang;
                $qt_to++;
            }
        }
        
        $flag_res = false;
        //On boucle si plusieurs destinataires
        foreach ($to_nom as $key=>$dummy_value) {
            $this->set_mail_to_name($dummy_value);
            $this->set_mail_to_mail($to_mail[$key]);
            $this->set_language($to_lang[$key]);
            if($this->mailpmb()){
                $flag_res = true;
            }
            $this->restaure_language();
        }
        //Il faut au moins un email bien envoyé pour retourner true.
        return $flag_res;
    }
    
    public static function set_hide_fines($hide_fines) {
        static::$hide_fines = $hide_fines;
    }
}