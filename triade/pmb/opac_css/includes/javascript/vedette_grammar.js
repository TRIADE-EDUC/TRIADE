/* +-------------------------------------------------+
// | 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_grammar.js,v 1.2 2020/12/22 16:55:43 btafforeau Exp $ */

var vedetteTabalreadyParsed = [];

function getGrammarForm(grammarName) {

}

function manageVedetteTab(tab) {
	if (!tab.classList.contains('grammar_selected')) {
		var grammarDiv = document.querySelector('.vedette_composee_corp');
		if (grammarDiv) {		
			var instance_name = document.getElementById('grammar_instance_name').value;
			var property_name = document.getElementById('grammar_property_name').value;			
			var idTab = tab.getAttribute("id").replace("grammar_tab_", "");
			var name = document.getElementById("grammar_name_" + idTab);		
			var xhr = new XMLHttpRequest();
			xhr.open('GET', './ajax.php?module=autorites&categ=vedettes&action=get_grammar_form&name=' + name.value + '&property_name=' + property_name + '&instance_name=' + instance_name );
			xhr.send();
			addOverlay(grammarDiv);
			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4 && xhr.status === 200) {
					// retirer overlay
					removeOverlay(grammarDiv);
					updateTabs(tab);
					grammarDiv.innerHTML = xhr.responseText;
					preLoadScripts(grammarDiv);
					init_drag();
				}
			}
		}
	}
}

function updateTabs (tab) {
	document.querySelector('.grammar_selected').classList.remove('grammar_selected');
	tab.classList.add('grammar_selected');
}

function removeOverlay(node) {
	node.querySelector('.overlay').remove();
}

function addOverlay(node) {
	var loader = document.createElement('img');
	loader.setAttribute('src', './images/loader.gif');
	loader.setAttribute('class', 'loader');
	var overlay = document.createElement('div');
	overlay.setAttribute('class', 'overlay');
	overlay.appendChild(loader);
	node.appendChild(overlay);
}