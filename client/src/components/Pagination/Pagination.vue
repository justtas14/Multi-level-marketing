<template>
    <ul class="pagination">
        <li v-if="paginationInfo.currentPage == 1" class="disabled">
            <a href="#!"><i class="material-icons">chevron_left</i></a>
        </li>
        <li v-else class="waves-effect"><a @click="previousPage">
            <i class="material-icons">chevron_left</i></a>
        </li>
        <li v-if="paginationInfo.currentPage - 1 > numberOfPagesToShow">
            <a @click="page(1)" class="hrefPages firstPage">{{ 1 }}</a>
        </li>
        <li v-if="paginationInfo.currentPage - 1 > numberOfPagesToShow+1"
             class="paginationDots">...</li>
        <span id="pages">
            <li v-for="n in paginationInfo.numberOfPages" :key="n"
              v-bind:class="[n == paginationInfo.currentPage ? activePage: notActivePage]">
                <a v-if="n == paginationInfo.currentPage" href="#!">
                    {{ paginationInfo.currentPage }}
                </a>
                <a v-else-if="(Math.abs(paginationInfo.currentPage-n) < numberOfPagesToShow+1)"
                 @click="page(n)" class="hrefPages">{{ n }}
                 </a>
            </li>
        </span>
        <li
        v-if="(paginationInfo.numberOfPages - paginationInfo.currentPage > numberOfPagesToShow+1)"
         class="paginationDots">...</li>
        <li v-if="paginationInfo.numberOfPages - paginationInfo.currentPage > numberOfPagesToShow">
            <a @click="page(paginationInfo.numberOfPages)"
                class="hrefPages lastPage">{{ paginationInfo.numberOfPages }}
            </a>
        </li>
        <li v-if="paginationInfo.currentPage == paginationInfo.numberOfPages"
         id="disabled_right" class="disabled rightPage">
             <a href="#!"><i class="material-icons">chevron_right</i></a>
         </li>
        <li v-else id="enabled_right" class="waves-effect rightPage">
            <a @click="nextPage"><i class="material-icons">chevron_right</i></a>
        </li>
    </ul>
</template>
<script>
import EventBus from './EventBus/EventBus';

export default {
    name: 'Pagination',
    props: ['paginationInfo'],
    data() {
        return {
            activePage: 'active',
            notActivePage: 'waves-effect pages',
            numberOfPagesToShow: 3,
        };
    },
    methods: {
        previousPage() {
            EventBus.$emit('previousPage');
        },
        nextPage() {
            EventBus.$emit('nextPage');
        },
        page(n) {
            EventBus.$emit('page', n);
        },
    },
};
</script>

<style lang="scss" scoped>
    @import './css/Pagination.scss';
</style>
