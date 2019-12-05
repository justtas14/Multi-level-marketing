<template>
    <tr class="searchBar-container"
    v-bind:style="{'border-bottom': associatesLength > 0 ? '1px solid rgba(0,0,0,0.12)' : 'unset'}">
        <th width="20%" class="searchBar-inputContainer">
            <md-field>
                <label>Name</label>
                <md-input @input="handleNameSearchInput" v-model="nameInputVal"></md-input>
            </md-field>
        </th>
        <th width="20%" class="searchBar-inputEmailContainer">
            <md-field>
                <label>Email</label>
                <md-input @input="handleEmailSearchInput" v-model="emailInputVal"></md-input>
            </md-field>
        </th>
        <th width="20%" class="searchBar-inputPhoneContainer">
            <md-field>
                <label>Phone</label>
                <md-input @input="handlePhoneSearchInput" v-model="phoneInputVal"></md-input>
            </md-field>
        </th>
        <th width="10%" class="searchBar-inputLevelContainer cantInput">Level</th>
        <th width="15%" class="searchBar-inputDateContainer cantInput">Join date</th>
        <th width="15%" class="searchBar-inputActionContainer cantInput">Action</th>
    </tr>
</template>

<script>
import { debounce } from 'debounce';

export default {
    name: 'SearchBar',
    props: ['associatesLength'],
    data() {
        return {
            nameInputVal: '',
            emailInputVal: '',
            phoneInputVal: '',
        };
    },
    components: {

    },
    methods: {
        handleNameSearchInput: debounce(function nameSearchtFun() {
            this.$emit('handleSearch', 'name', this.nameInputVal);
        }, 500),
        handlePhoneSearchInput: debounce(function phoneSearchFun() {
            this.$emit('handleSearch', 'phone', this.phoneInputVal);
        }, 500),
        handleEmailSearchInput: debounce(function emailSearchFun() {
            this.$emit('handleSearch', 'email', this.emailInputVal);
        }, 500),
    },
    computed: {

    },

};
</script>

<style lang="scss" scoped>
    @import './css/SearchBar.scss';
</style>
