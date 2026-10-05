<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tu_notice.class.php,v 1.52 2023/05/04 14:15:47 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path."/indexation_stack.class.php");
class tu_notice {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	public $id;		// MySQL id notice
	public $ntu_data;	//données des titres uniformes lié a la notice 
	public $ntu_form;
	public $oeuvre_events_order;
	public $recursif;
	
	// ---------------------------------------------------------------
	//		tu_notice($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0,$recursif=0) {
		$this->id = intval($id);
		if($this->id) {
			// on cherche à atteindre une notice existante
			$this->recursif=intval($recursif);
		}
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos auteur
	// ---------------------------------------------------------------
	public function getData() {
		$this->ntu_data=array();
		$this->oeuvre_events_order=array();
		if($this->id) {				
			$requete = "SELECT * FROM notices_titres_uniformes WHERE ntu_num_notice=$this->id order by ntu_ordre";
			$result = pmb_mysql_query($requete);
			$nb_result=0;
			if(pmb_mysql_num_rows($result)) {
				while(($res_tu = pmb_mysql_fetch_object($result))) {
					$this->ntu_data[$nb_result] = new stdClass();
					$this->ntu_data[$nb_result]->num_tu=	$res_tu->ntu_num_tu;
					$this->ntu_data[$nb_result]->titre=	$res_tu->ntu_titre;
					$this->ntu_data[$nb_result]->date=	$res_tu->ntu_date;
					$this->ntu_data[$nb_result]->sous_vedette=	$res_tu->ntu_sous_vedette;
					$this->ntu_data[$nb_result]->langue=	$res_tu->ntu_langue;
					$this->ntu_data[$nb_result]->version=	$res_tu->ntu_version;
					$this->ntu_data[$nb_result]->mention=	$res_tu->ntu_mention;  
					$authority = new authority(0, $this->ntu_data[$nb_result]->num_tu, AUT_TABLE_TITRES_UNIFORMES);
					$this->ntu_data[$nb_result]->tu = $authority;
					/*  Champs récupérés du titre uniforme:
					 	name 			
						tonalite
						comment
						distrib (array)
						ref (array)
						subdiv (array)
					*/
					// mémorisation de l'ordre des évenemments par leur date, si renseigné.
					if(isset($this->ntu_data[$nb_result]->tu->get_object_instance()->get_oeuvre_events()[0]) && isset($this->ntu_data[$nb_result]->tu->get_object_instance()->get_oeuvre_events()[0]['date']) && $this->ntu_data[$nb_result]->tu->get_object_instance()->get_oeuvre_events()[0]['date']){
						$this->oeuvre_events_order[$this->ntu_data[$nb_result]->tu->get_object_instance()->get_oeuvre_events()[0]['date']][]=$nb_result;
					}
					$nb_result++;
				}
				krsort($this->oeuvre_events_order);
				uasort($this->ntu_data, array(self::class, 'sort_tu'));				
			} else {
				// pas trouvé avec cette clé
			}
		}
	}

	public function get_print_type() {
		global $msg;
		
		if(!$this->ntu_data) return'';
		$display="<b>".$msg["catal_onglet_titre_uniforme"]."</b>&nbsp;:";
		$flag_first=0;
		$printed=array();
		foreach ($this->oeuvre_events_order as $elts){
			foreach ($elts as $elt){
				if($flag_first)$display.="<br />";
				$flag_first=1;
				$display.=" ".$this->get_link($this->ntu_data[$elt]);
				$printed[]=$elt;
			}
		}
		for($elt=0; $elt<count($this->ntu_data); $elt++) {
			if(in_array($elt, $printed)) continue;
			if($flag_first)$display.="<br />";
			$flag_first=1;
			$display.=" ".$this->get_link($this->ntu_data[$elt]);
		}
		return $display;		
	}
	
	public function get_link($tu){
		return "<a href='./autorites.php?categ=see&sub=titre_uniforme&id=".$tu->num_tu."' class='lien_gestion'>".$tu->tu->get_isbd()."</a>";
	}
	
