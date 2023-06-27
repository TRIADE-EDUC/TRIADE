/*
--- fonction serialize et unserialize ---
auteur : XoraX
email : xxorax@gmail.com
info : http://www.xorax.info/blog/programmation/40-javascript-serialize-php.html
version : 1.2 - 2007/04/23

ChangeLog:
----------
1.2 : ajout du support pour la sérialization d'Object php (case "O") + maj de la page de test
1.1 : fix bug dans unserialize sur boolean 

Description:
------------
permet de décoder la chaine revoyé par la fonction serialize php.
ne prend pas (encore?) en compte les objects.
*/

function serialize (txt) {
	switch(typeof(txt)){
	case 'string':
		return 's:'+txt.length+':"'+txt+'";';
	case 'number':
		if(txt>=0 && String(txt).indexOf('.') == -1 && txt < 65536) return 'i:'+txt+';';
		return 'd:'+txt+';';
	case 'boolean':
		return 'b:'+( (txt)?'1':'0' )+';';
	case 'object':
		var i=0,k,ret='';
		for(k in txt){
			//alert(isNaN(k));
			if(!isNaN(k)) k = Number(k);
			ret += serialize(k)+serialize(txt[k]);
			i++;
		}
		return 'a:'+i+':{'+ret+'}';
	default:
		return 'N;';
		alert('var undefined: '+typeof(txt));return undefined;
	}
}

function unserialize(txt){
	var level=0,arrlen=new Array(),del=0,final=new Array(),key=new Array(),save=txt;
	while(1){
		switch(txt.substr(0,1)){
		case 'N':
			del = 2;
			ret = null;
		break;
		case 'b':
			del = txt.indexOf(';')+1;
			ret = (txt.substring(2,del-1) == '1')?true:false;
		break;
		case 'i':
			del = txt.indexOf(';')+1;
			ret = Number(txt.substring(2,del-1));
		break;
		case 'd':
			del = txt.indexOf(';')+1;
			ret = Number(txt.substring(2,del-1));
		break;
		case 's':
			del = txt.substr(2,txt.substr(2).indexOf(':'));
			ret = txt.substr( 1+txt.indexOf('"'),del);
			del = txt.indexOf('"')+ 1 + ret.length + 2;
		break;
		case 'a':
			del = txt.indexOf(':{')+2;
			ret = new Array();
			arrlen[level+1] = Number(txt.substring(txt.indexOf(':')+1, del-2))*2;
		break;
		case 'O':
			txt = txt.substr(2);
			var tmp = txt.indexOf(':"')+2;
			var nlen = Number(txt.substring(0, txt.indexOf(':')));
			name = txt.substring(tmp, tmp+nlen );
			//alert(name);
			txt = txt.substring(tmp+nlen+2);
			del = txt.indexOf(':{')+2;
			ret = new Object();
			arrlen[level+1] = Number(txt.substring(0, del-2))*2;
		break;
		case '}':
			txt = txt.substr(1);
			if(arrlen[level] != 0){alert('var missed : '+save); return undefined;};
			//alert(arrlen[level]);
			level--;
		continue;
		default:
			if(level==0) return final;
			alert('syntax invalid(1) : '+save+"\nat\n"+txt+"level is at "+level);
			return undefined;
		}
		if(arrlen[level]%2 == 0){
			if(typeof(ret) == 'object'){alert('array index object no accepted : '+save);return undefined;}
			if(ret == undefined){alert('syntax invalid(2) : '+save);return undefined;}
			key[level] = ret;
		} else {
			var ev = '';
			for(var i=1;i<=level;i++){
				if(typeof(key[i]) == 'number'){
					ev += '['+key[i]+']';
				}else{
					ev += '["'+key[i]+'"]';
				}
			}
			eval('final'+ev+'= ret;');
		}
		arrlen[level]--;//alert(arrlen[level]-1);
		if(typeof(ret) == 'object') level++;
		txt = txt.substr(del);
		continue;
	}
}
