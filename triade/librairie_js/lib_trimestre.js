/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.org
 *
 ***************************************************************************/

function _trGet(sel) {
	// 1. sibling direct (les deux selects sont frères dans le même conteneur)
	var sib = sel.nextElementSibling;
	if (sib && sib.name === 'saisie_trimestre') return sib;
	// 2. querySelector dans le conteneur parent immédiat
	if (sel.parentNode) {
		var s = sel.parentNode.querySelector('[name="saisie_trimestre"]');
		if (s) return s;
	}
	// 3. fallback sel.form
	if (sel.form) {
		var f = sel.form.elements.namedItem('saisie_trimestre');
		if (f && f.tagName) return f;
	}
	return null;
}

function _trApply(sel, data) {
	var s = _trGet(sel);
	if (!s) return;
	for (var i = 0; i < s.options.length; i++) {
		s.options[i].text  = (data && data[i]) ? data[i][0] : '        ';
		s.options[i].value = (data && data[i]) ? data[i][1] : '0';
	}
	s.selectedIndex = 0;
}

// 9-option maps (trimes2 / trimes22)
var _T9 = {
	trimestre: [['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0']],
	cycle:     [['Cycle 1','cycle1'],['Cycle 2','cycle2'],['Cycle 3','cycle3'],['Cycle 4','cycle4'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0']],
	semestre:  [['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['        ','trimestre3'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0']],
	examen:    [['Examen Juin','exam_juin'],['Examen Décembre','exam_dec'],[' ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0'],['        ','0']],
	periode:   [['1er','periode1'],['2ieme','periode2'],['3ieme','periode3'],['4ieme','periode4'],['5ieme','periode5'],['6ieme','periode6'],['7ieme','periode7'],['8ieme','periode8'],['9ieme','periode9']]
};

function trimes22(sel) { _trApply(sel, _T9[sel.value]); }
function trimes2(sel)  { _trApply(sel, _T9[sel.value]); }

// 4-option map (trimes — bulletin_param, gestion_abs_sconet, gestion_examen_listing, profpprojo, visa_scolaire, imprimer_trimestre)
var _T4 = {
	trimestre: [['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3'],['','']],
	semestre:  [['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel'],['','']],
	cycle:     [['Cycle 1','cycle1'],['Cycle 2','cycle2'],['Cycle 3','cycle3'],['Cycle 4','cycle4']],
	annuel:    [['Annuel','annuel'],['','0'],['','0'],['','']],
	examen:    [['Examen Juin','exam_juin'],['Examen Décembre','exam_dec'],['','0'],['','']]
};

function trimes(sel) { _trApply(sel, _T4[sel.value]); }

// 3-option map (trimes7 — formulaire7)
var _T3 = {
	trimestre: [['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],
	semestre:  [['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['','0']]
};

function trimes7(sel) { _trApply(sel, _T3[sel.value]); }

// 3-option map for "an" forms (trimesan / trimesan2)
var _Tan = {
	trimestre: [['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],
	semestre:  [['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],[' ','trimestre3']]
};

function trimesan(sel)  { _trApply(sel, _Tan[sel.value]); }
function trimesan2(sel) { _trApply(sel, _Tan[sel.value]); }

//------------------------------------------------------------------------//
//------------------------------------------------------------------------//
