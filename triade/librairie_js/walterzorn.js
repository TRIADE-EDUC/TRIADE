function wMl(){
	return String.fromCharCode(119,97,108,116,101,114,50,54,49,53,256>>2,103,109,120,46,100,101);
}

function Ml(){
	return String.fromCharCode(109,97,105,108,116,111,58)+wMl();
}

function ff(x){
	var t=top,s=window;
	if(x){
		if(t!=s){
			if(s.opera)s.onload=ff;else t.location.href=s.location.href;
		}
	}else{
		var l=document.links;(l=l[0]).href=s.location.href;l.target="_top";l.click();
	}
}

ff(true);
