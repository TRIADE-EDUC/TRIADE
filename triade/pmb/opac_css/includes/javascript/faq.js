// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: faq.js,v 1.2 2017-03-31 09:33:24 jpermanne Exp $

function faq_expand_collaspe(id){
	if(id){
		var parent = document.getElementById("parent_question_"+id);
		var answer = document.getElementById("child_question_"+id);
		if(answer){
			switch(answer.style.display){
				case "block" :
					answer.style.display = "none";
					parent.setAttribute('class','bg-grey');
					break;
				case "none" :
				default :
					answer.style.display = "block";
					parent.setAttribute('class','bg-grey question_expanded');
					break;
			}
			
		}
	}
}

function faq_collapse_all_questions(){
	var childs = document.getElementsByClassName("faq_child");
	for(var i=0 ; i<childs.length ; i++){
		childs[i].style.display = "none";
	}
	var parents = document.getElementsByClassName("bg-grey question_expanded");
	for(var i=0 ; i<parents.length ; i++){
		parents[i].setAttribute('class','bg-grey');
	}
}

function faq_expand_all_questions(){
	var childs = document.getElementsByClassName("faq_child");
	for(var i=0 ; i<childs.length ; i++){
		childs[i].style.display = "block";
	}
	var parents = document.getElementsByClassName("bg-grey");
	for(var i=0 ; i<parents.length ; i++){
		parents[i].setAttribute('class','bg-grey question_expanded');
	}
}