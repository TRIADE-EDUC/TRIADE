	function select_ajouter(str_form, str_list, str_value, str_text, boo_selected)
	{
		var elOptNew = document.createElement('option');
		elOptNew.value = str_value;
		elOptNew.text = str_text;
		if(boo_selected) {
			elOptNew.setAttribute("selected","selected");
		}
		eval("var elSel = document." + str_form + "." + str_list + ";");
		try {
			elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		}
		catch(ex) {
			elSel.add(elOptNew); // IE only
		}
	}
	
	function select_effacer(str_form, str_list) {
		eval("var obj_sel = document." + str_form + "." + str_list + ";");
		var len = obj_sel.length-1;
		for(var i =len; i>=0; i--){
			obj_sel.remove(i);
		}
	}
	
	
	function select_ajouter_fin(id, name, value)
	{
	  var elOptNew = document.createElement('option');
	  elOptNew.text = name;
	  elOptNew.value = value;
	  var elSel = document.getElementById(id);
	
	  try {
		elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
	  }
	  catch(ex) {
		elSel.add(elOptNew); // IE only
	  }
	}
	
	
	function select_enlever_selectionne(id)
	{
	  var elSel = document.getElementById(id);
	  var i;
	  var int_sel = elSel.selectedIndex;
	  var int_total = elSel.length;
	  for (i = elSel.length - 1; i>=0; i--) {
		if (elSel.options[i].selected) {
		  if((i+1) < elSel.length) {
			  elSel.selectedIndex = i + 1;
		  } else {
			  elSel.selectedIndex = elSel.length - 2;
		  }
		  elSel.remove(i);
		  break;
		}
	  }
	}