<template>
    <div>
        <div class='row'>
            <div class="colonne3">
                <label>{{ messages.get('payment', 'admin_payment_name') }}</label>
            </div>
            <div class="colonne_suite">
                <label v-if="organization.name">{{ organization.name }}</label>
                <label v-else>{{ organizationName }}</label>
            </div>
        </div>
        <div class='row'>
            <div class="colonne3">
                <label>{{ messages.get('payment', 'admin_payment_payfip_numclient') }}</label>
            </div>
            <div class="colonne_suite">
                <input type="text" v-model="organization.data.numclient" />
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ["organizationName", "organization"],

    data: function () {
        return {
        }
    },
    created: function () {
        if (!this.organization) {
            this.organization = {
                data: {
                    numclient: "",
                },
                name: this.organizationName,
            };
        }
        this.$parent.$on('submit', this.beforeSubmit);
    },
    computed: {
    },
    methods: {
        beforeSubmit() {
            this.$emit('beforeSubmit', {
                data: {
                    organization: this.organization
                }
            });
            return true;
        }
    },
    components: {
    }
}
</script>