<template>
    <div>
        <h2 class="section-sub-title">{{ history.diffusion.name }}</h2>
        <h3 class="section-sub-title">{{ previewDate }}</h3>
        <table>
            <thead>
                <tr>
                    <th>{{ messages.get('dsi', 'diffusion_recipients') }}</th>
                    <th :colspan="getStatsList().length">{{ messages.get('dsi', 'statistique') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ history.totalRecipients }}</td>
                    <td v-for="(stat, index) in getStatsList()" :key="index">
                        {{ stat }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="dsi-form-diffusion-view">
            <div class="dsi-diffusion-view">
                <iframe
                    id="dsi-preview-frame"
                    :srcdoc="messages.get('dsi', 'dsi_preview_loading')"
                    :src="previewUrl"
                    width="100%"
                    sandbox="allow-same-origin"
                    @load="resizeFrame">
                </iframe>
            </div>
            <div class="dsi-diffusion-aside" v-if="subscribers.length">
                <pagination-list :list="subscribers" :nbPage="6" :perPage="10" :startPage="1" :nbResultDisplay="false">
                    <template #content="{ list }">
                        <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                            <thead>
                                <tr>
                                    <th>{{ messages.get('dsi', 'subscriber_list_name') }}</th>
                                    <th>{{ messages.get('dsi', 'subscriber_list_id_empr') }}</th>
                                    <th>{{ messages.get('dsi', 'subscriber_list_cb') }}</th>
                                    <th>{{ messages.get('dsi', 'subscriber_list_email') }}</th>
                                    <th>{{ messages.get('dsi', 'subscriber_type') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(empr, subindex) in list" :key="subindex">
                                    <td>{{ empr.name }}</td>
                                    <td>{{ empr.settings.idEmpr }}</td>
                                    <td>{{ empr.settings.cb }}</td>
                                    <td>{{ empr.settings.email }}</td>
                                    <td>
                                        <i v-if="empr.type == 1" class="fa fa-hand-spock-o" aria-hidden="true"></i>
                                        <i v-if="empr.type == 2" class="fa fa-database" aria-hidden="true"></i>
                                        <i v-if="empr.type == 3" class="fa fa-download" aria-hidden="true"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </pagination-list>
            </div>
        </div>
    </div>
</template>

<script>
import PaginationList from '../../components/paginationList.vue';
import FormSubscriberListContainer from '../../subscriberList/components/formSubscriberListContainer.vue';
import formView from '../../views/components/formView.vue';

export default {
    components: { formView, FormSubscriberListContainer, PaginationList },
    name: "preview",
    props: {
        history: {
            type: Object,
            required: true
        },
        contentHistoryTypes: {
            type: Object,
            default: () => { return {}; }
        },
    },
    data: function () {
        return {
            entityType: [],
            viewTypes: [],
            contentTypes: []
        }
    },
    mounted: function () {
        if (typeof domUpdated === "function") {
            domUpdated();
        }
        this.fetchData();
    },
    computed: {
        previewUrl: function () {
            return "./rest.php/dsi/diffusionsHistory/preview/" + this.history.id;
        },
        previewDate: function () {
            return this.messages.get('dsi', 'preview_date').replace('%s', this.history.date);
        },
        subscribers: function () {
            if (!Object.keys(this.contentHistoryTypes).length || !Object.keys(this.history.contentHistory).length) {
                return [];
            }
            if (this.history.contentHistory[this.contentHistoryTypes["subscribers"]][0] !== undefined) {
                return this.history.contentHistory[this.contentHistoryTypes["subscribers"]][0].content;
            }
            return [];
        }
    },
    methods: {
        getStats: function() {
            if (
                this.history.contentHistory[this.contentHistoryTypes['channel']] &&
                this.history.contentHistory[this.contentHistoryTypes['channel']][0]
            ) {
                const contentHistory = this.history.contentHistory[this.contentHistoryTypes['channel']][0];
                return contentHistory.content.settings.stats || null;
            }
            return null;
        },
        getStatsList: function() {
            const contentHistoryStats = this.getStats();
            const stats = contentHistoryStats ? contentHistoryStats.stats : ["--"];
            return Object.values(stats);
        },
        fetchData: async function () {
            const promises = [
                this.ws.get('diffusions', 'getEntityList'),
                this.ws.get('views', 'getTypeListAjax'),
            ];
            const result = await Promise.all(promises);
            this.entityType = result[0];
            this.viewTypes = result[1];
        },
        resizeFrame() {
            let frame = document.getElementById("dsi-preview-frame");
            if (frame) {
                const iframeDocument = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;

                let height = iframeDocument.documentElement.scrollHeight;
                if (height >= 800) {
                    height = 800;
                }

                frame.removeAttribute('srcdoc');
                frame.style.height = height + 'px';

                const links = iframeDocument.querySelectorAll('a');
                // Boucler sur chaque lien et ajouter l'attribut target
                links.forEach(link => {
                    link.setAttribute('target', '_top');
                });
            }
        }
    }
}
</script>