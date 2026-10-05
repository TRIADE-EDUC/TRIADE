<template>
	<tbody class="account-debt-group">
        <tr class="account-debt-line">
            <td class="account-debt-name">{{ account.label }}</td>
            <td class="account-debt-amount-owed">{{ Math.abs(account.sold) }}<span class="devise" v-html="devise"></span></td>
            <td class="account-debt-people">{{ account.empr[0].empr_nom }} {{ account.empr[0].empr_prenom  }}</td>
            <td class="account-debt-action">
                <template v-if="!account.isFrozen">
                    <input :id="'payment_'+id" name="payment" type="checkbox" value="1" @change="accountChecked"/>
                    <label :for="'payment_'+id">
                        {{ messages.get("payments", "pay") }}
                    </label>
                </template>
                <template v-else>
                    <label>
                        {{ messages.get("payments", "frozen") }}
                    </label>
                </template>
            </td>
        </tr>
    </tbody>
</template>

<script>
	export default {
		props : ["account", "id", "group", "devise"],

		data: function () {
            return {
                hover: -1
            }
		},
        methods: {
        	accountChecked: function(){
		    	this.$emit("accountchecked", {"accountId" : this.id, "group" : this.group})
		    },
        }
	}
</script>