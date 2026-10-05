import WebService from "../../common/helper/WebService";

class DsiMessages {
    constructor(webserviceUrl) {
        this.msg = [];
        this.ws = new WebService(webserviceUrl);
    }

    async getModuleMessages(moduleName) {
        if(this.msg[moduleName]) {
            return this.msg[moduleName];
        }
        let messages = await this.ws.get("common", "messages/" + moduleName);
        if(! messages.error) {
            this.msg[moduleName] = messages;
        }
        return this.msg[moduleName];
    }
}

export default DsiMessages;