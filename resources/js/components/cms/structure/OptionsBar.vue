<template>
  <nav class="options-nav">
    <div class="cms-title-label">
        <a href="/cms/dashboard" class="app-name" aria-label="Go to CMS dashboard">{{ $page.props.cms.site_name }}</a>
        <button class="cms-options-toggle" @click="menuOpen = !menuOpen"><font-awesome-icon :icon="['fas', menuOpen ? 'xmark' : 'bars']" /></button>
    </div>
    <ul class="cms-options-menu" v-show="menuOpen">
        <li>
            <button @click="toggle('pages')" :aria-expanded="this.expanded.pages" :aria-controls="'section-pages'" class="option accordion-toggle">
                Pages
                <span aria-hidden="hidden"><font-awesome-icon :icon="this.expanded.pages ? ['fas', 'sort-up'] : ['fas', 'sort-down']" /></span>
            </button>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/primary"
                method="get"
                class="option nav-child"
            >
                Primary
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/secondary"
                method="get"
                class="option nav-child"
            >
                Secondary
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user && this.expanded.pages"
                href="/cms/pages/footer"
                method="get"
                class="option nav-child"
            >
                Footer
            </Link>
        </li>
        <li>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user"
                href="/cms/slides"
                method="get"
                class="option"
            >
                Slides
            </Link>
        </li>
        <li>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user"
                href="/cms/images"
                method="get"
                class="option"
            >
                Images
            </Link>
        </li>
        <li>
            <Link 
                @click="closeMenu"
                v-if="$page.props.auth.user"
                href="/cms/layouts"
                method="get"
                class="option"
            >
                Layouts
            </Link>
        </li>
        <li>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.blog && $page.props.auth.user"
                href="/cms/blog"
                method="get"
                class="option"
            >
                Blog
            </Link>
        </li>
        <li>
            <button @click="toggle('crm')" :aria-expanded="this.expanded.crm" :aria-controls="'section-crm'" class="option accordion-toggle">
                CRM
                <span aria-hidden="hidden"><font-awesome-icon :icon="this.expanded.crm ? ['fas', 'sort-up'] : ['fas', 'sort-down']" /></span>
            </button>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.listings && $page.props.auth.user && this.expanded.crm"
                href="/cms/crm/listings"
                method="get"
                class="option nav-child"
            >
                Listings
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.events && $page.props.auth.user && this.expanded.crm"
                href="/cms/crm/events"
                method="get"
                class="option nav-child"
            >
                Events
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.products && $page.props.auth.user && this.expanded.crm"
                href="/cms/crm/products"
                method="get"
                class="option nav-child"
            >
                Products
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.categories && $page.props.auth.user && this.expanded.crm"
                href="/cms/crm/categories"
                method="get"
                class="option nav-child"
            >
                Categories
            </Link>
            <Link 
                @click="closeMenu"
                v-if="$page.props.cms.coupons && $page.props.auth.user && this.expanded.crm"
                href="/cms/crm/coupons"
                method="get"
                class="option nav-child"
            >
                Coupons
            </Link>
        </li>

    </ul>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3';


export default {
    components: {
      Link,
    },
    data() {
        return {
            menuOpen : false,
            expanded: {
                pages: false,
            }
        }
    },
    methods: {
        toggle(navItem) {
            this.expanded[navItem] = !this.expanded[navItem]
        },
        closeMenu() {
            this.menuOpen = false;
        }
    },

}
</script>

<style scoped>
    nav {
        background-color: var(--cms-primary);
        height: 100%;
        .app-name {
            padding: 10px 20px;
            display: flex;
            font-weight: 600;
        }
        ul.cms-options-menu {
            display: none;
            flex-direction: column;
            li {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                border-top: 2px solid var(--cms-white);
                .option {
                    padding: 10px 20px;
                    width: 100%;
                    display: flex;
                    align-content: center;
                    justify-content: flex-start;
                }
                .option.nav-child {
                    border-top: 1px solid var(--cms-white);
                    padding-left: 30px;
                    background-color: var(--cms-secondary);
                }
                .accordion-toggle {
                    justify-content: space-between;
                }
            }
            li:first-of-type {
                border-top: 4px solid var(--cms-black);
            }
        }
    }

    .cms-options-toggle {
        display: flex;
    }

     @media (min-width: 64rem) {
        .cms-options-toggle {
            display: none;
        }
        ul.cms-options-menu {
            display: flex !important;
            flex-direction: column; 
        }
    }
</style>