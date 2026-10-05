<template>
    <form action="#" method="post" @submit.prevent="submit">
        <div class="row">
            <div class="colonne4">
                <label for="ip">
                    {{ messages.get("authentication", "admin_authentication_add_ip_label") }}
                </label>
            </div>
            <div class="colonne_suite">
                <input
                    id="ip"
                    type="text"
                    class="saisie-80em"
                    name="ip"
                    v-model="ip"
                    :pattern="pattern"/>
            </div>
            <div class="colonne_suite">
                <button class="bouton" type="submit">
                    {{ messages.get("authentication", "admin_authentication_submit") }}
                </button>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data: function () {
        return {
            ip: "",
            pattern: "[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}"
        }
    },
    methods: {
        submit() {
            const regex = new RegExp(this.pattern);

            if (regex.test(this.ip)) {
                this.$emit("submit", this.ip);
                this.ip = "";
            }
            return false;
        }
    }
}
</script>