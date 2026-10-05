<template>
	<div id="paymentsList">
		<h2>{{ messages.get("payments", "my_accounts") }}</h2>
		<table class="account-table">
			<caption>{{ messages.get("payments", "my_accounts") }}</caption>
			<thead class="account-table-head">
				<tr>
					<th class="account-title">{{ messages.get("payments", "account") }}</th>
					<th class="sold-title">{{ messages.get("payments", "sold") }}</th>
				</tr>
			</thead>
			<accountline v-for="(account, key) in accounts" :key="key" :account="account" :index="key" :id="account.id"
				:devise="devise" />
		</table>

		<h2>{{ messages.get("payments", "debt") }}</h2>
		<table class="account-table">
			<caption>{{ messages.get("payments", "debt") }}</caption>
			<thead class="account-table-head">
				<tr class="account-debt-group">
					<th class="debt-account-title">{{ messages.get("payments", "account") }}</th>
					<th class="debt-account-owed-title">{{ messages.get("payments", "amount_owed") }}</th>
					<th class="debt-account-people-title">{{ messages.get("payments", "people") }}</th>
					<th class="debt-account-action-title"></th>
				</tr>
			</thead>

			<template v-if="0 == groupmember.length">
				<debtline v-if="0 > account.sold" v-for="(account, keyDeb) in accounts" :key="keyDeb" :account="account"
					:id="account.id" :devise="devise" :group="false" @accountchecked="accountChecked" />
			</template>
			<template v-else v-for="(member, keyMember) in groupmember">
				<debtline v-for="(account, keyAccount) in member.accounts" :key="`${keyMember}-${keyAccount}`"
					:account="account" :id="account.id" :devise="devise" :group="true" @accountchecked="accountChecked" />
			</template>

			<tfoot class="account-table-foot">
				<tr>
					<td class="debt-account-total-title" colspan="3">{{ messages.get("payments", "total") }}</td>
					<td class="debt-account-sold-title">{{ balanceToPay }} <span class="devise" v-html="devise"></span></td>
				</tr>
			</tfoot>
		</table>
		<div class="row actions">
			<button type="button" class="btn-pay" @click="payToWin">
				{{ messages.get("payments", "pay") }}
			</button>
		</div>
		<template v-if="0 != transactionlist.length">
			<h2>{{ messages.get("payments", "transaction_list") }}</h2>
			<table class="account-table">
				<caption>{{ messages.get("payments", "transaction_list") }}</caption>
				<thead class="account-table-head">
					<tr>
						<th class="account-title">{{ messages.get("payments", "order_number") }}</th>
						<th class="sold-title">{{ messages.get("payments", "order_number_date") }}</th>
						<th class="sold-title">{{ messages.get("payments", "order_number_status") }}</th>
					</tr>
				</thead>
				<transactionline v-for="(transaction, key) in transactionlist" :key="key" :transaction="transaction" :index="key" />
			</table>
		</template>
	</div>
</template>

<script>

import accountline from "./accountLine.vue";
import debtline from "./debtLine.vue";
import transactionline from "./transactionLine.vue";

export default {
	props: ["accounts", "emprid", "sold", "groupmember", "devise", "transactionlist"],

	data: function () {
		return {
			action: "list",
			accountList: [],
			balanceToPay: 0,
			group: false
		}
	},
	computed: {
		totalSoldClass: function () {
			let classCss = "center";
			classCss += 0 > this.sold ? ' loss-price' : '';
			return classCss;
		},
		pay: function () {
			let sold = 0;
			for (let idAccount of this.accountList) {
				sold += this.getAccountSold(idAccount);
			}
			return Math.abs(sold);
		}
	},
	methods: {
		accountChecked: function (event) {
			this.group = event.group;
			let accountId = event.accountId;

			if (this.group) {
				for (let i = 0; i < this.groupmember.length; i++) {
					for (let index in this.groupmember[i].accounts) {
						if (accountId == this.groupmember[i].accounts[index].id) {
							var account = this.groupmember[i].accounts[index];
							break;
						}
					}
					if (account) {
						break;
					}
				}
			} else {
				var account = this.accounts.find(elem => accountId == elem.id);
			}

			if (this.accountList.includes(accountId)) {
				let accountIndex = this.accountList.findIndex(index => accountId == index);
				this.accountList.splice(accountIndex, 1);
				this.balanceToPay -= Math.abs(account.sold);
			} else {
				this.accountList.push(accountId);;
				this.balanceToPay += Math.abs(account.sold);
			}
		},
		getAccountSold: function (idAccount) {
			if (this.group) {
				for (let i = 0; i < this.groupmember.length; i++) {
					for (let index in this.groupmember[i].accounts) {
						if (idAccount == this.groupmember[i].accounts[index].id) {
							var accountFind = this.groupmember[i].accounts;
							break;
						}
					}
					if (accountFind) {
						break;
					}
				}
			} else {
				var accountFind = this.accounts.find(account => account.id == idAccount);
			}
			return accountFind.sold ?? 0;
		},
		payToWin: function () {

			// On ne donne pas la possibilite tout de suite de payer
			return;
			
			if (!this.balanceToPay || 0 >= this.balanceToPay) {
				return;
			}

			if (!confirm(messages.get("payments", "transaction_payment"))) {
				return;
			}

			let data = new FormData();
			data.append('paymentData', JSON.stringify({
				"accounts": this.accountList,
				"emprId": this.emprid
			}));
			fetch("./ajax.php?module=ajax&categ=payments&action=update_status", {
				method: 'POST',
				body: data
			}).then((response) => {
				if (response.ok) {
					response.text().then((result) => {
						result = JSON.parse(result);
						if (result.success) {
							// Déclenchement du moyen de paiement
							// https://www.payfip.gouv.fr/tpa/paiement.web?numcli=######&exer=####&refdet=######&objet=######&montant=#######&mel=#####@####.##&urlcl=######&saisie=T
							let url = "http://www.localhost/pmb_75/devel/payfip.php?numcli=72000&exer=2023&refdet=" + result.transactionNumber + "&objet=paiement&montant=" + parseInt(this.balanceToPay) + "&mel=toto@sigb.net&urlcl=http://localhost/pmb_75/opac_css/rest.php/payments/response/&saisie=T";
							document.location = url;
						} else {
							window.location.reload(alert("une erreur est survenue"));
						}
					});
				} else {
					console.error("error ajax");
				}
			}).catch((error) => {
				console.error("error catch");
			});
		}
	},
	components: {
		accountline,
		debtline,
		transactionline,
	}
}
</script>