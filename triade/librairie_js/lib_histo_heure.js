function AffHisto(tabx,taby,X,Y,titre) {
	// tabx : tableau des coordonnées
	// taby : tableau des abscisses
	// X et Y, environ taille en pixels de l'affichage de l'histo
	// titre, titre sous le graphe
	// unitx et unity , unité des axes x et y

	var nb=taby.length;
	if (nb!=tabx.length) alert("Problème de taille du tableau");
	var incX=Math.floor(X/nb);
	var max=GetMax(taby);
	var min=GetMin(taby);
	var incY=Y/max;
	var tailleY=0; var tailleX=0;
	disp("<TABLE  width="+X+" height="+Y+"border=1><TR>");
	for(var i=0;i<nb;i++)
	{	tailleY=Math.floor(taby[i]*incY);
		tailleX=Math.floor(incX);
		disp("<TD   width="+incX+" valign='bottom' border='1'>");
		disp("<img border=0 src='image/commun/histo.gif' width="+tailleX+" height="+tailleY+" alt='"+taby[i]+" hit(s)'>");
		disp("</TD>");
	}
	disp("</TR><tr valign='center'>");
	for(var i=0;i<nb;i++)
	{
		disp("<TD  valign='bottom' border='1'>");
		disp(tabx[i]+"h <br />"+taby[i]);
		disp("</TD>");

	}


	
	disp("</TR></TABLE>");
	disp("<TABLE width="+X+"><TR><TD>");
//	disp("<FONT SIZE='-1'> Entre "+tabx[0]+" et "+tabx[nb-1]+", Maximum = "+max+" et Minimum = "+min+", Moyenne = "+GetMoy(taby)+"</FONT><BR>");
	disp("<FONT SIZE='2' COLOR='#339966'><CENTER><B>"+titre+"</B></CENTER></FONT></TD></TR></TABLE>")
}
function GetMoy(tab) {
	var nb=tab.length;
	var moy=0;
	for(var i=0;i<nb;i++)
	{moy=moy+tab[i];}
	if (nb!=0) return Math.round(moy/nb);
}
function GetMax(tab) {
	var max=tab[0];
	var nb=tab.length;
	for(var i=0;i<nb;i++)
	{max=Math.max(max,tab[i]);}
	return max;
}
function GetMin(tab) {
	var min=tab[0];
	var nb=tab.length;
	for(var i=0;i<nb;i++)
	{min=Math.min(min,tab[i]);}
	return min;
}
function MakeTab() {
	this.length = MakeTab.arguments.length;
	for (var i = 0; i < this.length; i++)
	this[i] = MakeTab.arguments[i];
}

function disp(txt) { document.write(txt) }