	public function get_form($form_name) {
		global $msg;

		$values = array();
		if(count($this->ntu_data)) {
			$i=0;
			do {
				$values[$i]["id"]= $this->ntu_data[$i]->num_tu;
				$values[$i]["label"]= $this->ntu_data[$i]->tu->get_isbd();
				$j=0;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_titre_section"];
				$values[$i]["objets"][$j]["name"]="ntu_titre";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->titre;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_date"];
				$values[$i]["objets"][$j]["name"]="ntu_date";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->date;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_sous_vedette"];
				$values[$i]["objets"][$j]["name"]="ntu_sous_vedette";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->sous_vedette;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_langue"];
				$values[$i]["objets"][$j]["name"]="ntu_langue";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->langue;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_version"];
				$values[$i]["objets"][$j]["name"]="ntu_version";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->version;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_mention"];
				$values[$i]["objets"][$j]["name"]="ntu_mention";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$this->ntu_data[$i]->mention;
			} while	(++$i<count($this->ntu_data));
		} else {
			$values = array(
					array(
							'id' => 0,
							'label' => '',
							'objets' => array(
									array(
											'label' => $msg["catal_titre_uniforme_titre_section"],
											'name' => "ntu_titre",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_date"],
											'name' => "ntu_date",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_sous_vedette"],
											'name' => "ntu_sous_vedette",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_langue"],
											'name' => "ntu_langue",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_version"],
											'name' => "ntu_version",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_mention"],
											'name' => "ntu_mention",
											'class' => "saisie-80em",
											'value' => ''
									),
							)
					)
			);
		}
		$this->ntu_form=static::gen_input_selection($msg["catal_onglet_titre_uniforme"],$form_name,"titre_uniforme",$values,"titre_uniforme","saisie-80emr");
		return $this->ntu_form;
	}
		
	public static function get_form_import($form_name,$ntu_data) {
		global $msg;

		$values = array();
		if(count($ntu_data)) {
			$i=0;
			do {	
				$values[$i]["id"]= $ntu_data[$i]->num_tu;
				$values[$i]["label"]= $ntu_data[$i]->tu->name;
				$j=0;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_titre_section"];
				$values[$i]["objets"][$j]["name"]="ntu_titre";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->titre;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_date"];
				$values[$i]["objets"][$j]["name"]="ntu_date";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->date;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_sous_vedette"];
				$values[$i]["objets"][$j]["name"]="ntu_sous_vedette";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->sous_vedette;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_langue"];
				$values[$i]["objets"][$j]["name"]="ntu_langue";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->langue;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_version"];
				$values[$i]["objets"][$j]["name"]="ntu_version";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->version;
				$j++;
				$values[$i]["objets"][$j]["label"]=$msg["catal_titre_uniforme_mention"];
				$values[$i]["objets"][$j]["name"]="ntu_mention";
				$values[$i]["objets"][$j]["class"]="saisie-80em";
				$values[$i]["objets"][$j]["value"]=$ntu_data[$i]->mention;
			} while	(++$i<count($ntu_data));
		} else {
			$values = array(
					array(
							'id' => 0,
							'label' => '',
							'objets' => array(
									array(
											'label' => $msg["catal_titre_uniforme_titre_section"],
											'name' => "ntu_titre",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_date"],
											'name' => "ntu_date",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_sous_vedette"],
											'name' => "ntu_sous_vedette",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_langue"],
											'name' => "ntu_langue",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_version"],
											'name' => "ntu_version",
											'class' => "saisie-80em",
											'value' => ''
									),
									array(
											'label' => $msg["catal_titre_uniforme_mention"],
											'name' => "ntu_mention",
											'class' => "saisie-80em",
											'value' => ''
									)
							)
					)
			);
		}
		$ntu_form=static::gen_input_selection($msg["catal_onglet_titre_uniforme"],$form_name,"titre_uniforme",$values,"titre_uniforme","saisie-80emr");
		return $ntu_form;
	}	
	
