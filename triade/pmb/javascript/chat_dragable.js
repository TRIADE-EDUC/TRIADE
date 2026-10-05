function dragElement() {
	var elmnt;
	 setTimeout(function(){ 
		 console.log('time ok');
		 var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
		 if (document.getElementById("dragButton")) {
			 elmnt = document.getElementById("chatUsersListWindow");
			 // on bouge quand la souris tient le bouton de déplacement enfoncé
			 document.getElementById("dragButton").onmousedown = dragMouseDown;
		 }
	 }, 2000);
	 
	 function dragMouseDown() {
		 e = window.event;
		 e.preventDefault();
		 // récupérons la position acutelle du pointeur
		 pos3 = e.clientX;
		 pos4 = e.clientY;
		 console.log(pos3, pos4)
		 document.onmouseup = closeDragElement;
		 // on déplace l'élément a chaque fois que la souris se déplace
		 document.onmousemove = elementDrag;
	 }
	 
	 function elementDrag() {
		 e = window.event;
		 e.preventDefault();
		 // on calcule la nouvelle position
		 pos1 = pos3 - e.clientX;
		 pos2 = pos4 - e.clientY;
		 pos3 = e.clientX;
		 pos4 = e.clientY;
		 // on donne cette nouvelle position à l'élément dragable
		 elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
		 elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
	 }
	 
	 function closeDragElement() {
		 // on arrête l'execution quand le pointeur s'arrète
		 document.onmouseup = null;
		 document.onmousemove = null;
	 }
}

