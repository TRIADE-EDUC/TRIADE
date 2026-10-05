<template>
	<tbody class="account-debt-group">
        <tr class="account-debt-line">
            <td class="account-debt-name">{{ transaction.order_number }}</td>
            <td class="account-debt-amount-owed">{{ transaction.payment_date }}</td>
            <td class="account-debt-amount-owed">
                <template v-if="transaction.payment_status == 1">{{ messages.get("payments", "order_number_status_in_progress") }}</template>
                <template v-if="transaction.payment_status == 2">{{ messages.get("payments", "order_number_finish") }}</template>
                <template v-else>{{ messages.get("payments", "order_number_abandoned") }}</template>
            </td>
        </tr>
    </tbody>
</template>

<script>
	export default {
		props : ["transaction"],

		data: function () {
            return {
                hover: -1
            }
		},
	}
</script>