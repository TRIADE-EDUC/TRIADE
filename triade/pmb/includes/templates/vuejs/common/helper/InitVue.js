import WebService from "./WebService.js";
import Messages from "./Messages.js";
import Notif from "./Notif.js";
import Helper from "./Helper.js";
import Images from "./Images.js";

export default function InitVue(Vue, options) {
    Vue.prototype.messages = Messages;
    Vue.prototype.notif = Notif;
    Vue.prototype.helper = Helper;
    Vue.prototype.images = Images;

    if (options.urlWebservice) {
        Vue.prototype.ws = new WebService(options.urlWebservice, options.webserviceCachingOptions ?? false);
    }

    if(options.plugins) {
        for(let i of Object.keys(options.plugins)) {
            Vue.prototype[i] = options.plugins[i];
        }
    }

    if (options.useLoader) {
        let loaderActive = false;
        let loaderNeed = 0;
        Vue.prototype.showLoader = () => {
            if (loaderActive) {
                loaderNeed++;
            } else {
                window.dispatchEvent(new Event("showLoader"));
                loaderActive = true;
            }
        }

        Vue.prototype.hiddenLoader = () => {
            if (loaderActive) {
                if (loaderNeed > 1) {
                    loaderNeed--;
                    return true;
                }
                setTimeout(() => {
                    window.dispatchEvent(new Event("hiddenLoader"));
                    loaderActive = false;
                    loaderNeed = 0;
                }, 300);
            }
        }

        if (Vue.prototype.ws) {
            Vue.prototype.ws.addEventListener("fetch", () => {
                Vue.prototype.showLoader();
            })
            Vue.prototype.ws.addEventListener("response", () => {
                Vue.prototype.hiddenLoader();
            })
            Vue.prototype.ws.addEventListener("error", () => {
                Vue.prototype.hiddenLoader();
            })
        }
    }
}