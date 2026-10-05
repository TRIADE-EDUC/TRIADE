<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_datatype_preflabel_card_ui.tpl.php,v 1.3 2018/11/26 10:04:59 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path;

$ontology_tpl['skos_concept_card_ui_wrapper']='

<script type="text/javascript">


	function toggle_form(){
	
		if(document.getElementById("skos_concept_card_ui_parent").style.display=="none"){
			document.getElementById("!!instance_name!!_!!property_name!!_is_composed").value = "";
			document.getElementById("skos_concept_card_ui_parent").style.display="block";
			document.getElementById("skos_concept_card_ui_derived").style.display="none";
			
			document.getElementById("skos_concept_card_ui_btn").value="'.$msg['skos_concept_card_ui_btn_derived'].'";
			
			var skos_concept_card_ui_derived = document.getElementById("skos_concept_card_ui_derived").children;
			
			for(var i=0 ; i<skos_concept_card_ui_derived.length; i++){
				for(var j=0 ; j<skos_concept_card_ui_derived[i].children.length; j++){
					for(var k=0 ; k<skos_concept_card_ui_derived[i].children[j].children.length; k++){	
						if(skos_concept_card_ui_derived[i].children[j].children[k] && skos_concept_card_ui_derived[i].children[j].children[k].nodeName=="INPUT"){
							skos_concept_card_ui_derived[i].children[j].children[k].disabled=true;
						}
					}	
				}
			}
			
			var skos_concept_card_ui_parent = document.getElementById("skos_concept_card_ui_parent").children;
			
			for(var i=0 ; i<skos_concept_card_ui_parent.length; i++){
				for(var j=0 ; j<skos_concept_card_ui_parent[i].children.length; j++){
					if(skos_concept_card_ui_parent[i].children[j] && skos_concept_card_ui_parent[i].children[j].nodeName=="INPUT"){
						skos_concept_card_ui_parent[i].children[j].disabled=false;
					}
				}
			}
			
		}else if(document.getElementById("skos_concept_card_ui_derived").style.display=="none"){
			document.getElementById("!!instance_name!!_!!property_name!!_is_composed").value = "composed";
			document.getElementById("skos_concept_card_ui_parent").style.display="none";
			document.getElementById("skos_concept_card_ui_derived").style.display="block";
			
			document.getElementById("skos_concept_card_ui_btn").value="'.$msg['skos_concept_card_ui_btn_parent'].'";
			
			var skos_concept_card_ui_derived = document.getElementById("skos_concept_card_ui_derived").children;
			
			for(var i=0 ; i<skos_concept_card_ui_derived.length; i++){
				for(var j=0 ; j<skos_concept_card_ui_derived[i].children.length; j++){
					for(var k=0 ; k<skos_concept_card_ui_derived[i].children[j].children.length; k++){
						if(skos_concept_card_ui_derived[i].children[j].children[k] && skos_concept_card_ui_derived[i].children[j].children[k].nodeName=="INPUT"){
							skos_concept_card_ui_derived[i].children[j].children[k].disabled=false;
						}
					}	
				}
			}
			
			var skos_concept_card_ui_parent = document.getElementById("skos_concept_card_ui_parent").children;
			
			for(var i=0 ; i<skos_concept_card_ui_parent.length; i++){
				for(var j=0 ; j<skos_concept_card_ui_parent[i].children.length; j++){
					if(skos_concept_card_ui_parent[i].children[j] && skos_concept_card_ui_parent[i].children[j].nodeName=="INPUT"){
						skos_concept_card_ui_parent[i].children[j].disabled=true;
					}
				}
			}
		}
		// Il faut reparser pour le dragndrop
		init_drag();
	}
</script>

<div class="row">
	<input type="button" id="skos_concept_card_ui_btn" value="!!skos_concept_card_ui_btn_value!!" onclick="toggle_form();" class="bouton">
</div>
<input type="hidden" id="!!instance_name!!_!!property_name!!_is_composed" name="!!instance_name!!_!!property_name!!_is_composed" value="!!is_composed!!"/>
<div !!skos_concept_card_ui_parent_visible!! id="skos_concept_card_ui_parent">
	!!skos_concept_card_ui_parent_form!!
</div>
<div !!skos_concept_card_ui_derived_visible!! id="skos_concept_card_ui_derived">
	!!skos_concept_card_ui_derived_form!!
</div>
';