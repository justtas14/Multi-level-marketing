<template>
    <div>
        <div id="rootExplorer"
                :data-source="getPath()"
                data-details=""
                >
        </div>
    </div>
</template>

<script>
import './css/explorer_main.css';
import Parameters from '../../../parameters';

export default {
    name: 'RootExplorer',
    components: {

    },
    props: ['path'],
    methods: {
        getPath() {
            return `${Parameters.API_HOST_URL}${this.path}`;
        },
        hideViewer(node) {
            const ua = window.navigator.userAgent;
            const msie = ua.indexOf('MSIE ');
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv:11./)) {
                if (node) {
                    node.id = 'no';
                    node.innerHTML = 'Downline explorer not supported in Internet explorer.';
                }
            }
        },
        appendAndRemoveScript(src) {
            const explorerScript = document.createElement('script');
            explorerScript.setAttribute('src', src);
            const contains = document.querySelector(`script[src="${src}"]`);
            if (contains) {
                contains.remove();
            }
            document.head.appendChild(explorerScript);
        },

    },
    computed: {

    },
    mounted() {
        const node = document.querySelector('div#rootExplorer');
        this.hideViewer(node);
        this.appendAndRemoveScript('/assets/js/explorer_main.js');
    },
    created() {
        this.appendAndRemoveScript('/assets/js/explorer_2.js');
        this.appendAndRemoveScript('/assets/js/explorer_runtime_main.js');
    },
};
</script>

<style lang="scss" scoped>
</style>
