
<template>
    <Head :title="page.title">
    </Head>

    <div class="front-page-wrapper" :class="pageClass">
        <HamburgerHeader :header="header" :allPages="pages" :pages="header.pages" :link="header.link" :logo="header.logo"/>
    
        <component v-for="widget in widgets" :key="widget.id" 
            :is="widget.variant"
            :widget="widget"
        />
    
        <Footer v-if="footer" :footer="footer" :pages="pages" />
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
import axios from 'axios';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import Cards4 from '@/components/cms/pages/collections/cards/Cards4.vue';
import Footer from '@/components/nav/Footer.vue';
import { asyncWidgets } from '@/utils/asyncWidgets';

export default {
    components: {
        Head,
        Footer,
        HamburgerHeader,
        Cards4,
        defineAsyncComponent,
        ...asyncWidgets,
    },
    props: {
        widgets: Array,
        header: Object,
        footer: Object,
        pages: Object,
        page: Object,
    },
    data() {
        return {
            
        }
    },
    mounted() {
    },
    computed: {
        pageClass() {
            return this.page?.title?.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '') || ''
        }
    },
    methods: {
        // Get the list of pages
        // fetchPages() {
        //     axios.get('/api/pages')
        //     .then((response) => {
        //         this.pages = response.data.pages; 
        //     })
        //     .catch((error) => {
        //         console.error('Error fetching pages:', error);
        //     });
        //  }
    }
}
</script>