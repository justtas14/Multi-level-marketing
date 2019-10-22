<template>
    
</template>

<script>
    import '../css/Login.scss'
    import { mapActions, mapMutations, mapState, mapGetters } from 'vuex'

    export default {
        name: "Login",
        components: {
        },
        props: [],
        data() {
            return {

            }
        },
        methods: {

            ...mapActions('security', [
            ]),
            ...mapMutations('security', [
            ])
        },
        computed: {
            ...mapGetters('security', {
            }),
            ...mapState('security', {
            })
        },
        created() {

        }
    }
</script>

<style scoped>

</style>