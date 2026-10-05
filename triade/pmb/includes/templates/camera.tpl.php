<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: camera.tpl.php,v 1.5 2023/02/01 16:03:22 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $camera_tpl, $msg, $photo_tpl;

$camera_tpl='
	<div class="camera">
	    <video id="video">Video stream not available.</video>
	    <input type="button" class="bouton" id="startbutton" value="'.$msg['camera_photo_capture'].'" /> 
	</div>
	<canvas id="canvas"></canvas>
	<input type="button" class="bouton" id="savebutton" value="'.$msg['camera_photo_upload'].'" />
	<span id="uploaded" ></span>
	
<script>
(function() {		
	var width = 320;    // We will scale the photo width to this
	var height = 0;     // This will be computed based on the input stream

 	// |streaming| indicates whether or not were currently streaming
	// video from the camera. Obviously, we start at false.
	var streaming = false;

	// The various HTML elements we need to configure or control. These
	// will be set by the startup() function.
	var video = null;
	var canvas = null;
	var startbutton = null;
	var savebutton = null;

	function startup() {
		video = document.getElementById("video");
	    canvas = document.getElementById("canvas");
	    startbutton = document.getElementById("startbutton");
	    savebutton = document.getElementById("savebutton");
	
        let response = navigator.mediaDevices.getUserMedia({
            audio: false,
            video: true
        });

        response.then(function (stream) {
            video.srcObject = stream;
            video.play();
        });
        response.catch(function (err) {
	        console.log("An error occured! " + err);
        });

		video.addEventListener("canplay", function(ev){
			if (!streaming) {
				height = video.videoHeight / (video.videoWidth/width);
      
				// Firefox currently has a bug where the height can"t be read from
				// the video, so we will make assumptions if this happens.
      
				if (isNaN(height)) {
					height = width / (4/3);
				}
      
				video.setAttribute("width", width);
				video.setAttribute("height", height);
				canvas.setAttribute("width", width);
				canvas.setAttribute("height", height);
				streaming = true;
			}
		}, false);

		startbutton.addEventListener("click", function(ev){
			takepicture();
			ev.preventDefault();
		}, false);
    
		savebutton.addEventListener("click", function(ev){
			savepicture();
			ev.preventDefault();
		}, false);
    
		clearphoto();
	}

	// Fill the photo with an indication that none has been
	// captured.

	function clearphoto() {
		var context = canvas.getContext("2d");
		context.fillStyle = "#AAA";
		context.fillRect(0, 0, canvas.width, canvas.height);
	}
	
	// Capture a photo by fetching the current contents of the video
	// and drawing it into a canvas, then converting that to a PNG
	// format data URL. By drawing it on an offscreen canvas and then
	// drawing that to the screen, we can change its size and/or apply
	// other changes before drawing it.

	function takepicture() {
		var context = canvas.getContext("2d");
		if (width && height) {
			canvas.width = width;
			canvas.height = height;
			context.drawImage(video, 0, 0, width, height);

		} else {
			clearphoto();
		}
	}

	function savepicture() {
		var dataUrl = canvas.toDataURL("image/jpeg", 0.85);
		dataUrl=encodeURIComponent(dataUrl);

	 	var xhr_object = new http_request();
		xhr_object.request("./camera_upload.php",true,"imgBase64=" + dataUrl + "&id_empr=" + id_empr, true, cback, 0, 0);
	}

	function cback(response){
		var response = JSON.parse(response);
		if(parseInt(response.status)) {
			document.getElementById("uploaded").innerHTML = "'.$msg['camera_photo_success_uploaded'].'";
		} else {
			document.getElementById("uploaded").innerHTML = "'.$msg['camera_photo_fail_uploaded'].' "+response.message;
		}
	}

	window.addEventListener("load_camera", startup, false);

})();


function init_camera(idEmpr) {
	var event = new Event("load_camera");
	window.dispatchEvent(event);

	id_empr = idEmpr;
}

id_empr = 0;

</script>

';

$photo_tpl='
<div id="preview-row">
	<div id="drop-target" >'.$msg['camera_photo_drop_file'].'</div>
	<div id="preview" style="width: 166px; height: 100px;">
		<canvas id="canvas" width="166" height="100"></canvas>
	</div>
</div>
<input type="button" class="bouton" id="savebutton" value="'.$msg['camera_photo_upload'].'"/>
<span id="uploaded"></span>
		
<script>
require(["dojo/dom", "dojo/domReady!"], function(dom){
	var MAX_HEIGHT = 100;
	var target = dom.byId("drop-target"),
	preview = dom.byId("preview"),
	canvas = dom.byId("canvas");
	savebutton = dom.byId("savebutton");

	var render = function(src){
		var img = new Image();
		img.onload = function(){
			if(img.height > MAX_HEIGHT) {
				img.width *= MAX_HEIGHT / img.height;
				img.height = MAX_HEIGHT;
			}
			var ctx = canvas.getContext("2d");
			ctx.clearRect(0, 0, canvas.width, canvas.height);
			preview.style.width = img.width + "px";
			preview.style.height = img.height + "px";
			canvas.width = img.width;
			canvas.height = img.height;
			ctx.drawImage(img, 0, 0, img.width, img.height);
		};
		img.src = src;
	};

	savebutton.addEventListener("click", function(ev){
			savepicture();
			ev.preventDefault();
	}, false);
    
	var readImage = function(imgFile){
		if(!imgFile.type.match(/image.*/)){
			console.log("The dropped file is not an image: ", imgFile.type);
			return;
		}

		var reader = new FileReader();
		reader.onload = function(e){
			render(e.target.result);
		};
		reader.readAsDataURL(imgFile);
	};

	//	DOMReady setup
	target.addEventListener("dragover", function(e) {e.preventDefault();}, true);
	target.addEventListener("drop", function(e){
		e.preventDefault();
		readImage(e.dataTransfer.files[0]);
	}, true);
					
	function savepicture() {
		var dataUrl = canvas.toDataURL("image/jpeg", 0.85);
		var dataUrl_encode = encodeURIComponent(dataUrl);
	 	var xhr_object = new http_request();
		xhr_object.request("./camera_upload.php",true,"imgBase64=" + dataUrl_encode + "&id_empr=" + id_empr, true, cback, 0, 0);
	}
		
	function cback(response){
		var response = JSON.parse(response);
		if(parseInt(response.status)) {
			document.getElementById("uploaded").innerHTML = "'.$msg['camera_photo_success_uploaded'].'";
		} else {
			document.getElementById("uploaded").innerHTML = "'.$msg['camera_photo_fail_uploaded'].' "+response.message;
		}
	}
});
		
function init_camera(idEmpr) {
	id_empr = idEmpr;
}
			
id_empr = 0;
</script>
';		