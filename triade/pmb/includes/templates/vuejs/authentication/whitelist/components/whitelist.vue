<template>
    <div>
        <h2>{{ messages.get("authentication", "auth_whitelist") }}</h2>
        <add-form @submit="addIp"></add-form>
        <list :items="items" :pages="pages" :page-count="pageCount" @remove="remove" @change-page="changePage"></list>
    </div>
</template>

<script>
import addForm from "../../common/list/addForm.vue";
import list from "../../common/list/list.vue";

export default {
    props: ['items', 'pages', 'pageCount'],
    components: {
        addForm,
        list
    },
    methods: {
        addIp(ip) {
            this.ws.post("whitelist", "add", {
                ip: ip
            })
            .then((response) => {
                if (response.error) {
                    this.notif.error(this.messages.get("common", "failed_save"));
                } else {
                    this.notif.info(this.messages.get("common", "success_save"));
                }
            })
            .catch((error) => {
                console.error(error);
            })
        },
        remove(item) {
            const msg = this.messages.get("authentication", "admin_authentication_confirm_delete").replace('%s', item.ip);

            if (confirm(msg)) {
                this.ws.post("whitelist", "remove", {
                    id: item.id
                })
                .then((response) => {
                    if (response.error) {
                        this.notif.error(this.messages.get("common", "failed_delete"));
                    } else {
                        this.notif.info(this.messages.get("common", "success_delete"));
                        document.location.reload();
                    }
                })
                .catch((error) => {
                    console.error(error);
                })
            }
        },
        changePage(page) {
            document.location = "./admin.php?categ=auth&sub=whitelist&page=" + page;
        }
    }
}
</script>