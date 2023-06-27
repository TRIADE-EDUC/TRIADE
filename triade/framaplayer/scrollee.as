
/************* Start scrollee functions ***************/

FScrollee = function (mc,maxString, waitingFrame) {
	this.mc = mc;
	this.waitingFrame = waitingFrame;
	this.maxString = maxString;
	this.currentLetter = 1;
	this.wait = 0;
}

FScrollee.prototype.SetText = function (text, bReset) {
	if (arguments[1] == null) { //Default value for bReset is true
		bReset = true;
		this.text = text;
	}
	if (bReset) { this.currentLetter = 1; }
}

FScrollee.prototype.DisplayAuthor = function () {
  if (this.text.length >= this.maxString) {
	if (this.currentLetter <= this.maxString) {
		this.mc.soundAuthorText = this.text.substr( 0, this.currentLetter );
	} else {
		this.mc.soundAuthorText = this.text.substr( this.currentLetter - this.maxString, this.maxString );
	}
	if (this.currentLetter == this.text.length) {
		if (this.wait < this.waitingFrame) {
			this.wait++
		} else {
			this.currentLetter = 1;
			this.wait = 0;
		}
	} else {
		this.currentLetter++;
	}
  } else {
	 this.mc.soundAuthorText = this.text;
  }
}
FScrollee.prototype.DisplayTitle = function () {
  if (this.text.length >= this.maxString) {
	if (this.currentLetter <= this.maxString) {
		this.mc.soundTitleText = this.text.substr( 0, this.currentLetter );
	} else {
		this.mc.soundTitleText = this.text.substr( this.currentLetter - this.maxString, this.maxString );
	}
	if (this.currentLetter == this.text.length) {
		if (this.wait < this.waitingFrame) {
			this.wait++
		} else {
			this.currentLetter = 1;
			this.wait = 0;
		}
	} else {
		this.currentLetter++;
	}
  } else {
	 this.mc.soundTitleText = this.text;
  }
}
var soundAuthorScrollee = new FScrollee (mcInfo, 30, 20);
var soundTitleScrollee = new FScrollee (mcInfo, 30, 20);
/************** End scrollee functions ***************/