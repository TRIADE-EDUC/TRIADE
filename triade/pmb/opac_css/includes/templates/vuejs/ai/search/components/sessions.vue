<template>
  <div class="ai_sessions">
    <modal
      :showModal="showModal"
      @closeModal="closeModal"
      @validModal="validModal"
      :label="messages.get('ai', 'session_rename')"
      class="ai_sessions_modal ai_sessions_rename">
      <fieldset>
        <legend>{{ messages.get('ai', 'session_rename') }}</legend>
        <input type="text" name="session_name" v-model="edit_session.aiSessionSemantiqueName" />
      </fieldset>
    </modal>
    <h2 class="ai_sessions_title">{{ messages.get('ai', 'sessions_title') }}</h2>
    <ul class="ai_sessions_list" v-if="sessions.length">
      <li
        :class="['ai_sessions_list_item', { active: isActive(session) }]"
        v-for="(session, index) in sessions"
        :key="index"
      >
        <button type="button" class="ai_sessions_list_item_name btn_link" @click.prevent="selectionSession(session)">
          {{ session.aiSessionSemantiqueName }}
        </button>
        <button type="button"
          class="ai_sessions_list_item_rename"
          :title="messages.get('ai', 'session_rename')"
          @click.prevent="renameSession(session)">
          <img :src="images.get('b_edit.png')" :alt="messages.get('ai', 'session_rename')">
        </button>
        <button type="button"
          class="ai_sessions_list_item_delete"
          :title="messages.get('ai', 'session_delete')"
          @click.prevent="deleteSession(session)">
          <img :src="images.get('empty-001.svg')" :alt="messages.get('ai', 'session_delete')">
        </button>
      </li>
      <li class="ai_sessions_list_item ai_sessions_list_item_new" v-if="current_session.idAiSessionSemantique">
        <button type="button"
          class="ai_sessions_list_item_delete"
          @click.prevent="newSession">
          {{ messages.get('ai', 'sessions_new') }}
        </button>
      </li>
    </ul>
    <p class="ai_sessions_list_empty" v-else>{{ messages.get('ai', 'sessions_list_empty') }}</p>
  </div>
</template>

<script>
import modal from "../../../common/components/modal.vue";
export default {
  name: "sessions",
  components: {
    modal
  },
  props: {
    current_session: {
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
    sessions: {
      type: Array,
      default: () => [],
    },
    type: {
      type: Number,
      require: true,
    },
    list_id: {
      default: null
    },
  },
  data: () => {
    return {
      showModal: false,
      edit_session: {
        idAiSessionSemantique: 0,
        aiSessionSemantiqueName: "",
        aiSessionSemantiqueQuestions: [],
        aiSessionSemantiqueNumObjects: [],
      },
    }
  },
  methods: {
    isActive(session) {
      return (
        this.current_session.idAiSessionSemantique ===
        session.idAiSessionSemantique
      );
    },
    selectionSession(session) {
      this.$root.selectionSession(session);
    },
    deleteSession(session) {
      const confirm_msg = this.messages.get('ai', 'session_delete_confirm')
        .replace('%s', session.aiSessionSemantiqueName);

      if (confirm(confirm_msg)) {
        this.$root.deleteSession(session);
      }
    },
    cloneObject(object) {
      return JSON.parse(JSON.stringify(object));
    },
    renameSession(session) {
      this.edit_session = this.cloneObject(session);
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.edit_session = {
        idAiSessionSemantique: 0,
        aiSessionSemantiqueName: "",
        aiSessionSemantiqueQuestions: [],
        aiSessionSemantiqueNumObjects: [],
      };
    },
    validModal() {
      this.$root.renameSession(this.edit_session);
      this.closeModal();
    },
    newSession() {
      this.$root.newSession();
    }
  },
};
</script>
