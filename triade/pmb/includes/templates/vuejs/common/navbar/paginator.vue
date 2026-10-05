<template>
    <div class="navbar">
        <nav class="pagination" v-if="total > 1 && nbPages > 1">
            <button type="button" class="pagination-bouton-nav" :disabled="currentPage <= 1" @click="switchPage(1)">&laquo;</button>
            <button type="button" class="pagination-bouton-nav" :disabled="currentPage <= 1" @click="switchPage(currentPage - 1)">&#139;</button>

            <button type="button"
                :class="currentPage == pageNumber ? 'bouton-page active' : 'bouton-page'"
                v-for="(pageNumber, index) in pages" :key="index"
                :disabled="[currentPage, '...'].includes(pageNumber)"
                @click="switchPage(pageNumber)">
                    {{ pageNumber }}
            </button>

            <button type="button" class="pagination-bouton-nav" :disabled="currentPage >= nbPages" @click="switchPage(currentPage + 1)">&#155;</button>
            <button type="button" class="pagination-bouton-nav" :disabled="currentPage >= nbPages" @click="switchPage(nbPages)">&raquo;</button>
        </nav>
    </div>
</template>

<script>

    export default {
        name: "paginator",
        props : {
            url: {
                type: String,
                required: false,
                default: () => ''
            },
            total: {
                type: Number,
                required: true
            },
            currentPage: {
                type: Number,
                required: true
            },
            nbPerPage: {
                type: Number,
                required: false,
                default: () => 10
            },
        },
        computed: {
            nbPages: function () {
                if (this.nbPerPage) {
                    return Math.ceil(this.total / this.nbPerPage);
                }
                return 1;
            },
            distance: function () {
                if (this.nbPages <= 4 && this.currentPage <= 4 ) {
                    return 2;
                }
                return 3;
            },
            start: function () {
                let start = this.currentPage - this.distance;
                if (start <= 2) {
                    start = 2;
                }

                return start;
            },
            end: function () {
                let end = this.currentPage + this.distance;
                if (end >= this.nbPages) {
                    end = this.nbPages - 1;
                }

                return end;
            },
            pages: function () {
                if (this.nbPages <= 1) {
                    return [];
                }

                if (4 < this.nbPages) {
                    if (this.currentPage < 4) {
                        return this.computePages(2, 4, false, true);
                    } else if (this.nbPages - 3 < this.currentPage) {
                        return this.computePages(this.nbPages - 3, this.nbPages - 1, true, false);
                    } else {
                        return this.computePages(this.start, this.end, true, true);
                    }
                } else {
                    return this.computePages(this.start, this.end);
                }
            }
        },
        methods: {
            computePages: function (start, end, ellipsisStart = false, ellipsisEnd = false) {
                let pages = [1];

                if (ellipsisStart) {
                    pages.push('...');
                }

                for (let page = start; page <= end; page++) {
                    pages.push(page);
                }

                if (ellipsisEnd) {
                    pages.push('...');
                }
                pages.push(this.nbPages);

                return pages;
            },
            switchPage: async function (pageNumber) {
                if ([this.currentPage, '...'].includes(pageNumber)) {
                    return false;
                }

                if (this.currentPage <= 0 || this.nbPages < this.currentPage) {
                    return false;
                }

                if (this.url != '') {
                    try {
                        if ((typeof this.showLoader) === 'function') {
                            this.showLoader();
                        }

                        let formData = new FormData();
                        formData.append('data', JSON.stringify({
                            page: pageNumber
                        }));

                        const result = await fetch(this.url, {
                            method: 'POST',
                            body: formData
                        })
                        const response = await result.json();
                        if (this.validStructure(response)) {
                            this.$emit('switchPage', response);
                        }

                        if ((typeof this.hiddenLoader) === 'function') {
                            this.hiddenLoader();
                        }
                    } catch (e) {
                        console.error(e)
                    }
                } else {
                    // c'est la composant parent qui gere la recuperation des instances
                    this.$emit('switchPage', { page: pageNumber });
                }
            },
            validStructure: function (data) {
                if (!data.currentPage || !data.total || !data.nbPerPage) {
                    return false;
                }
                if (!(data.instances instanceof Array)) {
                    return false;
                }
                return true;
            }
        }
    }
</script>