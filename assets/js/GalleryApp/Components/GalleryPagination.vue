<template>
    <ul class="pagination">
        <li v-if="paginationInfo.currentPage == 1" class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
        <li v-else class="waves-effect"><a @click="previousPage"><i class="material-icons">chevron_left</i></a></li>
        <span id="pages">
            <li v-for="n in paginationInfo.numberOfPages"  v-bind:class="[n == paginationInfo.currentPage ? activePage: notActivePage]">
                <a v-if="n == paginationInfo.currentPage" href="#!">{{ paginationInfo.currentPage }}</a>
                <a v-else @click="page(n)" class="hrefPages">{{ n }}</a>
            </li>
        </span>
        <li v-if="paginationInfo.currentPage == paginationInfo.numberOfPages" id="disabled_right" class="disabled rightPage"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
        <li v-else id="enabled_right" class="waves-effect rightPage"><a @click="nextPage"><i class="material-icons">chevron_right</i></a></li>
    </ul>
</template>
<script>
    import EventBus from '../EventBus/EventBus';

    export default {
        name: 'Pagination',
        props: ['paginationInfo'],
        data() {
            return {
                activePage: 'active',
                notActivePage: 'waves-effect pages'
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

<style src="../css/GalleryPagination.css" scoped>

</style>