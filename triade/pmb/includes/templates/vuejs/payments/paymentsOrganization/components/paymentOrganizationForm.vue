<template>
    <div>
        <form class="form-admin" action="" method="POST" @submit.prevent="$emit('submit')">
            <h3>
                {{ messages.get("payment", "admin_payment_oarganization_add") }}
            </h3>
            <div class='form-contenu'>
                <div class='row' v-if="!organization">
                    <div class="colonne3">
                        <label>{{ messages.get("payment", "admin_payment_organization_choice") }} :</label>
                    </div>
                    <div class="colonne_suite">
                        <select name="organizationDetail" id="organizationDetail" v-model="organizationName">
                            <option value="">{{ messages.get("payment", "admin_payment_choice") }}</option>
                            <option v-for="(organization, index) in organizationlistavaible" :key="index"
                                :value="organization">{{ organization }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div id="organizationDetails">
                        <component :is="organizationName" :organizationName="organizationName" :organization="organization"
                            @cancel="cancel" v-if="showComponent" @beforeSubmit="$event => beforeSubmit($event)">
                        </component>
                        <div v-if="!showComponent && organizationName">
                            <img :src="images.get('patience.gif')" :alt="messages.get('common', 'wait')"
                                :title="messages.get('common', 'wait')">
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <br />
                    <div class="left">
                        <input @click="cancel" class="bouton" type="button"
                            :value="messages.get('payment', 'admin_payment_cancel')" />
                        <input class="bouton" type="submit" :value="messages.get('payment', 'admin_payment_save')" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>

import payfip from "../components/organization/payfip.vue";

export default {

    props: ["organization", "organizationlistavaible"],

    data: function () {
        return {
            organizationName: "",
        }
    },
    created: function () {
        if (this.organization) {
            this.organizationName = this.organization.name;
            this.organization.data = JSON.parse(this.organization.data)
        }
    },
    computed: {
        showComponent: function () {
            if (this.organizationName && this.organizationName != "") {
                return true;
            }
            return false;
        }
    },
    methods: {
        cancel: function () {
            document.location = './admin.php?categ=finance&sub=organization_account';
        },
        beforeSubmit(event) {
            //Here check the event
            // console.log(event);

            //Here submit
            this.submit(event);
        },
        submit(data) {
            let formData = new FormData();
            formData.append('data', JSON.stringify(data.data));

            fetch('./admin.php?categ=finance&sub=organization_account&action=save', {
                method: 'POST',
                body: formData,
            }).then(() => {
                document.location = './admin.php?categ=finance&sub=organization_account';
            }).then(response => console.log(response));
        }
    },
    components: {
        payfip: payfip,
    }
}
</script>