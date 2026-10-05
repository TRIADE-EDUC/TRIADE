<template>
    <div>
        <paginatorfilter v-if="filterFields" 
            :list="list" 
            :fields="filterFields"
            @filter="setFilter"></paginatorfilter>
        <span v-if="nbResultDisplay"><b>{{ Object.keys(filteredList).length }}</b> {{messages.get('paginator', "paginator_found_results")}}</span>
        <slot name="content" :list="displayedList"></slot>
        <nav class="pagination" v-if="pageCount > 1">
            <button type="button" :name="paginationPrevious" class="pagination-bouton-nav" @click="currentPage=1">&laquo;</button>
            <button type="button" :name="paginationPrevious" class="pagination-bouton-nav" @click="currentPage--">&#139;</button>

            <button type="button" :class="currentPage == pageNumber ? 'bouton-page active' : 'bouton-page'" v-for="(pageNumber, index) in displayedPages" :key="index" @click="currentPage=pageNumber"> {{pageNumber}} </button>

            <button type="button" :name="paginationNext" class="pagination-bouton-nav" @click="currentPage++">&#155;</button>
            <button type="button" :name="paginationNext" class="pagination-bouton-nav" @click="currentPage=pageCount">&raquo;</button>
        </nav>
    </div>
</template>

<script>
import paginatorfilter from "./paginatorFilter.vue";
import messages from "../helper/Messages.js";

    export default {
        props : ["list", "perPage", "startPage", "nbPage", "filterFields", "nbResultDisplay"],
        components: { 
        	paginatorfilter 
       	},
        data: function () {
            return {
                pages: [],
                currentPage: this.startPage,
                paginationNext : "",
                paginationPrevious : "",
                filteredList : this.list,
                messages: messages
            }
        },
        computed: {
            pageCount: function() {
                return Math.ceil(this.filteredList.length / this.perPage);
            },
            displayedList: function() {
                return this.paginate(this.filteredList);
            },
            displayedPages: function() {
                return this.pages.slice(this.startPaginatorIndex(), this.endPaginatorIndex());
            }
        },
        created : function() {
            this.init();
        },
        mounted: function() {
            this.$set(this, "filteredList", this.list);
            this.setPages();
            this.displayNav();
        },
        watch: {
            list() {
                this.pages = [];
                this.currentPage = this.startPage;
                this.setPages();
                this.displayNav();
            },
            currentPage() {
                this.displayNav();
            },
            list : function(newList) {
                this.setFilter(newList);
            }
        },
        methods: {
            setPages: function() {
                let numberOfPages = Math.ceil(this.filteredList.length / this.perPage);
                for (let i=1; i<=numberOfPages; i++) {
                    this.pages.push(i);
                }
            },
            paginate: function(list) {
                let from = (this.currentPage * this.perPage) - this.perPage;
                let to = (this.currentPage * this.perPage);

                return list.slice(from, to);
            },
            startPaginatorIndex() {
                let startIndex = (Math.floor(this.currentPage / this.nbPage) * this.nbPage);
                if (startIndex >= this.currentPage) {
                    startIndex = (Math.floor((this.currentPage - 1) / this.nbPage) * this.nbPage);
                }
                return startIndex;
            },
            endPaginatorIndex() {
                let endIndex = (Math.ceil(this.currentPage / this.nbPage) * this.nbPage);
                if (endIndex >= this.pageCount) {
                    endIndex = this.pageCount;
                }
                return endIndex;
            },
            displayNav: function() {
                let nextElements = document.getElementsByName(this.paginationNext);
                let previousElements = document.getElementsByName(this.paginationPrevious);

                nextElements.forEach((element) => {
                    element.disabled = false;
                	if(!(this.currentPage < this.pageCount)) {
	                    element.disabled = true;
	                    this.currentPage = this.pageCount;
                	}
                });

                previousElements.forEach((element) => {
                    element.disabled = (this.currentPage == 1);
                });
            },
            init : function() {
                this.paginationNext = this.getButtonName("pagination-next-");
                this.paginationPrevious = this.getButtonName("pagination-previous-");
                this.$root.$on("filter", (list) => {
                    this.filteredList = list;
                });
            },
            getButtonName : function(button) {
                let i = 0;
                while(document.getElementsByName(button + i).length > 0) {
                    i++;
                }
                return button + i;
            },
            setFilter : function(list) {
                this.$set(this, "filteredList", list);
            	this.displayNav();
            }
        }
    }
</script>