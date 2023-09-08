
function calculer_cle_rib(banque, guichet, compte) {
	if (5 != banque.length || 5 != guichet.length || 11 != compte.length)
		return '';
	compte= parseInt(compte.toUpperCase().replace(/[A-Z]/g, finance_replace_alpha), 10);
  	return 97 - (((parseInt(banque, 10)% 97 * 100000 + parseFloat(guichet)) % 97 * 100000000000 + compte) % 97) * 100 % 97; 
} 

function finance_replace_alpha(alpha) {
	return '12345678912345678923456789'.charAt(alpha.charCodeAt(0) - 65);
}

