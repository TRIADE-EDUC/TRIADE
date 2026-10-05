<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animationMail.class.php,v 1.5 2024/04/11 08:26:23 dbellamy Exp $

use Pmb\Animations\Models\MailingAnimationModel;
use Pmb\Animations\Models\AnimationModel;
global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");

class animationMail extends scheduler_task {
	
	public function execution() {
		if (SESSrights & ANIMATION_AUTH) {
		    $report = '';
		    if($this->statut == scheduler_task::WAITING) {
		        $this->send_command(scheduler_task::RUNNING);
		    }
		    if($this->statut == scheduler_task::RUNNING) {
		        
                //Recuperer les id de toutes les animations
		        $animations = AnimationModel::getAllAnimations();
		        
		        //On recupere la liste des type de communication selectionner dans le planificateur de tache
		        $mailing_list_choice = [];
	            if ($this->params['mailing_list_choice']) {
	                $mailing_list_choice = $this->params['mailing_list_choice'];
	            }
	            
                //Boucler sur chaque animation
		        foreach ($animations as $anim) {
		            $r = MailingAnimationModel::computeMail($anim->id_animation, $mailing_list_choice);
                    $report .= $this->format_response($r);
                }
                if (empty($report)) {
                	$this->add_section_report($this->msg['animation_mailing_no_send']);
                } else {
                    $this->report[] = $report;
                }
                $this->update_progression(100);
		    }
		} else{
		    $this->add_rights_bad_user_report();
		}
	}
	
	private function format_response($response){
	    $html ='';
	    foreach ($response as $animation=>$contacts){
    	    $html .= "<tr>";
    	    $html .= "<th>$animation</th>"; 
    	    $html .= "</tr>";
    	    foreach ($contacts as $contact){
        	    $html .= "<tr>";
    	        $html .= "<td>".$contact['NAME']."</td>";
    	        $html .= "<td>".$contact['EMAIL']."</td>";
    	        $html .= "<td>".($contact['SUCCESS'] ? $this->msg['animation_mailing_send_success'] : $this->msg['animation_mailing_send_fail'])."</td>";
        	    $html .= "</tr>";
    	    }
	    }
	    return $html;
    	    
	}
}