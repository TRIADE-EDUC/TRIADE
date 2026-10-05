<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_datatype_range_selector_ui.tpl.php,v 1.4 2022/11/21 10:40:46 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id;

$ontology_tpl['onto_ontopmb_datatype_range_selector_ui'] = '
<script type="text/javascript">
    //On utilise le selecteur de range pour synchroniser le range, le datatype et le marclist qui fonctionne ensemble dans cette ontologie !
    function initRangeDatatypeMarclist() {
        let rangeId =  "!!onto_row_id!!_select";
        let datatypeId = rangeId.replace("range_select","pmbdatatype_0_value");
        let marclistId = rangeId.replace("range_select","marclist_name_0_value");
        document.getElementById(rangeId).addEventListener("change",syncRangeDatatypeMarclist);
        document.getElementById(datatypeId).addEventListener("change",syncRangeDatatypeMarclist);
    }

    window.addEventListener("load", function() {
       initRangeDatatypeMarclist();
       syncRangeDatatypeMarclist();
    });
  
    function syncRangeDatatypeMarclist(evt){ 
        let rangeId =  "!!onto_row_id!!_select";
        let datatypeId = rangeId.replace("range_select","pmbdatatype_0_value");
        let marclistId = rangeId.replace("range_select","marclist_name_0_value");
        let marclistContainerId = rangeId.replace("range_select","marclist_name");
        let range= document.getElementById(rangeId);
        let datatype = document.getElementById(datatypeId);
        let marclist = document.getElementById(marclistId);
        let marclistContainer = document.getElementById(marclistContainerId);
        let allowedDatatype = {
            "http://www.w3.org/2000/01/rdf-schema#Literal" : [ 
                "http://www.pmbservices.fr/ontology#small_text",
                "http://www.pmbservices.fr/ontology#text",
		        "http://www.pmbservices.fr/ontology#date",
                "http://www.pmbservices.fr/ontology#small_text_card",
                "http://www.pmbservices.fr/ontology#url",
                "http://www.pmbservices.fr/ontology#small_text_link",
                "http://www.pmbservices.fr/ontology#file"
            ],
            "http://www.pmbservices.fr/ontology#record" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#author" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#category" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#publisher" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#collection" :["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#sub_collection" :["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#serie" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#work" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#indexint" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.w3.org/2004/02/skos/core#Concept" : ["http://www.pmbservices.fr/ontology#resource_pmb_selector"],
			"http://www.pmbservices.fr/ontology#marclist" : ["http://www.pmbservices.fr/ontology#marclist"],
        }
	   
        let allowedRange = {
            "http://www.pmbservices.fr/ontology#small_text" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
            "http://www.pmbservices.fr/ontology#text" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
	        "http://www.pmbservices.fr/ontology#date" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
            "http://www.pmbservices.fr/ontology#small_text_card" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
            "http://www.pmbservices.fr/ontology#url" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
            "http://www.pmbservices.fr/ontology#small_text_link" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
            "http://www.pmbservices.fr/ontology#file" : ["http://www.w3.org/2000/01/rdf-schema#Literal"],
			"http://www.pmbservices.fr/ontology#marclist" : ["http://www.pmbservices.fr/ontology#marclist"],
            "http://www.pmbservices.fr/ontology#resource_pmb_selector" : [
                "http://www.pmbservices.fr/ontology#record",
    			"http://www.pmbservices.fr/ontology#author",
    			"http://www.pmbservices.fr/ontology#category",
    			"http://www.pmbservices.fr/ontology#publisher",
    			"http://www.pmbservices.fr/ontology#collection",
    			"http://www.pmbservices.fr/ontology#sub_collection",
    			"http://www.pmbservices.fr/ontology#serie",
    			"http://www.pmbservices.fr/ontology#work",
    			"http://www.pmbservices.fr/ontology#indexint",
    			"http://www.w3.org/2004/02/skos/core#Concept",
            ]
        };
		let from = datatypeId;
        if(typeof(evt) != "undefined"){       
            from = evt.target.id;
        }
        switch(from){
            case rangeId :
                //On aligne le datatype
                if(typeof(allowedDatatype[range.value]) == "undefined"){
                     datatype.value = "http://www.pmbservices.fr/ontology#resource_selector";
                    break;
                }
                if(allowedDatatype[range.value].indexOf(datatype.value) == -1){
                    datatype.value = allowedDatatype[range.value][0];
                }
                break;
            case datatypeId : 
                //On aligne le range
                if(typeof(allowedRange[datatype.value]) == "undefined"){
                    for(let=0 ; i<range.options.length ; i++){
                        if(typeof(allowedRange[range.options.item(i).value]) == "undefined"){
                            range.value = range.options.item(i).value;
                            break;
                        }
                    }
                    break;
                } 
                if(allowedRange[datatype.value].indexOf(range.value) == -1){
                    range.value = allowedRange[datatype.value][0];
                }
                break;    
        }
        //On active/desactive le champ marclist en fonction du range...
        if(range.value == "http://www.pmbservices.fr/ontology#marclist" ){
            marclist.disabled = false;
            marclistContainer.style.display = "block";
        }else{
            marclist.disabled = true;
            marclistContainer.style.display = "none";
        }
    }
</script>
<select id="!!onto_row_id!!_select" name="!!onto_row_id!!_select" multiple="yes">
	!!options!!
</select>
<input type="hidden" value="http://pmbservices.fr/ontology_description#Class" name="!!onto_row_id!![0][type]" id="!!onto_row_id!!_0_type"/>
<div id="!!onto_row_id!!_values"></div>';