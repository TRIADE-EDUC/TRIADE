window.addEventListener('DOMContentLoaded', () => {
	const addField = document.getElementById('add_field');
	var options = addField ? addField.children : [];
	
	var input = document.getElementById('input_filter');
	if (input) {
		input.addEventListener('input', filter_select);
	}
	
	function filter_select(event) {
		let needle = event.target.value.toLowerCase();
		if (needle.length > 0){
			//initialisation
			var group = 1;
			var grpElements =0;
			
			for (var i=1; i<options.length; i++) {
				var label = options[i].innerHTML.toLowerCase();
				var classname = options[i].className;
				options[i].style.display = 'inline';
				
				if (classname == 'select-group') {
					//efface le nom du groupe s'il est vide
					if (i!=group){
						if(grpElements<1){
							options[group].style.display= 'none';
						} else {
							options[group].style.display= 'inline';
						}
						grpElements=0;
					}
					//met a jour la position du groupe actuel
					group = i;
					
				} else if (classname == 'select-option') {
					//affiche ou non l'option
					if (label.indexOf(needle)<0) {
						options[i].style.display= 'none';
					} else{
						options[i].style.display = 'inline';
						grpElements +=1;
					}
				}
				if (i == options.length-1 && grpElements == 0){
					//cas pour le dernier groupe s'il ne possède pas d'élément 
					options[group].style.display= 'none';
				} else {
					options[group].style.display= 'inline';
				}
			}
		}
	}
});