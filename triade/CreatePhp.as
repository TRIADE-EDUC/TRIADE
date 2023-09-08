/*
Classe par k-ny
Url: blog.ka-studio.net
*/
import mx.utils.Delegate;
class CreatePhp {
	private var oLv:LoadVars;
	public var etat:String = "init";
	public var fini;
	public function ecrire(p1, p2, p3, p4, p5):Void {
		if (p2 == undefined) {
			p2 = "";
		} else if (p5 == undefined || p5 == "") {
			p5 = "write.php";
		}
		oLv = new LoadVars();
		oLv.onLoad = Delegate.create(this, setText);
		oLv.php_verif = "flash";
		oLv.php_var_nom = p1;
		oLv.php_var_string = p2;
		oLv.php_var_html = p3;
		oLv.php_var_dec = p4;
		oLv.sendAndLoad(p5, oLv, "POST");
	}
	private function setText(sText:Boolean):Void {
		etat = oLv.retour_php;
		fini(sText);
	}
}
