<template>
    <div>
        <table>
            <thead>
                <tr>
                    <th>{{ messages.get("authentication", "admin_authentication_ip") }}</th>
                    <th>{{ messages.get("authentication", "admin_authentication_date") }}</th>
                    <th>{{ messages.get("authentication", "admin_authentication_actions") }}</th>
                </tr>
            </thead>
            <tbody>
                <template v-if="rows.length > 0">
                    <tr  v-for="item in rows">
                        <td>{{ item.ip }}</td>
                        <td>{{ item.date }}</td>
                        <td>
                            <button type="button" class="bouton" @click="remove(item)">
                                {{ messages.get("common", "remove") }}
                            </button>
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="3" class="center">{{ messages.get("authentication", "admin_authentication_no_ip") }}</td>
                </tr>
            </tbody>
        </table>

        <nav class="pagination" v-if="pages.length > 1">
            <button type="button" :disabled="currentPage == 1" class="pagination-bouton-nav" @click="changePage(1)">&laquo;</button>
            <button type="button" :disabled="currentPage == 1" class="pagination-bouton-nav" @click="changePage(currentPage - 1)">&#139;</button>

            <button type="button" :class="page.isActive ? 'bouton-page active' : 'bouton-page'" v-for="(page, index) in pages" :key="index" @click="changePage(page.page)">{{ page.page }}</button>

            <button type="button" :disabled="currentPage == pageCount" class="pagination-bouton-nav" @click="changePage(currentPage + 1)">&#155;</button>
            <button type="button" :disabled="currentPage == pageCount" class="pagination-bouton-nav" @click="changePage(pageCount)">&raquo;</button>
        </nav>
    </div>
</template>

<script>
    export default {
        props : ['items', 'pages', 'pageCount'],
        mounted: function () {
            let items = this.items || [];
            this.rows = this.helper.cloneObject(items);
        },
        data: function () {
            return {
                rows: [],
            }
        },
        computed: {
            currentPage: function () {
                const page = this.pages.find(page => page.isActive);
                return page ? page.page : 1;
            },
        },
        methods: {
            remove: function (item) {
                this.$emit('remove', item);
            },
            changePage(page) {
                this.$emit('change-page', page);
            },
        }
    }
</script>