<template>
    <div class="page-wrap crm-page">
        <section class="page-left">
            <NewCategory v-if="showNewCategory" @cancel-new="showNewCategory = false" id="newCategoryForm" />
            <NewSubcategory :forText="currentCategory.name" :parentId="currentCategory.id" v-if="showNewSubCategory" @subcategory-created="addNewSubcategory" id="newSubCategoryForm" />
            
            <div class="category-list-wrap crm-list-wrap">
                <div class="crm-header-wrap">
                    <h1 v-if="!showNewCategory && !showNewSubCategory" class="crm-header">{{ showSubCategories ? `Sub Categories - ${currentCategory.name}` : 'Categories' }}</h1>
                    <button v-if="!showSubCategories" class="new-layout" @click="showNewCategory = !showNewCategory" :aria-expanded="showNewCategory.toString()" aria-controls="newCategoryForm">
                        {{ showNewCategory ? 'Cancel' : 'New Category' }}
                    </button>
                    <div v-if="showSubCategories" class="new-sub-cat-wrap double-btn">
                        <button  class="new-layout" @click="showNewSubCategory = !showNewSubCategory" :aria-expanded="showNewSubCategory.toString()" aria-controls="newSubCategoryForm">
                            {{ showNewSubCategory ? 'Cancel' : 'New Sub Category' }}
                        </button>
                        <button class="new-layout" @click="closeSubcatList()" aria-label="Back to Categories">
                            Back
                        </button>
                    </div>
                </div>

                <ul v-if="!showNewCategory && !showNewSubCategory" class="category-list crm-list">
                    <li v-for="category in useCategories" :key="category.id" class="category-list-item crm-list-item">
                        <a :href="`/category/${category.slug}`">{{ category.name }}</a>
                        <button v-if="$page.props.auth.user && !showSubCategories" class="option" @click="toggleShowSubCategories(category.id)" :aria-label="`Show subcategories of: ${category.name}`" title="Show Subcategories">
                        <font-awesome-icon :icon="['fas', 'children']" />
                        </button>
                        <button v-if="$page.props.auth.user" class="option" @click="showSubCategories ? deleteSubcat(category.id) : deleteCategory(category.id)" :aria-label="`Delete category: ${category.name}`" title="Delete category">
                        <font-awesome-icon :icon="['fas', 'trash-can']" />
                        </button>
                    </li>
                    <li class="no-items" v-if="useCategories.length < 1">
                        <p>No Categories</p>
                    </li>
                </ul>
            </div>
        </section>
    </div>
</template>

  

<script>
import NewCategory from '@/components/crm/NewCategory.vue';
import NewSubcategory from '@/components/crm/NewSubcategory.vue';
import CMSLayout from '@/layouts/CMSLayout.vue';
import { Link, router } from '@inertiajs/vue3';

export default {
    layout: CMSLayout,
    components: {
        Link,
        NewCategory,
        NewSubcategory,
    },
    props: {
        categories: Array,
    },
    data() {
        return {
            showNewCategory : false,
            showNewSubCategory : false,
            showSubCategories : false,
            currentCategory: null,
            localCategories: [],
            localSubcategories: [],
            useCategories: [],
        }
    },
    created() {
        this.localCategories = this.categories;
        this.useCategories = this.localCategories;
    },
    methods: {
        newListing() {
            this.showNewCategory = true;
        },
        toggleShowSubCategories(id) {
            this.currentCategory = this.categories.find(
                (cat) => cat.id === id
            );
            
            this.localSubcategories = this.currentCategory.subcategories
            this.useCategories = this.localSubcategories
            this.showSubCategories = true;
        },
        closeSubcatList() {
            this.useCategories = this.localCategories;
            this.showSubCategories = false;
            this.currentCategory = null;
        },
        addNewSubcategory(newSub) {
            this.localSubcategories.push(newSub);
            this.showNewSubCategory = false;
        },
        deleteCategory(category_id) {
            if (confirm("Are you sure you want to delete this category?")) {
                router.delete(`/cms/crm/category/delete/${category_id}`, {
                onSuccess: () => {
                    this.localCategories = this.localCategories.filter(item => item.id !== category_id);
                    if (!this.showSubCategories) {
                        this.useCategories = this.localCategories;
                    }
                },
                onError: (errors) => {
                    console.log('Error deleting category:', errors);
                }
                });
            }
        },
        deleteSubcat(category_id) {
            if (confirm("Are you sure you want to delete this subcategory?")) {
                router.delete(`/cms/crm/subcategory/delete/${category_id}`, {
                onSuccess: () => {
                    this.localSubcategories = this.localSubcategories.filter(item => item.id !== category_id);
                    if (this.showSubCategories) {
                        this.useCategories = this.localSubcategories;
                    }
                },
                onError: (errors) => {
                    console.log('Error deleting subcategory:', errors);
                }
                });
            }
        },
    },
}
</script>

<style scoped>
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
    .category-list {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 6px;
        padding-top: 10px;
        .category-list-item {
            width: 100%;;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>