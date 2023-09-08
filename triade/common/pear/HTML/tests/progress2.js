var isDom = document.getElementById?true:false;
var isIE  = document.all?true:false;
var isNS4 = document.layers?true:false;
var cellCount = 10;

function setprogress(pIdent, pValue, pString)
{
        if (isDom)
            prog = document.getElementById(pIdent+'installationProgress');
        if (isIE)
            prog = document.all[pIdent+'installationProgress'];
        if (isNS4)
            prog = document.layers[pIdent+'installationProgress'];
	if (prog != null) 
	    prog.innerHTML = pString;

        if (pValue == 0) {
	    for (i=0; i < cellCount; i++) {
                if (isDom)
                    document.getElementById(pIdent+'progressCell'+i+'A').style.visibility = "hidden";
                if (isIE)
                    document.all[pIdent+'progressCell'+i+'A'].style.visibility = "hidden";
                if (isNS4)
                    document.layers[pIdent+'progressCell'+i+'A'].style.visibility = "hidden";
            }
        }

	for (i=pValue-1; i >= 0; i--) {
            if (isDom)
                document.getElementById(pIdent+'progressCell'+i+'A').style.visibility = "visible";
            if (isIE)
                document.all[pIdent+'progressCell'+i+'A'].style.visibility = "visible";
            if (isNS4)
                document.layers[pIdent+'progressCell'+i+'A'].style.visibility = "visible";
        }
}
