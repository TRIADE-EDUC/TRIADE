import Vue from "vue";
// Helper
import Webservice from "../../common/helper/WebService.js";
import messages from "../../common/helper/Messages.js";
import images from "../../common/helper/Images.js";
// Components
import sessions from "./components/sessions.vue";
import sessionContainer from "./components/sessionContainer.vue";

document.addEventListener("DOMContentLoaded", function() {
  // Definition des constantes
  Vue.prototype.const = {
    TYPE_SEMANTIC: 0,
    TYPE_SHARED_LIST: 1,
  };

  // Definition des datas
  let $searchData = window.$searchData || {};
  $searchData = {
    // Valeur par defaut
    type: Vue.prototype.const.TYPE_SEMANTIC,
    list_id: null,
    // Valeurs
    ...$searchData,
  };

  // Init
  Vue.prototype.messages = messages;
  Vue.prototype.images = images;
  Vue.prototype.ws = new Webservice($searchData.webservice_url);

  new Vue({
    el: "#ai_search",
    data: {
      ...$searchData,
      ...{
        sessions: [],
        current_session: {
          idAiSessionSemantique: 0,
          aiSessionSemantiqueName: "",
          aiSessionSemantiqueQuestions: [],
          aiSessionSemantiqueReponses: [],
          aiSessionSemantiqueNumObjects: [],
          aiSessionSemantiqueHistorique: {},
        },
        fetch_tips: false,
        tips: {
          question: "",
          reformulee: "",
          boolean: "",
          conseil: "",
        },
        loaded: false,
        ready: false,
      },
    },
    components: {
      sessions,
      sessionContainer,
    },
    created() {
      this.ready = true;

      let promiseList = [];
      if (this.type === this.const.TYPE_SHARED_LIST) {
        promiseList.push(
          this.ws.post("session", "list", {
            id: this.list_id,
          })
        );
      } else {
        promiseList.push(this.ws.get("session", "list"));
      }

      const params = new URLSearchParams(location.search);
      if (
        this.ai_session <= 0 &&
        params.get("lvl") === "search_result" &&
        this.type === this.const.TYPE_SEMANTIC
      ) {
        promiseList.push(this.ws.get("session", "last"));
      }

      if (this.ai_session < 0 && this.type === this.const.TYPE_SHARED_LIST) {
        promiseList.push(
          this.ws.post("session", "last", {
            id: this.list_id,
          })
        );
      }

      Promise.all(promiseList).then((promise) => {
        this.sessions = promise[0].data;
        if (promise[1] && promise[1].data) {
          this.current_session = promise[1].data;
          this.ai_session = this.current_session.idAiSessionSemantique;
        } else if (this.ai_session > 0) {
          this.current_session = this.sessions.find(
            (session) => session.idAiSessionSemantique == this.ai_session
          );
        }

        if (this.ai_session_index_question == null) {
          this.ai_session_index_question =
            this.current_session.aiSessionSemantiqueQuestions.length - 1;
        }

        this.loaded = true;
        if (this.fetch_text_generation) {
          const currentUrl = new URL(window.location.toString());
          const urlParams = new URLSearchParams(currentUrl.search);

          this.fetchTextGeneration();
          if (urlParams.has("tips") && urlParams.get("tips") == 1) {
            this.fetch_tips = true;
            this.fetchTips();
          }
        }
      });
    },
    methods: {
      fetchTips() {
        if (!this.ai_session) {
          this.fetch_tips = false;
          return false;
        }

        this.ws
          .post("text", "tips", {
            id: this.ai_session,
            indexQuestion: this.ai_session_index_question,
          })
          .then((response) => {
            if (response.data) {
              this.tips = response.data;
            }

            this.fetch_tips = false;
          })
          .catch((error) => {
            this.fetch_tips = false;
          });
      },
      fetchTextGeneration() {
        if (!this.ai_session) {
          this.fetch_text_generation = false;
          return false;
        }

        this.ws
          .post("text", "generation", {
            id: this.ai_session,
            indexQuestion: this.ai_session_index_question,
          })
          .then((response) => {
            if (response.result) {
              this.current_session.aiSessionSemantiqueReponses[
                this.ai_session_index_question
              ] = response.result;
            }

            this.fetch_text_generation = false;
          })
          .catch((error) => {
            this.fetch_text_generation = false;
          });
      },
      selectionSession(session) {
        this.current_session = session;
      },
      async renameSession(session) {
        // Requete ajax
        let response = null;
        if (this.type === this.const.TYPE_SHARED_LIST) {
          response = await this.ws.post("session", "rename", {
            idSession: session.idAiSessionSemantique,
            id: this.list_id,
            name: session.aiSessionSemantiqueName,
          });
        } else {
          response = await this.ws.post("session", "rename", {
            id: session.idAiSessionSemantique,
            name: session.aiSessionSemantiqueName,
          });
        }

        if (!response.error) {
          // Renommage dans le tableau sessions
          let index = this.sessions.findIndex(
            (s) => s.idAiSessionSemantique == session.idAiSessionSemantique
          );
          this.sessions[index].aiSessionSemantiqueName =
            session.aiSessionSemantiqueName;

          if (
            this.current_session.idAiSessionSemantique ==
            session.idAiSessionSemantique
          ) {
            this.current_session.aiSessionSemantiqueName =
              session.aiSessionSemantiqueName;
          }
        }
      },
      async deleteSession(session) {
        // Requete ajax
        let response = null;
        if (this.type === this.const.TYPE_SHARED_LIST) {
          response = await this.ws.post("session", "delete", {
            idSession: session.idAiSessionSemantique,
            id: this.list_id,
          });
        } else {
          response = await this.ws.post("session", "delete", {
            id: session.idAiSessionSemantique,
          });
        }

        // Suppression dans le tableau sessions
        if (!response.error) {
          if (
            this.current_session.idAiSessionSemantique ==
            session.idAiSessionSemantique
          ) {
            this.newSession();
          }

          let index = this.sessions.findIndex(
            (s) => s.idAiSessionSemantique == session.idAiSessionSemantique
          );
          this.sessions.splice(index, 1);
        }
      },
      newSession() {
        if (this.ai_session == this.current_session.idAiSessionSemantique) {
          this.ai_session = null;
          this.current_index_question = null;
        }

        this.current_session = {
          idAiSessionSemantique: 0,
          aiSessionSemantiqueName: "",
          aiSessionSemantiqueQuestions: [],
          aiSessionSemantiqueNumObjects: [],
          aiSessionSemantiqueHistorique: {},
        };
      },
    },
  });
});
