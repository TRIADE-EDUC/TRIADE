/*
                                     
  Welcome !                          
  This is Dinosaur Land !            
                                     
                                     
                ████████             
              ██        ██           
            ██▒▒▒▒        ██         
          ██▒▒▒▒▒▒      ▒▒▒▒██       
          ██▒▒▒▒▒▒      ▒▒▒▒██       
        ██  ▒▒▒▒        ▒▒▒▒▒▒██     
        ██                ▒▒▒▒██     
      ██▒▒      ▒▒▒▒▒▒          ██   
      ██      ▒▒▒▒▒▒▒▒▒▒        ██   
      ██      ▒▒▒▒▒▒▒▒▒▒    ▒▒▒▒██   
      ██▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒▒▒██   
        ██▒▒▒▒  ▒▒▒▒▒▒    ▒▒▒▒██     
        ██▒▒▒▒            ▒▒▒▒██     
          ██▒▒              ██       
            ████        ████         
                ████████             
                                     
                                     
  Easter Egg succesfully Loaded !    
                                     
*/
var easterEggEnabled = false;
var keys = new Array();
var canvas = null;
var context = null;
var size = 24;
		
function parseHTML (encodedStr) {
	var parser = new DOMParser();
	var dom = parser.parseFromString(encodedStr, "text/html");
	return dom.body.textContent.trim();
}

function shortcuts(e) {
	
	if (!e) {
		return false;
	}
	
	if (e.type === "keydown") {
		if (!keys.includes(e.keyCode)) keys.push(e.keyCode)
	} else {
		const index = keys.findIndex(key => key == e.keyCode);
		if (index != -1) {keys.splice(index, 1);}
	}
	
	if (match()) {
		e.preventDefault();
		EasterEgg();
	}
}

function match() {
	// Ctrl + Alt + P
	const needle = [17, 18, 80];
	
	if (keys.length != needle.length) {
		return false;
	}
	
	for (let index in keys) {
		if (!needle.includes(keys[index])) {
			return false;
		}
	}
	
	return true;
}

function EasterEgg() {
	if (easterEggEnabled) {
		window.document.body.style.setProperty("cursor", "");
		easterEggEnabled = false;
	} else {
		context.clearRect(0, 0, canvas.width, canvas.height);
		context.fillText(parseHTML("&#x2697;&#xFE0F"), size / 2, size / 2);
		var imgDataURL = canvas.toDataURL();
		
		var offset = (size / 2) + " 0";
		window.document.body.style.setProperty("cursor", "url(" + imgDataURL + ") " + offset + ", auto", "important");
		
		keys = [];
		easterEggEnabled = true;
	}
}

function init() {
	// Initialize event listener
	window.addEventListener("keydown", shortcuts);
	window.addEventListener("keyup", shortcuts);

	// Initialize canvas
	canvas = document.createElement("canvas");
	canvas.width = size;
	canvas.height = size;
	canvas.style.setProperty("display", "hidden");
	
	// Store context & set styling
	context = canvas.getContext("2d");
	context.font = size + "px sans-serif";
	context.textBaseline = "middle";
	context.textAlign = "center";
}

export default init;