	public static function gen_input_selection($label,$form_name,$item,$values,$what_sel,$class='saisie-80em', $show_other_fields=1) {  
	
		global $msg, $charset;
// 		$select_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";
// 		$link="'./select.php?what=$what_sel&caller=$form_name&param1=f_".$item."_code!!num!!&param2=f_".$item."!!num!!&deb_rech='+".pmb_escape()."(this.form.f_".$item."!!num!!.value), '$what_sel', 400, 400, -2, -2, '$select_prop'";
		$size_item=strlen($item)+2;
		
		$show_other_fields_plus_js = '';
		$show_other_fields_plus = '';
		if(!$show_other_fields) {		    
		    $show_other_fields_plus_js = "img_" . $item . ".style.display='none';";
		    $show_other_fields_plus = "display:none;";
		}
		$script_js="
		<script>
		var memo_id='';
				
		function tu_add_callback(field,tu_id){
			if(typeof(formMapperCallback) != 'undefined'){
				//var tu_id = document.getElementById(document.getElementById(field).getAttribute('autfield')).value;
		          var tu_id = document.getElementById('f_titre_uniforme_code0').value;
				formMapperCallback(tu_id);
			}
		}		
				
		function fonction_selecteur_".$item."() {
			var nom='f_".$item."';
			if(memo_id) name=memo_id.substring(4);  
			else name=this.getAttribute('id').substring(4);
			memo_id='';	    
			var indice = name.substr(nom.length);
			name_id = name.substr(0,nom.length)+'_code'+indice;
			if(indice == 0){
			openPopUp('./select.php?what=$what_sel&caller=$form_name&param1='+name_id+'&param2='+name+'&callback=tu_add_callback&deb_rech='+document.getElementById(nom+indice).value, 'selector');	        
			}else{
			 openPopUp('./select.php?what=$what_sel&caller=$form_name&param1='+name_id+'&param2='+name+'&deb_rech='+document.getElementById(nom+indice).value, 'selector');
	        }	        
	    }
	    function fonction_raz_".$item."() {
	        name=this.getAttribute('id').substring(4);
			name_id = name.substr(0,$size_item)+'_code'+name.substr($size_item);
	        document.getElementById(name).value='';
			document.getElementById(name_id).value='';
	    }
	    function add_".$item."() {
	        template = document.getElementById('add".$item."');
	        suffixe=document.getElementById('max_".$item."').value;
	  
	        ".$item."=document.createElement('div');
	        ".$item.".className='parent';
	        ".$item.".setAttribute('id','tu'+suffixe);
	      	".$item.".style.display='block';
	      		      	
	      	img_".$item."= document.createElement('img');
			img_".$item.".setAttribute('src','".get_url_icon('plus.gif')."');  
			img_".$item.".setAttribute('class','img_plus');
			img_".$item.".setAttribute('name','imEx');
			img_".$item.".setAttribute('id','tu'+suffixe+'Img');
			img_".$item.".setAttribute('onclick',\"expandBase(this.id.substring(0,this.id.length - 3), true); return false;\");
			img_".$item.".setAttribute('border','0');	
            " . $show_other_fields_plus_js . "
	        
	        nom_id = 'f_".$item."'+suffixe;
	        f_".$item." = document.createElement('input');
	        f_".$item.".setAttribute('name',nom_id);
	        f_".$item.".setAttribute('id',nom_id);
	        f_".$item.".setAttribute('type','text');
	        f_".$item.".className='$class';
	        f_".$item.".setAttribute('value','');
	        //f_".$item.".setAttribute('callback','tu_add_callback');
			f_".$item.".setAttribute('completion','".$item."');
			f_".$item.".setAttribute('autfield','f_".$item."_code'+suffixe);
	        
			id = 'f_".$item."_code'+suffixe;
			f_".$item."_code = document.createElement('input');
			f_".$item."_code.setAttribute('name',id);
	        f_".$item."_code.setAttribute('id',id);
	        f_".$item."_code.setAttribute('type','hidden');
			f_".$item."_code.setAttribute('value','');
	 
	        del_f_".$item." = document.createElement('input');
	        del_f_".$item.".setAttribute('id','del_f_".$item."'+suffixe);
	        del_f_".$item.".onclick=fonction_raz_".$item.";
	        del_f_".$item.".setAttribute('type','button');
	        del_f_".$item.".className='bouton';
	        del_f_".$item.".setAttribute('readonly','');
	        del_f_".$item.".setAttribute('value','".$msg["raz"]."');
	
	        sel_f_".$item." = document.createElement('input');
	        sel_f_".$item.".setAttribute('id','sel_f_".$item."'+suffixe);
	        sel_f_".$item.".setAttribute('type','button');
	        sel_f_".$item.".className='bouton';
	        sel_f_".$item.".setAttribute('readonly','');
	        sel_f_".$item.".setAttribute('value','".$msg["parcourir"]."');
	        sel_f_".$item.".onclick=fonction_selecteur_".$item.";
	
	        ".$item.".appendChild(img_".$item.");
	        space=document.createTextNode(' ');
	        ".$item.".appendChild(space);
	        ".$item.".appendChild(f_".$item.");
			".$item.".appendChild(f_".$item."_code);
	        space=document.createTextNode(' ');
	        ".$item.".appendChild(space);
	        if('$what_sel')".$item.".appendChild(sel_f_".$item.");
	        ".$item.".appendChild(space.cloneNode(false));
	        ".$item.".appendChild(del_f_".$item.");	        
	        ".$item.".appendChild(document.getElementById('button_add_".$item."'));	        
	        template.appendChild(".$item.");
	        
	        child_".$item."= document.createElement('div');
	        child_".$item.".className='child';
	        child_".$item.".setAttribute('id','tu'+suffixe+'Child');
	        child_".$item.".setAttribute('etirable','yes');
	        child_".$item.".setAttribute('invert','');
	        child_".$item.".setAttribute('hide','');
	      	child_".$item.".style.display='none';
	      	template.appendChild(child_".$item.");
			//!!add_option!!	
			document.getElementById('max_".$item."').value=(suffixe*1)+(1*1) ;
	        ajax_pack_element(f_".$item.");	        
	    }
		</script>";
		$script_js_option="
			div_label_!!num!!=document.createElement('div');
			div_label_!!num!!.className='row';
			label_!!num!!=document.createElement('label');
			texte_!!num!!=document.createTextNode('!!label!!');
  			label_!!num!!.appendChild(texte_!!num!!);
			div_label_!!num!!.appendChild(label_!!num!!);
					
	        div_!!num!!=document.createElement('div');
	        div_!!num!!.className='row';
	        op_!!num!! = document.createElement('input');
	        op_!!num!!.setAttribute('name','!!name!!'+suffixe);
	        op_!!num!!.setAttribute('id','!!name!!'+suffixe);
	        op_!!num!!.setAttribute('type','text');
	        op_!!num!!.className='!!class!!';
	        op_!!num!!.setAttribute('value','');
	        div_!!num!!.appendChild(op_!!num!!);
	        
	    	child_".$item.".appendChild(div_label_!!num!!);
	    	child_".$item.".appendChild(div_!!num!!);
	    ";
		
		//template de zone de texte pour chaque valeur				
		$aff="
		<div style='display: block;' id='tu!!num!!Parent' class='parent'>
			<img src='".get_url_icon('plus.gif')."' class='img_plus' name='imEx' id='tu!!num!!Img' title='Zone des notes' onclick=\"expandBase('tu!!num!!', true); return false;\" style='border:0px;"
			    . $show_other_fields_plus . "'>
			<input type='text' data-form-name='f_".$item."' class='$class' id='f_".$item."!!num!!' name='f_".$item."!!num!!' value='!!label_element!!' autfield='f_".$item."_code!!num!!' completion=\"".$item."\" !!tu_callback!! />
			<input type='hidden' data-form-name='f_".$item."_code' id='f_".$item."_code!!num!!' name='f_".$item."_code!!num!!' value='!!id_element!!'>
			!!bouton_parcourir!!
			<input type='button' class='bouton' value='".$msg["raz"]."' onclick=\"this.form.f_".$item."!!num!!.value='';this.form.f_".$item."_code!!num!!.value='';\" />
			!!bouton_ajouter!!
		</div>		
		<div hide='' style='display: none;' invert='' id='tu!!num!!Child' class='child' title=''>	
		\n";
			
		$aff_option="
		<div class='row'>
			<label for='!!name!!!!num!!' class='etiquette'>!!label!!</label></div>
		<div class='row'>
			<input type='text' class='!!class!!' id='!!name!!!!num!!' name='!!name!!!!num!!' value=\"!!value!!\" data-form-name='!!name!!' />
		</div>";	

		if($what_sel)$bouton_parcourir="<input type='button' id='sel_f_".$item."!!num!!' class='bouton' value='".$msg["parcourir"]."' onclick=\"memo_id=this.getAttribute('id');fonction_selecteur_".$item."();\" />";
		else $bouton_parcourir="";
		$aff= str_replace('!!bouton_parcourir!!', $bouton_parcourir, $aff);	

		$template=$script_js."<div id='add".$item."' class='row'>";
		$template.="<div class='row'>
						<label for='f_".$item."' class='etiquette'>".$label."</label>
						<input class='bouton' value='+' onclick='add_$item();' type='button'>
					</div>";
		$num=0;
		if(!isset($values[0]) || !$values[0]) {
			$values[0]["label"] = "";
			$values[0]["id"]= 0;
			$values[0]["objets"] = array();
		}
		$last_id = end($values)['id'];
		foreach($values as $value) {
			$label_element=$value["label"];
			$id_element=$value["id"];
			$temp= str_replace('!!id_element!!', $id_element, $aff);	
			$temp= str_replace('!!label_element!!', htmlentities(html_entity_decode($label_element),ENT_QUOTES,$charset), $temp);	
			$temp= str_replace('!!num!!', $num, $temp);	
			
			if($id_element == $last_id){
			    $temp= str_replace('!!bouton_ajouter!!', "<input id='button_add_".$item."' class='bouton' value='".$msg["req_bt_add_line"]."' onclick='add_".$item."();' type='button'>", $temp);	
			    $temp= str_replace('!!tu_callback!!', 'callback="tu_add_callback"', $temp);
			}else{ 
			    $temp= str_replace('!!bouton_ajouter!!', "", $temp);
			    $temp= str_replace('!!tu_callback!!', '', $temp);
			}	
			$template.=$temp;
			// option
			if(is_array($value["objets"]))
			foreach($value["objets"] as $objet) {
				
				$option = str_replace('!!label!!', $objet["label"], $aff_option);		
				$option = str_replace('!!name!!', $objet["name"], $option);		
				$option = str_replace('!!class!!', $objet["class"], $option);		
				$option = str_replace('!!num!!', $num, $option);		
				$option = str_replace('!!value!!', htmlentities($objet["value"],ENT_QUOTES,$charset), $option);	
				$template.=$option;	
			}
			$template.="</div>";
			if(!$num) {				
				$script_option_js = '';
				$j=0;
				if(is_array($value["objets"]))
				foreach($value["objets"] as $objet) {
					// Ajout des javascript qui permet la répétabilité des champs option 			
					$option_js = str_replace('!!label!!', addslashes($objet["label"]), $script_js_option);		
					$option_js = str_replace('!!name!!', $objet["name"], $option_js);		
					$option_js = str_replace('!!class!!', $objet["class"], $option_js);		
					$option_js = str_replace('!!num!!', $j, $option_js);		
					$option_js = str_replace('!!value!!', $objet["value"], $option_js);
					$script_option_js.=$option_js; 
					$j++;	
				}				
				$template=str_replace('!!add_option!!',$script_option_js, $template);		
			}	
			$num++;
		}	
		$template.="<input type='hidden' name='max_".$item."' id='max_".$item."' value='$num'>";					
		$template.="</div><div id='add".$item."'/>
		</div>";
		return $template;		
	}
	
	// ---------------------------------------------------------------
	//		show_form : affichage du formulaire de saisie
	// ---------------------------------------------------------------
	public function show_form() {
	
		global $msg;
		global $titre_uniforme_form;
		global $charset;
		global $user_input, $nbr_lignes, $page ;
		
		if($this->id) {
			$action = "./autorites.php?categ=titres_uniformes&sub=update&id=$this->id";
			$libelle = $msg["aut_titre_uniforme_modifier"];
			$button_remplace = "<input type='button' class='bouton' value='$msg[158]' ";
			$button_remplace .= "onclick='unload_off();document.location=\"./autorites.php?categ=titres_uniformes&sub=replace&id=$this->id\"'>";
			
			$button_voir = "<input type='button' class='bouton' value='$msg[voir_notices_assoc]' ";
			$button_voir .= "onclick='unload_off();document.location=\"./catalog.php?categ=search&mode=0&etat=tu_search&tu_id=$this->id\"'>";
			
			$button_delete = "<input type='button' class='bouton' value='$msg[63]' ";
			$button_delete .= "onClick=\"confirm_delete();\">";
			
		} else {
			$action = './autorites.php?categ=titres_uniformes&sub=update&id=';
			$libelle = $msg["aut_titre_uniforme_ajouter"];
			$button_remplace = '';
			$button_delete ='';
		}
				
		$titre_uniforme_form = str_replace('!!id!!',				$this->id,		$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!action!!',			$action,		$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!libelle!!',			$libelle,		$titre_uniforme_form);
		
		$titre_uniforme_form = str_replace('!!nom!!',				htmlentities($this->name,ENT_QUOTES, $charset), $titre_uniforme_form);
				
		$distribution_form=static::gen_input_selection($msg["aut_titre_uniforme_form_distribution"],"saisie_titre_uniforme","distrib",$this->distrib,"","saisie-80em");
		$titre_uniforme_form = str_replace("<!--	Distribution instrumentale et vocale (pour la musique)	-->",$distribution_form, $titre_uniforme_form);

		$ref_num_form=static::gen_input_selection($msg["aut_titre_uniforme_form_ref_numerique"],"saisie_titre_uniforme","ref",$this->ref,"","saisie-80em");
		$titre_uniforme_form = str_replace("<!--	Référence numérique (pour la musique)	-->",$ref_num_form, $titre_uniforme_form);
		
		$titre_uniforme_form = str_replace('!!tonalite!!',			htmlentities($this->tonalite,ENT_QUOTES, $charset),	$titre_uniforme_form);				
		$titre_uniforme_form = str_replace('!!comment!!',			htmlentities($this->comment,ENT_QUOTES, $charset),	$titre_uniforme_form);

		$sub_form=static::gen_input_selection($msg["aut_titre_uniforme_form_subdivision_forme"],"saisie_titre_uniforme","subdiv",$this->subdiv,"","saisie-80em");
		$titre_uniforme_form = str_replace('<!-- Subdivision de forme -->',	$sub_form, $titre_uniforme_form);
		
		$titre_uniforme_form = str_replace('!!remplace!!',			$button_remplace,	$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!voir_notices!!',		$button_voir,		$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!delete!!',			$button_delete,		$titre_uniforme_form);
			
		$titre_uniforme_form = str_replace('!!user_input_url!!',	rawurlencode(stripslashes($user_input)),							$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!user_input!!',		htmlentities($user_input,ENT_QUOTES, $charset),						$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!nbr_lignes!!',		$nbr_lignes,														$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!page!!',				$page,																$titre_uniforme_form);
		$titre_uniforme_form = str_replace('!!controller_url_base!!', './autorites.php?categ=titres_uniformes', 						$titre_uniforme_form);
		print $titre_uniforme_form;
	}
	
	// ---------------------------------------------------------------
	//		replace_form : affichage du formulaire de remplacement
	// ---------------------------------------------------------------
	public function replace_form() {
		global $titre_uniforme_replace;
		global $msg;
		global $include_path;
	
		if(!$this->id || !$this->name) {
			require_once("$include_path/user_error.inc.php");
			error_message($msg[161], $msg[162], 1, './autorites.php?categ=titres_uniformes&sub=&id=');
			return false;
		}
		$titre_uniforme_replace=str_replace('!!old_titre_uniforme_libelle!!', $this->display, $titre_uniforme_replace);
		$titre_uniforme_replace=str_replace('!!id!!', $this->id, $titre_uniforme_replace);
		$titre_uniforme_replace=str_replace('!!controller_url_base!!', './autorites.php?categ=titres_uniformes', $titre_uniforme_replace);
		print $titre_uniforme_replace;
		return true;
	}
	
	
	// ---------------------------------------------------------------
	//		delete() : suppression 
	// ---------------------------------------------------------------
	public function delete() {
		if(!$this->id) return false;
		$requete = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice='$this->id' ";
		pmb_mysql_query($requete);
		$this->id=0;
		$this->ntu_data=array();
	}
	
	
	// ---------------------------------------------------------------
	//		replace($by) : remplacement 
	// ---------------------------------------------------------------
	public function replace($by) {
		global $msg;
	
		if (($this->id == $by) || (!$this->id))  {
			return $msg[223];
		}
//		titre_uniforme::update_index($by);
		return FALSE;
	}
	
	// ---------------------------------------------------------------
	//		update($value) : mise à jour 
	// ---------------------------------------------------------------
	public function update($values) {
		if(!$this->id) return false;
		$requete = "DELETE FROM notices_titres_uniformes WHERE ntu_num_notice=".$this->id;
		pmb_mysql_query($requete);
		if (!empty($values)) {
    		// nettoyage des chaînes en entrée		
    		$ordre=0;
    		foreach($values as $value) {			
    			if($value['num_tu']) {
    				$requete = "INSERT INTO notices_titres_uniformes SET 
    				ntu_num_notice='$this->id', 
    				ntu_num_tu='".$value['num_tu']."', 
    				ntu_titre='".clean_string($value['ntu_titre'])."', 
    				ntu_date='".clean_string($value['ntu_date'])."', 
    				ntu_sous_vedette='".clean_string($value['ntu_sous_vedette'])."', 
    				ntu_langue='".clean_string($value['ntu_langue'])."', 
    				ntu_version='".clean_string($value['ntu_version'])."', 
    				ntu_mention='".clean_string($value['ntu_mention'])."',
    				ntu_ordre=$ordre 				
    				";
    				pmb_mysql_query($requete);
    			}
    			$ordre++;
    		}
		}
		return TRUE;
	}
		
	// ---------------------------------------------------------------
	//		import() : import d'un titre_uniforme
	// ---------------------------------------------------------------
	// fonction d'import de notice titre_uniforme 
	public function import($data) {
	// To do
	
	}
		
	// ---------------------------------------------------------------
	//		search_form() : affichage du form de recherche
	// ---------------------------------------------------------------
	public function search_form() {
		global $user_query, $user_input;
		global $msg, $charset;
		
		$user_query = str_replace ('!!user_query_title!!', $msg[357]." : ".$msg["aut_menu_titre_uniforme"] , $user_query);
		$user_query = str_replace ('!!action!!', './autorites.php?categ=titres_uniformes&sub=reach&id=', $user_query);
		$user_query = str_replace ('!!add_auth_msg!!', $msg["aut_titre_uniforme_ajouter"] , $user_query);
		$user_query = str_replace ('!!add_auth_act!!', './autorites.php?categ=titres_uniformes&sub=titre_uniforme_form', $user_query);
		$user_query = str_replace ('<!-- lien_derniers -->', "<a href='./autorites.php?categ=titres_uniformes&sub=titre_uniforme_last'>".$msg["aut_titre_uniforme_derniers_crees"]."</a>", $user_query);
		$user_query = str_replace("!!user_input!!",htmlentities(stripslashes($user_input),ENT_QUOTES, $charset),$user_query);
			
		print pmb_bidi($user_query) ;
	}
	
	//---------------------------------------------------------------
	// update_index($id) : maj des n-uplets la table notice_global_index en rapport avec cet author	
	//---------------------------------------------------------------
	public static function update_index($id) {
		$id = intval($id);
		// On cherche tous les n-uplet de la table notice correspondant à ce titre_uniforme.
		$found = pmb_mysql_query("select ntu_num_notice from notices_titres_uniformes where ntu_num_tu = ".$id);
		// Pour chaque n-uplet trouvés on met a jour la table notice_global_index avec l'auteur modifié :
		while(($mesNotices = pmb_mysql_fetch_object($found))) {
			$notice_id = $mesNotices->ntu_num_notice;
			indexation_stack::push($notice_id, TYPE_NOTICE);
		}
	}
	
	public static function sort_tu($first_one,$second_one){
		$mc_oeuvre_nature = marc_list_collection::get_instance('oeuvre_nature');
		$nature_keys = array_keys($mc_oeuvre_nature->table);
		if(array_search($first_one->tu->get_object_instance()->oeuvre_nature, $nature_keys) == array_search($second_one->tu->get_object_instance()->oeuvre_nature, $nature_keys)){
			return 0;
		}  
		return (array_search($first_one->tu->get_object_instance()->oeuvre_nature, $nature_keys) < array_search($second_one->tu->get_object_instance()->oeuvre_nature, $nature_keys)) ? -1 : 1;
	} 
	
	public static function create_tu_notice_link($num_tu, $num_notice, $ordre = 0) {
		$num_tu = intval($num_tu);
		$num_notice = intval($num_notice);
		$ordre = intval($ordre);
		$query = "INSERT INTO notices_titres_uniformes SET
						ntu_num_notice='" . $num_notice . "',
						ntu_num_tu='" . $num_tu . "',
						ntu_ordre= ". $ordre;
		pmb_mysql_query($query);
	}
	
} // class auteur


