
class Messages {

	get(js, code) {
		if (typeof code != "string") {
			console.error(`[Messages] invalid code !`);
			return "Error";
		}

		if (code.slice(0, 4) == "msg:") {
			code = code.slice(4);
		}
        let message = '';
        try {
		  message = pmbDojo.messages.getMessage(js, code);
		} catch (e) {
            console.log("js = " + js + "code = " + code);
            console.log("error = " + e);
            return code;
        }
		return ("" != message) ? message : code;
	}

}

export default new Messages();