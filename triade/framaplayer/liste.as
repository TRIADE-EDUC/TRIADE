onClipEvent (load) {
	this.setStyleProperty("background", 0xF5F5FF);
	this.setStyleProperty("selection", 0xEAEAFF);
	this.setStyleProperty("textColor", 0x707092);	this.setStyleProperty("text", 0x707092);
	this.setStyleProperty("textSelected", 0x707092);
	this.setStyleProperty("highlight3D", 0xEAEAFF);
	//this.setStyleProperty("highlight", 0x707092);
	//this.setStyleProperty("arrow", 0x0066CC);
	//this.setStyleProperty("darkshadow", 0x707092);
	this.setStyleProperty("face", 0xEAEAFF);
	this.setStyleProperty("shadow", 0xEAEAFF);
	this.setStyleProperty("selectionUnfocused", 0xEAEAFF);
	this.setStyleProperty("scrollTrack", 0xEAEAFF);
	this.setStyleProperty("focusRectOuter", 0x000066);	//this.setStyleProperty("fadeRate", 20);
	this.setStyleProperty("textSize", 10);
	this.setAutoHideScrollBar(true);
	Selection.setFocus(this);
	sel = 0;
}

onClipEvent (enterFrame) {
	select = this.getSelectedIndex();
	
	if (select != sel & select != undefined) {
		trace("select===="+select);
		_root.playFileFromXML(Number(select));
		sel = select;
	}
}