<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simple_circ.class.php,v 1.9.4.1 2025/02/12 12:34:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/simple_circ/impr_etiquette.inc.php");

class simple_circ {
	protected $start_date;
	protected $end_date;
	protected $data;
	protected $data_tri_day;
	protected $circ_cb_list;

	public function __construct($start_date,$end_date,$circ_cb_list='') {

		if(!$start_date)$start_date=date("Y-m-d");
		if(!$end_date)$end_date=date("Y-m-d");
		$this->start_date=$start_date;
		$this->end_date=$end_date;

		$this->circ_cb_list=array();
		if(is_array($circ_cb_list)){
			foreach ($circ_cb_list as $circ_cb){
				$this->circ_cb_list[] = intval($circ_cb);
			}
		}elseif($circ_cb_list){
			$this->circ_cb_list[] = intval($circ_cb_list);
		}
		$this->fetch_data();
	}

	private function fetch_data() {
		if(!count($this->circ_cb_list))
			$req="select *, g.date_parution as date_sortie from serialcirc,abts_grille_abt as g, abts_abts, notices where serialcirc_simple=1 and num_serialcirc_abt=num_abt  and abt_id=num_abt and num_notice=notice_id
				and g.date_parution >= '".$this->start_date."' and g.date_parution <= '".$this->end_date."' order by g.date_parution";
		else
			$req="select * from serialcirc, abts_abts, notices where num_serialcirc_abt=abt_id  and num_notice=notice_id
			and abt_id in(".implode(",", $this->circ_cb_list).")";
		//print $req;
		$this->data=array();
		$this->data_tri_day=array();
		$data=array();
		$i=0;
		$resultat=pmb_mysql_query($req);
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){
				//printr($r);
				$data[$i]["abt_name"]=$r->abt_name;
				$data[$i]["abt_id"]=$r->abt_id;
				$data[$i]["tit1"]=$r->tit1;
				$data[$i]["date_parution"]=$r->date_sortie;
				$this->data_tri_day[$r->date_sortie][]=$i;
				$req_diff="select * from serialcirc_diff where num_serialcirc_diff_serialcirc=".$r->id_serialcirc." order by serialcirc_diff_order";
				//print $req_diff;
				$resultat_diff=pmb_mysql_query($req_diff);
				$count_diff=0;
				if (pmb_mysql_num_rows($resultat_diff)) {
					while($r_diff=pmb_mysql_fetch_object($resultat_diff)){
						//printr($r_diff);
						if($r_diff->serialcirc_diff_empr_type){
							// un groupe de lecteur avec un responsable
							$data[$i]["diff"][$count_diff]["is_group"]=$r_diff->serialcirc_diff_empr_type;
							$data[$i]["diff"][$count_diff]["group_name"]=$r_diff->serialcirc_diff_group_name;
							$req_group="select * from serialcirc_group where num_serialcirc_group_diff=".$r_diff->id_serialcirc_diff." order by serialcirc_group_order";
							$resultat_group=pmb_mysql_query($req_group);
							$count_group=0;
							if (pmb_mysql_num_rows($resultat_group)) {
								while($r_group=pmb_mysql_fetch_object($resultat_group)){
									//printr($r_group);
									$id_empr=$r_group->num_serialcirc_group_empr;
									$data[$i]["diff"][$count_diff]["group"][$count_group]=$this->empr_info($id_empr);
									$data[$i]["diff"][$count_diff]["group"][$count_group]["is_responsable"]=$r_group->serialcirc_group_responsable;
									$count_group++;
								}
							}
						}else{
							// un simple lecteur
							$id_empr=$r_diff->num_serialcirc_diff_empr;
							$data[$i]["diff"][$count_diff]=$this->empr_info($id_empr);
							$data[$i]["diff"][$count_diff]["is_group"]=0;
						}
						$count_diff++;
					}
				}
				$i++;
			}
		}
		$this->data=$data;
		//printr($data);
	}

	private function empr_info($id){
	    global $msg;

		$info=array();
	//	$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail, empr_statut from empr where id_empr=".$id;
		$requete = "SELECT e.*, c.libelle AS code1, s.libelle AS code2, es.statut_libelle AS empr_statut_libelle, allow_loan, allow_book, allow_opac, allow_dsi, allow_dsi_priv, allow_sugg, allow_prol, d.location_libelle as localisation, date_format(empr_date_adhesion, '".$msg["format_date"]."') as aff_empr_date_adhesion, date_format(empr_date_expiration, '".$msg["format_date"]."') as aff_empr_date_expiration,date_format(last_loan_date, '".$msg["format_date"]."') as aff_last_loan_date FROM empr e left join docs_location as d on e.empr_location=d.idlocation, empr_categ c, empr_codestat s, empr_statut es ";
		$requete .= " WHERE e.id_empr='".$id."' " ;
		$requete .= " AND c.id_categ_empr=e.empr_categ";
		$requete .= " AND s.idcode=e.empr_codestat";
		$requete .= " AND es.idstatut=e.empr_statut";
		$requete .= " LIMIT 1";
		$res_empr=pmb_mysql_query($requete);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom;
			$info['prenom'] = $empr->empr_prenom;
			$info['mail'] = $empr->empr_mail;
			$info['statut_libelle'] = $empr->empr_statut_libelle;
			$info['categ_libelle'] = $empr->code1;
			$info['codestat_libelle'] = $empr->code2;
			$info['id_empr']=$id;
			$info['view_link']='./circ.php?categ=pret&form_cb='.$empr->empr_cb;
			$info['empr_libelle']=$info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
		}
		return $info;
	}

	public function get_data(){
		return $this->data;
	}

	public function get_display(){
		global $msg,$base_path;
		global $current_module;
		$simple_circ_form_tpl="
		<script>
		</script>
		<form class='form-$current_module' id='simple_circ_form' name='simple_circ_form' method='post' action=''>
			<h3>".$msg["serial_simple_circ_edit_title"]."</h3>
			<div class='form-contenu'>
				<div class='row'>
					<label class='etiquette' for='start_date'>".$msg["serial_simple_circ_edit_start_date"]."</label>
					<input type='date' name='start_date' id='start_date' value='!!start_date!!' />
					<label class='etiquette' for='end_date'>".$msg["serial_simple_circ_edit_end_date"]."</label>
					<input type='date' name='end_date' id='end_date' value='!!end_date!!' />

					<input type='button' value='".$msg["serial_simple_circ_edit_calculate"]."' class='bouton' onclick=\"this.form.setAttribute('action','');this.form.submit();\"   />
				</div>
				!!contents_to_print!!
				<div class='row'>
							".gen_plus("circ_edit_format",$msg["serial_simple_circ_edit_format"],aff_choix_quoi_impr_cote())."
				</div>
			</div>
			<div class='row'>
				<div class='left'>
					<input type='button' value='".$msg["serial_simple_circ_edit_print"]."' class='bouton' onclick=\"this.form.setAttribute('action','./edit/serials_simple_circ_suite.php');this.form.submit();\" />
				</div>
				<div class='right'>
				</div>
			</div>
			<div class='row'></div>
		</form>

		<script>
			function add_cb(){
				var circ_cb=document.getElementById('circ_cb').value;
				if(circ_cb=='')return;
				var url= './edit/serials_simple_circ_suite.php?action=add_circ_cb&circ_cb=' + circ_cb;

				var req = new http_request();
				if(req.request(url,1)){
					alert ( req.get_text() );
				} else {
					data=req.get_text();
				}
				if(!data.length){
					alert('".$msg["serial_simple_circ_edit_print_error"]."');
    				document.getElementById('circ_cb').value='';
					return;
				}
				var data = JSON.parse(data);

				data=data[0];

				var elmt=document.getElementById('cb_list');
				var tr = document.createElement('tr');
    			elmt.appendChild(tr);

    			var td = document.createElement('td');
    			tr.appendChild(td);
    			var tdText = document.createTextNode(data.tit1);
    			td.appendChild(tdText);

    			var td = document.createElement('td');
    			tr.appendChild(td);
    			var tdText = document.createTextNode(data.abt_name);
    			td.appendChild(tdText);

    			var td = document.createElement('td');
    			tr.appendChild(td);
    			var newButton = document.createElement('input');
				newButton.setAttribute('type','button');
	        	newButton.className='bouton';
				newButton.setAttribute('value','X');
	        	newButton.onclick= function (){raz_line(this);};
    			td.appendChild(newButton);

				var input = document.createElement('input');
				input.setAttribute('type','hidden');
				input.setAttribute('name','abt_cb[]');
				input.setAttribute('value',circ_cb);
    			td.appendChild(input);
    			document.getElementById('circ_cb').value='';

			}

			function raz_all(){
				var elmt = document.getElementById('cb_list');
				var all_tr = elmt.getElementsByTagName('tr');
				var nb=all_tr.length;
				for(var i=0;i<nb;i++){
					if(i){
						elmt.removeChild(all_tr[nb-i]);
					}
				}
			}

			function raz_line(e){
				var elmt = document.getElementById('cb_list');
				var td=e.parentNode;
				elmt.removeChild(td.parentNode);
			}

			function testForEnter(event){
				if (event.keyCode == 13){
					event.cancelBubble = true;
					event.returnValue = false;
					add_cb();
					return false;
			    }
			}

		</script>
		<form class='form-$current_module' id='simple_circ_form_list' name='simple_circ_form_list' method='post' action=''>
			<h3>".$msg["serial_simple_circ_edit_list_title"]."</h3>
			<div class='form-contenu'>
				<div class='row'>
					<label class='etiquette' for='circ_cb'>".$msg["serial_simple_circ_edit_cb"]."</label>
				</div>
				<div class='row'>
					<input type='text' id='circ_cb' name='circ_cb' value='' onkeydown=\"return testForEnter(event);\" />
					<input type='button' value='".$msg["serial_simple_circ_edit_list_add"]."' class='bouton' onclick=\"add_cb();\" />
				</div>
				<div class='row'>
					<table class='sortable' style='width:100%' id='cb_list' role='presentation'>
						<tr>
							<th>".$msg["serial_simple_circ_edit_list_table_perio"]."</th>
							<th>".$msg["serial_simple_circ_edit_list_table_abt"]."</th>
							<th></th>
						</tr>
					</table>
				</div>
			</div>
			<div class='row'>
				<div class='left'>
					<input type='button' value='".$msg["serial_simple_circ_edit_print"]."' class='bouton' onclick=\"this.form.setAttribute('action','./edit/serials_simple_circ_suite.php?action=print_list');this.form.submit();\" />
				</div>
				<div class='right'>
					<input type='button' value='".$msg["serial_simple_circ_edit_raz"]."' class='bouton' onclick=\"raz_all();document.getElementById('circ_cb').focus();\" />
				</div>
			</div>
			<div class='row'></div>
		</form>
		";

		$simple_circ_day_tpl="
			<h3>!!date_sortie!!</h3>
			<div class='row'>
				!!abt_list!!
			</div>";

		$simple_circ_abt_tpl="
			<div class='row'>
				<label class='etiquette'>!!tit1!!</label><br />
				<label class='etiquette'>!!abt_name!!</label>
				!!diff_list!!
			</div>";

		$simple_circ_diff_group_tpl="
			<br />!!name!!<br />
			";

		$display="";
		$display_day_list="";
		/* plus demandé ...
		foreach ($this->data_tri_day as $date_sortie => $index_list){
			$day_tpl=$simple_circ_day_tpl;
			$day_tpl=str_replace("!!date_sortie!!", formatDate($date_sortie), $day_tpl);
			$display_abt_list="";
			foreach ($index_list as $index){
				$abt=$this->data[$index];
				//printr($abt);
				$abt_tpl=$simple_circ_abt_tpl;
				$abt_tpl=str_replace("!!tit1!!", $abt["tit1"], $abt_tpl);
				$abt_tpl=str_replace("!!abt_name!!", $abt["abt_name"], $abt_tpl);
				$abt_tpl=str_replace("!!date_sortie!!", formatDate($date_sortie), $abt_tpl);
				$display_diff_list="";
				foreach($abt["diff"] as $diff){
					if($diff["is_group"]){
						$diff_group_tpl=$simple_circ_diff_group_tpl;
						$diff_group_tpl=str_replace("!!name!!", $diff["group_name"], $diff_group_tpl);

						$display_diff_list.=$diff_group_tpl;
					}else{

					}

				}
				$abt_tpl=str_replace("!!diff_list!!", $display_diff_list, $abt_tpl);
				$display_abt_list.=$abt_tpl;
			}
			$day_tpl=str_replace("!!abt_list!!", $display_abt_list, $day_tpl);

			$display_day_list.=$day_tpl;
		}
		*/
		$display_day_list="<label class='etiquette'>".$msg["serial_simple_circ_number"]." ".count($this->data)."</label>";
		$form_tpl=$simple_circ_form_tpl;
		$form_tpl=str_replace("!!contents_to_print!!", $display_day_list, $form_tpl);

		$form_tpl=str_replace("!!day!!",  date("Ymd"), $form_tpl);
		$form_tpl=str_replace("!!start_date!!", $this->start_date, $form_tpl);
		$form_tpl=str_replace("!!form_start_date!!", formatDate($this->start_date), $form_tpl);

		$form_tpl=str_replace("!!end_date!!", $this->end_date, $form_tpl);
		$form_tpl=str_replace("!!form_end_date!!", formatDate($this->end_date), $form_tpl);


		$display.=$form_tpl;
		return $display;
	}

}// class end