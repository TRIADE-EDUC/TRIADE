<template>
  <div class="session">
    <fieldset class="session_fieldset">
      <legend class="session_legend" v-if="session.aiSessionSemantiqueName">
        {{ messages.get('ai', 'session_title') }} {{ session.aiSessionSemantiqueName }}
      </legend>
      <legend class="session_legend" v-else>
        {{ messages.get('ai', 'sessions_new') }}
      </legend>
      <session
        :session="session"
        :current_index_question="current_index_question"
        :current_ai_session="current_ai_session"
        :fetch_text_generation="fetch_text_generation"
        :fetch_tips="fetch_tips"
        :tips="tips"
      />
      <form
        name="ai_search_form"
        :action="formAction"
        method="post"
        class="ai_search_form"
        @submit="sendQuestion($event)"
      >
        <input type="hidden" name="ai_session" :value="session.id" />
        <div class="user_query_container">
          <input
            type="text"
            name="user_query"
            class="user_query"
            :placeholder="welcome_message"
            spellcheck="true"
            v-model.trim="user_query"
          />
          <button
            type="submit"
            class="bouton"
            :title="messages.get('ai', 'send_question')"
          >
            <span aria-hidden="true">&#8594;</span>
          </button>
        </div>
      </form>
    </fieldset>
    <div>
      <!-- Affichage de la pop-up -->
      <div v-if="isModalVisible" class="modal">
        <p>{{ messages.get('ai', 'wait_retry_after') }} {{ countdown }} {{ messages.get('ai', 'wait_retry_after_second') }}</p>
        <div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import session from "./session.vue";

export default {
  name: "sessionContainer",
  props: {
    tips: {
      default: () => {
        return {
          question: "",
          reformulee: "",
          boolean: "",
          conseil: "",
        };
      },
    },
    fetch_tips: {
      default: false,
    },
    fetch_text_generation: {
      default: false,
    },
    welcome_message: {
      type: String,
      default: "",
    },
    session: {
      type: Object,
      default: () => {
        return {
          idAiSessionSemantique: 0,
          aiSessionSemantiqueName: "",
          aiSessionSemantiqueQuestions: [],
          aiSessionSemantiqueReponses: [],
          aiSessionSemantiqueNumObjects: [],
          aiSessionSemantiqueHistorique: {}
        };
      },
    },
    current_index_question: {
      default: null
    },
    current_ai_session: {
      default: null
    },
    type: {
      type: Number,
      require: true,
    },
    list_id: {
      default: null
    },
  },
  components: {
    session
  },
  data(){
    return {
      user_query: "",
      isModalVisible: false,
      countdown: 0,
      randomDelay: 0,
      countdownInterval: null,
      redirectRetry: false
    }
  },
  created() {
    const url = new URL(window.location.toString());
    const paramsUrl = new URLSearchParams(url.search);
    const wait = parseInt(paramsUrl.get("wait"));
    const retryAfter = parseInt(paramsUrl.get("retry_after"));
    if ((!isNaN(wait) && wait === 1) && (!isNaN(retryAfter))) {
      this.randomDelay = (Math.floor(Math.random() * (4 - 2 + 1)) + 2) + retryAfter;
      this.redirectRetry = true;
      this.showModal();
    }
  },
  computed: {
    formAction() {
      let params = "";

      if(this.redirectRetry) {
        const url = new URL(window.location.toString());
        const paramsUrl = new URLSearchParams(url.search);

        const aiSession = parseInt(paramsUrl.get("ai_session"));
        const retryQuery = parseInt(paramsUrl.get("retry_query"));

        params = `&ai_session=${aiSession}&retry_query=${retryQuery}`;
      }

      if (this.type === this.const.TYPE_SHARED_LIST) {

        const url = new URL(window.location.toString());
        const paramsUrl = new URLSearchParams(url.search);
        const sub = paramsUrl.get('sub');

        return `./index.php?lvl=show_list&sub=${sub}&id_liste=${this.list_id}${params}#ai_search`;
      }
      return `./index.php?lvl=search_result&search_type_asked=ai_search&tips=1${params}#ai_search`;
    }
  },
  methods: {
    sendQuestion(event) {
      if (this.user_query === "") {
        event.preventDefault();
        return false;
      }
      return true;
    },
    showModal() {
      this.countdown = this.randomDelay;
      this.isModalVisible = true;

      const interval = setInterval(() => {
        this.countdown--;
        if (this.countdown <= 0) {
          clearInterval(interval);
          this.closeModal();
          this.redirect(); // Appeler la methode de redirection
        }
      }, 1000);
    },
    closeModal() {
      this.isModalVisible = false;
    },
    redirect() {
      window.location.href = this.formAction; // Rediriger vers l'URL
    }
  },
};
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal p {
  color: white;
}
</style>
