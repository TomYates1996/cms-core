<template>
    <div class="page-wrap">
        <div class="page-left">
            <div class="crm-header-wrap">
                <h1 class="crm-header">Pages - {{ section }}</h1>
                <div class="double-btn">
                    <button v-if="!showModal.newLink" @click="toggleNew()" :aria-expanded="showModal.new.toString()" :aria-controls="'newPageForm'">
                        {{ showModal.new ? 'Cancel' : 'New Page' }}
                    </button>
                    <button v-if="!showModal.new" @click="toggleNewLink()" :aria-expanded="showModal.newLink.toString()" :aria-controls="'newPageLinkForm'">
                        {{ showModal.newLink ? 'Cancel' : 'New Page Link' }}
                    </button>
                </div>
            </div>
            <table v-if="!showModal.edit.details && !showModal.new && !showModal.newLink" class="page-list" role="table" aria-label="Page List">
                <thead>
                    <tr class="table-head">
                        <th scope="col">Details</th>
                        <th scope="col">Title</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Show in Nav</th>
                        <th scope="col">Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="page in pages" :key="page.id" class="page-item">
                        <td class="options-section" aria-label="Actions for page">
                            <button v-if="$page.props.auth.user && !page.is_link" class="option" @click="editDetails(page)" title="Edit details" aria-label="Edit details for {{ page.title }} page"><font-awesome-icon :icon="['fas', 'keyboard']" /></button>
                            <Link v-if="$page.props.auth.user && !page.is_link" :href="`/cms/pages/edit-content/${page.slug}`" title="Edit content" method="get" class="option" role="button" aria-label="Edit content for {{ page.title }}"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></Link>
                            <Link v-if="$page.props.auth.user && !page.is_link" :href="`/cms/pages/children/${page.slug}`" method="get" title="View page children" class="option" role="button" aria-label="View children for {{ page.title }}"><font-awesome-icon :icon="['fas', 'children']" /></Link>
                            <button v-if="$page.props.auth.user" class="option" @click="deletePage(page.id)" title="Delete page" aria-label="Delete {{ page.title }} page"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
                        </td>
                        <td><a :href="'/' + page.slug" class="page-title" title="Visit page" aria-label="Visit page: {{ page.title }}">{{ page.title }}</a></td>
                        <td><a :href="'/' + page.slug" class="page-slug" title="Visit page" aria-label="Visit {{ page.title }}">{{ page.slug }}</a></td>
                        <td class="page-slug" aria-label="Show in nav">{{ page.show_in_nav ? 'Yes' : 'No' }}</td>
                        <td aria-label="Type">{{ page.is_link ? 'Link' : 'Page' }}</td>
                    </tr>
                </tbody>
            </table>

            <EditPage @cancelNew="toggleEdit" v-if="showModal.edit.details" :page="currentPage"/>
            <NewPage v-if="showModal.new" :parent="parent" :section="section" @cancelNew="toggleNew"/>
            <NewPageLink v-if="showModal.newLink" :parent="parent" :section="section"/>
        </div>
    </div>
</template>
  
<script>  
import { Head, Link } from '@inertiajs/vue3';
import EditPage from '@/components/cms/pages/EditPage.vue';
import NewPage from '@/components/cms/pages/NewPage.vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3'
import CMSLayout from '@/layouts/CMSLayout.vue';
import NewPageLink from '@/components/cms/pages/NewPageLink.vue';

  
export default {
    layout: CMSLayout,
    components: {
        Link,
        EditPage,
        NewPage,
        NewPageLink,
    },
    props: {
        pages: Array,
        parent: Object,
        section: String,
    },
    data() {
        return {
            showModal: {
                edit: {},
                new: false,
                newLink: false,
            },
            showForm : false,
            currentPage: {},       
        }
    },
    computed: {
        parentUrl() {
            let slug = '';
            if (this.parent.level === 1) {
                slug = `/cms/pages/${this.parent.section}`;
                return slug;
            } else {
                const segments = this.parent.slug.split('/').filter(Boolean); // split and remove empty strings
                segments.pop();
                segments.join('/');
                slug = `/cms/pages/children/${segments}`;
                return slug;
            }
        }
    },
    // mounted() {
    //     this.fetchPages();
    // },
    methods: {
        deletePage(page_id) {
            if (confirm("Are you sure you want to delete this page? All child pages will also be deleted.")) {
                router.delete(`/cms/pages/delete/${page_id}`, {
                onSuccess: () => {
                    // this.$inertia.get('/cms/pages'); 
                },
                onError: (errors) => {
                    console.log('Error deleting page:', errors);
                }
                });
            }
        },
        editDetails(page) {
            this.currentPage = page;
            this.showModal.edit.details = true;
            this.showModal.new = false;
        },
        toggleNew() {
            this.showModal.new = !this.showModal.new;
            this.showModal.edit.details = false;
        },
        toggleNewLink() {
            this.showModal.newLink = !this.showModal.newLink;
            this.showModal.edit.details = false;
            this.showModal.new = false;
        },
        toggleEdit() {
            this.showModal.edit.details = false;
        },
    }
}
</script>  
  
<style>
.page-list {
    width: 100%;
    tr, td {
        text-align: left;
    }
    .options-section {
        display: flex;
        gap: 6px;
    }

}
.table-head {
    border-bottom: 1px solid var(--black);
}
.page-wrap {
    display: flex;
    gap: 20px;
    .page-left {
        width: 80%;
    }
    .page-right {
        width: 20%;
        border-left: 2px solid var(--black);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
}
</style>