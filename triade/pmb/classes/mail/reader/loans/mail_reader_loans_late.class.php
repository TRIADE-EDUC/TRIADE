<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_late.class.php,v 1.16.2.7 2024/12/04 11:06:08 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_loans_late extends mail_reader_loans {
    
    protected static $niveau_relance;
    
    protected $expl_list;
    
    protected $levels = [];
    
    protected static function get_parameter_prefix() {
        return "mailretard";
    }
    
    protected function get_parameter_value($name) {
        if(isset(static::$niveau_relance)) {
            $parameter_name = static::get_parameter_prefix().'_'.static::$niveau_relance.$name;
            if($this->is_exist_parameter($parameter_name)) {
                return $this->get_evaluated_parameter($parameter_name);
            }
        }
        $parameter_name = static::get_parameter_prefix().'_1'.$name;
        if($this->is_exist_parameter($parameter_name)) {
            return $this->get_evaluated_parameter($parameter_name);
        } else {
            return parent::get_parameter_value($name);
        }
    }
    
    protected function get_parameter_level_value($name, $level=0) {
        if($level) {
            $parameter_name = static::get_parameter_prefix().'_'.$level.$name;
            if($this->is_exist_parameter($parameter_name)) {
                return $this->get_evaluated_parameter($parameter_name);
            }
        }
        return '';
    }
    
    protected function _init_parameter_value($name, $value) {
        if(isset(static::$niveau_relance)) {
            $parameter_name = static::get_parameter_prefix().'_'.static::$niveau_relance.$name;
            global $$parameter_name;
            if(empty(${$parameter_name}) && ${$parameter_name} != "0") {
                ${$parameter_name} = $value;
            }
        } else {
            parent::_init_parameter_value($name, $value);
        }
    }
    
    protected function _init_default_parameters() {
        $this->_init_parameter_value('list_order', 'pret_date');
        parent::_init_default_parameters();
    }
    
    protected function get_mail_object() {
        global $charset;
        
        $mail_object = $this->get_parameter_value('objet');
        
        $coords = $this->get_empr_coords();
        $mail_object .= " : ".$coords->empr_prenom." ".mb_strtoupper($coords->empr_nom,$charset)." (".$coords->empr_cb.")";
        return $mail_object;
    }
    
    protected function get_query_list_order() {
        $list_order = array();
        if (!empty($this->get_parameter_value('title_list'))) {
            $list_order[] = 'niveau_relance DESC';
        }
        if (!empty($this->get_parameter_value('list_order'))) {
            $list_order[] = $this->get_parameter_value('list_order');
        }
        if (!empty($list_order)) {
            return "order by ".implode(', ', $list_order);
        }
        return '';
    }
    
    protected function get_query_list($id) {
    	$id = intval($id);
        return $this->get_query_list_base()." where pret_idempr='".$id."' and pret_retour < curdate() ".$this->get_query_list_order();
    }
    
    protected function get_expl_list($id_empr) {
        
        if(empty($this->expl_list[$id_empr])) {
            $expl_list = array();
            if( 3 != static::$niveau_relance ) {
                $rqt = $this->get_query_list($id_empr);
                $req = pmb_mysql_query($rqt);
                while ($data = pmb_mysql_fetch_array($req)) {
                    $expl_list[] = $data;
                    $this->levels[] = $data['niveau_relance'];
                }
            } else {
                $expl_list = array(
                    'r' => array(),
                    'r1' => array(),
                    'r2' => array(),
                    'r3' => array()
                );
                $requete="select expl_cb from exemplaires, pret where pret_idempr=$id_empr and pret_idexpl=expl_id and niveau_relance=3";
                $res_recouvre=pmb_mysql_query($requete);
                if (pmb_mysql_num_rows($res_recouvre)) {
                    while ($rrc=pmb_mysql_fetch_object($res_recouvre)) {
                        $expl_list['r3'][]=$rrc->expl_cb;
                    }
                    $this->levels[] = 3;
                }
                $rqt = $this->get_query_list($id_empr);
                $req = pmb_mysql_query($rqt);
                while ($data = pmb_mysql_fetch_object($req)) {
                    // Pas répéter les retard si déjà en niveau 3
                    if(isset($expl_list['r3'])){
                        if(in_array($data->expl_cb,$expl_list['r3'])===false){
                            $expl_list['r'][] = $data->expl_cb;
                            if($data->niveau_relance == 1) {
                                $expl_list['r1'][] = $data->expl_cb;
                            } else {
                                $expl_list['r2'][] = $data->expl_cb;
                            }
                            $this->levels[] = $data->niveau_relance;
                        }
                    }
                }
            }
            $this->expl_list[$id_empr] = $expl_list;
            $this->levels = array_unique($this->levels);
        }
        return $this->expl_list[$id_empr];
    }
    
    protected function get_mail_to_id() {
        $coords_dest = $this->get_resp_coords();
        return $coords_dest->id_empr;
    }
    
    protected function get_mail_to_name() {
        $coords_dest = $this->get_resp_coords();
        return $coords_dest->empr_prenom." ".$coords_dest->empr_nom;
    }
    
    protected function get_mail_to_mail() {
        $coords_dest = $this->get_resp_coords();
        return $coords_dest->empr_mail;
    }
    
    protected function get_mail_content_title_list($level=0) {
        $mail_content = '';
        if($level) {
            $mail_content .= $this->get_parameter_level_value('title_list', $level);
        } else {
            $mail_content .= $this->get_parameter_value('title_list');
        }
        if (!empty($mail_content) && count($this->levels) > 1) {
            $mail_content = "<b>".$mail_content."</b>\r\n";
            //Reprise du before_list en dessous le titre
            if ($level && $level != static::$niveau_relance) {
                $before_content = $this->get_parameter_level_value('before_list', $level);
                if ($before_content) {
                    $mail_content .= $before_content."\r\n";
                }
            }
            return $mail_content;
        }
        return '';
    }
    
    protected function get_mail_content_expl_list($liste_r, $level=0) {
        $mail_content = '';
        if (!empty($liste_r) ) {
            if ($level && static::$niveau_relance != $level) {
                $mail_content .= $this->get_mail_content_title_list($level);
            }
            foreach($liste_r as $cb_expl) {
                $mail_content .= $this->get_mail_expl_content($cb_expl);
            }
        }
        return $mail_content;
    }
    
    protected function get_mail_content() {
        
        $mail_content = '';
        
        //Récupération des exemplaires
        $expl_list = $this->get_expl_list($this->mail_to_id);
        
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
//                             if( empty($liste_r) ) {
                                if($this->get_parameter_value('after_recouvrement')) {
                                    $mail_content .= $this->get_parameter_value('after_recouvrement')."\r\n\r\n";
                                }
//                             }
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
                    $mail_content .= $this->get_mail_expl_content($data['expl_cb']);
                }
                break;
        }
        
        $mail_content .= "\r\n";
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
    
    protected function get_resp_coords() {
        //Si mail de rappel affecté au responsable du groupe
        $requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$this->mail_to_id." and resp_groupe and mail_rappel limit 1";
        $res=pmb_mysql_query($requete);
        /* Récupération du nom, prénom et mail du lecteur destinataire */
        if(pmb_mysql_num_rows($res) > 0) {
            $requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_lang from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
            $result=pmb_mysql_query($requete);
            $coords_dest=pmb_mysql_fetch_object($result);
        } else {
            $requete="select id_empr, empr_mail, empr_nom, empr_prenom, empr_lang from empr where id_empr=".$this->mail_to_id;
            $result=pmb_mysql_query($requete);
            $coords_dest=pmb_mysql_fetch_object($result);
        }
        return $coords_dest;
    }
    
    public function send_mail() {
        $sended = false;
        $coords_dest = $this->get_resp_coords();
        if($coords_dest->empr_mail) {
            $this->set_language($coords_dest->empr_lang);
            $sended = $this->mailpmb();
            $this->restaure_language();
            if ($sended) {
                echo $this->get_display_sent_succeed();
            } else {
                echo $this->get_display_sent_failed();
            }
        } else {
            echo $this->get_display_unknown_mail();
        }
        return $sended;
    }
    
    public static function set_niveau_relance($niveau_relance) {
        static::$niveau_relance = $niveau_relance;
    }
}