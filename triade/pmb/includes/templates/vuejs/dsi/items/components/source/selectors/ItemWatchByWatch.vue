<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group">
            <label for="sectionList">{{ messages.get('dsi', 'source_form_label_item_watch_by_watch') }}</label>
            <div class="dsi-form-group-content">
                <select name="sectionList" v-model.number="data.watchId">
                    <option value="0" disabled>
                        {{ messages.get('dsi', 'source_form_selector_item_watch_by_watch') }}
                    </option>
                    <option v-for="(watchTitle, index) in watchList"
                        :key="index"
                        :value="index"
                        v-html="watchTitle">
                    </option>
                </select>
            </div>
        </div>
        <div v-show="data.watchId > 0">
            <slot name="trySearch"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            dafault: () => {
                return {
                    watchId: 0
                }
            }
        }
    },
    data: function () {
        return {
            watchList: [],
        }
    },
    created() {
        if (!this.data.watchId) {
            this.$set(this.data, "watchId", 0);
        }

        this.getSectionList();
    },
    methods: {
        getSectionList: async function () {
            let response = await this.ws.get('items', 'getWatchList');
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                this.watchList = response;
            }
        }
    }
}
</script>

