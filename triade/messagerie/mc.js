var ap = window.navigator.appVersion.toLowerCase();
var i = ap.indexOf("msie");
if (ap.indexOf("windows") >= 0 && i >= 0 && ((i + 5) < ap.length)) {
  var v = ap.charAt(i + 5);
  if (v >= 5 ) {
    document.write('<scr' + 'ipt src="messagerie/mc1.js"></scr' + 'ipt>');
    document.write('<scr' + 'ipt src="messagerie/mc2.js"></scr' + 'ipt>');
  }
}
