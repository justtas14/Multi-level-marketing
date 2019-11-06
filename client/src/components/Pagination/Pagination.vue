<template>
    <ul class="pagination">
        <li v-if="paginationInfo.currentPage == 1" class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
        <li v-else class="waves-effect"><a @click="previousPage"><i class="material-icons">chevron_left</i></a></li>
        <li v-if="paginationInfo.currentPage - 1 > numberOfPagesToShow"><a @click="page(1)" class="hrefPages firstPage">{{ 1 }}</a></li>
        <li v-if="paginationInfo.currentPage - 1 > numberOfPagesToShow+1" class="paginationDots">...</li>
        <span id="pages">
            <li v-for="n in paginationInfo.numberOfPages"  v-bind:class="[n == paginationInfo.currentPage ? activePage: notActivePage]">
                <a v-if="n == paginationInfo.currentPage" href="#!">{{ paginationInfo.currentPage }}</a>
                <a v-else-if="(Math.abs(paginationInfo.currentPage-n) < numberOfPagesToShow+1)" @click="page(n)" class="hrefPages">{{ n }}</a>
            </li>
        </span>
        <li v-if="(paginationInfo.numberOfPages - paginationInfo.currentPage > numberOfPagesToShow+1)" class="paginationDots">...</li>
        <li v-if="paginationInfo.numberOfPages - paginationInfo.currentPage > numberOfPagesToShow">
            <a @click="page(paginationInfo.numberOfPages)" class="hrefPages lastPage">{{ paginationInfo.numberOfPages }}</a>
        </li>
        <li v-if="paginationInfo.currentPage == paginationInfo.numberOfPages" id="disabled_right" class="disabled rightPage"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
        <li v-else id="enabled_right" class="waves-effect rightPage"><a @click="nextPage"><i class="material-icons">chevron_right</i></a></li>
    </ul>
</template>
<script>
    import EventBus from "./EventBus/EventBus";
    import './css/Pagination.scss';

    export default {
        name: 'Pagination',
        props: ['paginationInfo'],
        data() {
            return {
                activePage: 'active',
                notActivePage: 'waves-effect pages',
                numberOfPagesToShow: 3
            }
        },
        methods: {
            previousPage: function() {
                EventBus.$emit('previousPage');
            },
            nextPage: function() {
                EventBus.$emit('nextPage');
            },
            page: function (n) {
                EventBus.$emit('page', n);
            }
        }
    }
</script>

<style scoped>
</style>