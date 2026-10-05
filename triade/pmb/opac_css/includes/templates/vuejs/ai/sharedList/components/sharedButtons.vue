<template>
  <div class="ai_shared_buttons">
    <input 
      type="button" 
      :value="messages.get('ai', 'sharedlist_btn_add_docnum')" @click="openModal">

    <input 
      type="button" 
      :value="indexationLabel" 
      :title="indexationTitle" 
      @click="startIndexation"
      :disabled="nbElementsRemaining == 0">

    <sharedUploadModal 
      :shared_list_id="shared_list_id"
      :upload_max_size="upload_max_size"
      :showModal="showModal"
      @closeModal="closeModal">
    </sharedUploadModal>
  </div>
</template>

<script>
  import sharedUploadModal from "./sharedUploadModal.vue";
  export default {
    name: "sharedButtons",
    props: {
      shared_list_id: {
        type: Number,
        default: 0
      },
      indexation_packet_size: {
        type: Number,
        default: 5
      },
      nb_records: {
        type: Number,
        default: 0
      },
      nb_docnums: {
        type: Number,
        default: 0
      },
      upload_max_size: {
        type: Number,
        default: 100
      }
    },
    components: {
      sharedUploadModal
    },
    data() {
      return {
        indexation_progress_records: 0,
        indexation_progress_docnums: 0,
        status_progress: false,
        showModal: false,
        nbRecordsRemaining: this.nb_records,
        nbDocnumsRemaining: this.nb_docnums
      }
    },
    mounted() {
      window.addEventListener('DocumentUploaded', () => {
        this.nbDocnumsRemaining++;
      });
      window.addEventListener('DocumentRemoved', () => {
        this.nbDocnumsRemaining--;
      });
    },
    computed: {

      /**
       * Renvoie le label du bouton d'indexation en fonction de l'état actuel de la progression.
       *
       * @return {string}
       */
      indexationLabel() {
        if(!this.status_progress) {
          return this.messages.get('ai', 'sharedlist_btn_index_list')
        }

        return this.messages.get('ai', 'sharedlist_btn_index_list_progress').replace('%s', this.calcPercentage);
      },

      /**
       * Renvoie le title du bouton d'indexation
       *
       * @return {string}
       */
      indexationTitle() {
        return `${this.messages.get('ai', 'sharedlist_btn_index_list_title')} ${this.nbRecordsRemaining}\n${this.messages.get('ai', 'sharedlist_btn_index_docnum_title')} ${this.nbDocnumsRemaining}`;
      },

      /**
       * Calcul le pourcentage d'avancement de l'indexation
       *
       * @return {integer}
       */
      calcPercentage() {
        const totalElements = this.nb_records + this.nb_docnums;
        const indexedElements = this.indexation_progress_records + this.indexation_progress_docnums;
        return ((indexedElements / totalElements) * 100).toFixed(0);
      },

      nbElementsRemaining() {
        return this.nbRecordsRemaining + this.nbDocnumsRemaining;
      }
    },
    methods: {

      /**
       * Démarre le processus d'indexation d'une liste.
       *
       * @return {void}
       */
       async startIndexation() {
        if (!this.shared_list_id) {
          alert("Error: No shared list ID provided");
          return;
        }

        this.status_progress = true;

        await this.indexRecords();
        await this.indexDocnums();

        this.status_progress = false;
        this.indexation_progress_records = 0;
        this.indexation_progress_docnums = 0;
      },

      async indexRecords() {
        const totalPackets = Math.ceil(this.nb_records / this.indexation_packet_size);
        for (let i = 0; i < totalPackets; i++) {
          try {
            const response = await this.ws.post('AiApiSharedList', 'indexation', {
              id: this.shared_list_id,
              type: 'records'
            });

            if (response.error) {
              console.error(`Error in packet ${i + 1}:`, response.error);
              break;
            } else {
              this.indexation_progress_records = Math.min((i + 1) * this.indexation_packet_size, this.nb_records);
              this.nbRecordsRemaining = Math.max(0, this.nbRecordsRemaining - this.indexation_packet_size);
            }
          } catch (error) {
            console.error(`Failed to index packet ${i + 1}:`, error);
            break;
          }
        }
    },

    async indexDocnums() {
      const totalPackets = Math.ceil(this.nb_docnums / this.indexation_packet_size);
      for (let i = 0; i < totalPackets; i++) {
        try {
          const response = await this.ws.post('AiApiSharedList', 'indexation', {
            id: this.shared_list_id,
            type: 'docnums'
          });

          if (response.error) {
            console.error(`Error in packet ${i + 1}:`, response.error);
            break;
          } else {
            this.indexation_progress_docnums = Math.min((i + 1) * this.indexation_packet_size, this.nb_docnums);
            this.nbDocnumsRemaining = Math.max(0, this.nbDocnumsRemaining - this.indexation_packet_size);
          }
        } catch (error) {
          console.error(`Failed to index packet ${i + 1}:`, error);
          break;
        }
      }
    },

      /**
       * Ouvre la modal d'ajout de document.
       *
       * @return {void}
       */
      openModal() {
        this.showModal = true;
      },

      /**
       * Ferme la modal d'ajout de document.
       *
       * @return {void}
       */
      closeModal() {
        this.showModal = false;
      }
    }
  };
</script>
