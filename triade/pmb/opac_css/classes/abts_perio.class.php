<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_perio.class.php,v 1.3 2017-02-08 13:41:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classes d'info sur bulletinage de périodique
		
class abts_perio {

	public $serial_id       = 0;         // id du périodique 
	
	// constructeur
	public function __construct($serial_id=0) {
		$this->serial_id = $serial_id+0;
		if($this->serial_id) {
			$this->fetch_data();
		}
	}
	    
	// récupération des infos en base
	public function fetch_data() {
		global $dbh;
		
		$req="SELECT surloc_num, location_id,location_libelle, rel_date_parution,rel_libelle_numero, rel_comment_opac 
			from perio_relance, abts_abts, docs_location
			where  location_id=idlocation and rel_abt_num=abt_id and num_notice=".$this->serial_id." and rel_comment_opac!='' group by rel_abt_num,rel_date_parution,rel_libelle_numero order by rel_nb desc";		
	
		$result = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($result)){
			$tr_class="";
			while($r = pmb_mysql_fetch_object($result)) {	
				$surloc_libelle="";
				if($opac_sur_location_activate && $r->surloc_num ){
					$req="select surloc_libelle from sur_location where surloc_id = ".$r->surloc_num;
					$res_surloc = pmb_mysql_query($req);
					if(pmb_mysql_num_rows($res_surloc)){
						$surloc= pmb_mysql_fetch_object($res_surloc);
						$surloc_libelle=$surloc->surloc_libelle." / ";
					}
				}			
				$line=$bulletin_retard_line;
				
				$line=str_replace("!!location_libelle!!", $surloc_libelle.$r->location_libelle , $line);	
				$line=str_replace("!!date_parution!!", $r->rel_date_parution, $line);	
				$line=str_replace("!!libelle_numero!!", $r->rel_libelle_numero, $line);		
				$line=str_replace("!!comment_opac!!", $r->rel_comment_opac, $line);	
				if($tr_class=='even')$tr_class="odd"; else $tr_class='even';
				$line=str_replace("!!tr_class!!",$tr_class, $line);	
				$lines.=$line	;	
			}
			$tpl=$bulletin_retard_form;
			$tpl=gen_plus("bulletin_retard",$msg["bulletin_retard_title"],str_replace("!!bulletin_retard_list!!", $lines, $tpl));		
		}
	}

} // fin définition classe
