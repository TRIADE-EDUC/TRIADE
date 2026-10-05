<template>
    <button type="button" class="bouton" @click="rapport" :disabled="disabled">
        {{ messages.get('dsi', 'rapport') }}
    </button>
</template>

<script>
export default {
    props : {
        history: {
            type: Object,
            default: () => { return {}; }
        },
        contentHistoryTypes: {
            type: Object,
            default: () => { return {}; }
        },
    },
    data: function() {
        return {}
    },
    computed : {
        disabled: function() {
            const stats = this.getStats();
            return !stats || !stats.report.type;
        }
    },
    methods : {
        getStats: function() {
            if (this.history.contentHistory[this.contentHistoryTypes['channel']] && this.history.contentHistory[this.contentHistoryTypes['channel']][0]) {
                const contentHistory = this.history.contentHistory[this.contentHistoryTypes['channel']][0];
                return contentHistory.content.settings.stats || null;
            }
            return null;
        },
        rapport: function () {
            const contentHistoryStats = this.getStats(this.history);
            switch (contentHistoryStats.report.type) {
                case 1:
                    window.location = contentHistoryStats.report.data;
                    break;

                default:
                    return false;
            }
        }
    }
}
</script>