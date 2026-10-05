<template>
  <ul class="questions">
    <template v-for="(question, index) in session.aiSessionSemantiqueQuestions">
      <li
          :class="['question', isActiveQuestion(index) ? 'active' : '']"
          :id="`question_${index}`"
          :key="`question_${index}`"
      >
        <p class="question_text">
            {{ question }}
        </p>
        <a class="question_see_more" :href="hrefHistory(index)" v-if="hasHistory(index)">
            {{ messages.get('ai', 'see_more') }}
        </a>
      </li>
      <li class="answer" :id="`answer_${index}`" :key="`answer_${index}`">
        <p class="answer_text" v-if="fetch_text_generation && index == lastIndexQuestion">
            <span class="writing">
              <span></span>
              <span></span>
              <span></span>
            </span>
        </p>
        <p class="answer_text" v-else-if="session.aiSessionSemantiqueReponses[index]" v-html="session.aiSessionSemantiqueReponses[index]">
        </p>
        <p class="answer_text" v-else>
          {{ messages.get('ai', 'no_answer') }}
        </p>
        <p class="answer_information">
            {{ messages.get('ai', 'answer_information') }}
        </p>
      </li>
      <li class="tips" v-if="index == lastIndexQuestion && (hasTips || fetch_tips)">
        <div class="tips_container_text">
          <p class="tips_text" v-if="fetch_tips">
              <span class="writing">
                <span></span>
                <span></span>
                <span></span>
              </span>
          </p>
          <p class="tips_text" v-else-if="hasTips">
            {{ messages.get('ai', 'tips_text') }} <a :href="reformulated_question_link">{{ tips.reformulee }}</a>
            {{ messages.get('ai', 'tips_text_suite') }} <a :href="boolean_search_link" target="_blank">{{ tips.boolean }}</a>
            <br>
            {{ messages.get('ai', 'recommendation') }} <span v-html="tips.conseil"></span>
          </p>
        </div>
      </li>
    </template>
  </ul>
</template>

<script>
  export default {
    name: "session",
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
    },
    computed: {
      lastIndexQuestion() {
        return this.session.aiSessionSemantiqueQuestions.length - 1
      },
      hasTips: function () {
        return (
          (this.tips.conseil && this.tips.conseil.length > 0) ||
          (this.tips.reformulee && this.tips.reformulee.length > 0)
        );
      },
      reformulated_question_link() {
        const url = new URL(window.location.toString());
        const paramsUrl = new URLSearchParams(url);

        paramsUrl.set('lvl', 'search_result');
        paramsUrl.set('search_type_asked', 'ai_search');
        paramsUrl.set('ai_session', this.session.idAiSessionSemantique);
        paramsUrl.set('user_query', this.tips.reformulee);

        url.search = paramsUrl.toString();
        url.hash = 'ai_search';
        return url.toString();
      },
      boolean_search_link() {
        const url = new URL(window.location.toString());
        const paramsUrl = new URLSearchParams(url);

        paramsUrl.set('lvl', 'more_results');
        paramsUrl.set('user_query', this.tips.boolean);
        paramsUrl.set('autolevel1', 1);
        paramsUrl.set('look_ALL', 1);

        url.search = paramsUrl.toString();
        url.hash = "";

        return url.toString();
      }
    },
    updated() {
      this.$nextTick(() => this.scrollToBottom());
    },
    methods: {
      isActiveQuestion(indexQuestion) {
        if (this.current_ai_session === null || this.session.idAiSessionSemantique != this.current_ai_session) {
          return false
        }
        if (this.current_index_question === null || indexQuestion != this.current_index_question) {
          return false
        }
        return true;
      },
      scrollToBottom() {
        const questions = document.querySelector(".questions");
        if (!questions) {
            return;
        }

        const questionActive = document.querySelector(".questions > .question.active");
        let offsetTop = questions.scrollHeight
        if (questionActive) {
            offsetTop = questionActive.offsetTop - (questionActive.offsetHeight * 1.5)
        }
        questions.scroll({
          top: offsetTop,
          behavior: "smooth",
        });
      },
      hasHistory(indexQuestion) {
        return this.session.aiSessionSemantiqueHistorique[indexQuestion] !== undefined;
      },
      hrefHistory(indexQuestion) {
        const ai_search_history = this.session.aiSessionSemantiqueHistorique[indexQuestion];
        return `./index.php?lvl=search_result&get_query=${ai_search_history}`;
      }
    },
  };
  </script>
