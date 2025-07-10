<template>
  <button @click="toggle()" class="toggle-btn">
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
    <span :class="toggled ? 'toggle-yes toggle' : 'toggle-no toggle'"></span>
  </button>
    <nav class="hamburger-nav" v-if="toggled">
    <div v-if="isMobile" class="hamburger-inner mobile">
        <ul class="mobile-nav">
        <li v-for="page in pages" :key="page.id">
            <div :class="openChild === page.id ? 'nav-item open' : 'nav-item' ">
                <a :href="'/' + page.slug">{{ page.title }}</a>
                <button v-if="page.children" class="more-btn" @click="toggleChild(page)">
                    <font-awesome-icon :icon="['fas', openChild === page.id ? 'minus' : 'plus']" />
                </button>
            </div>

            <ul v-if="openChild === page.id" class="child-nav">
            <li v-for="child in page.children" :key="child.id">
                <div class="nav-item">
                    <a :href="'/' + child.slug">{{ child.title }}</a>
                    <button v-if="child.children" class="more-btn" @click="toggleGrandchild(child)">
                        <font-awesome-icon :icon="['fas', openGrandchild === child.id ? 'minus' : 'plus']" />
                    </button>
                </div>

                <ul v-if="openGrandchild === child.id" class="grandchild-nav">
                <li v-for="grandchild in child.children" :key="grandchild.id">
                    <a :href="'/' + grandchild.slug">{{ grandchild.title }}</a>
                </li>
                </ul>
            </li>
            </ul>
        </li>
        </ul>
    </div>

    <div v-else class="hamburger-inner desktop">
        <ul class="col-1">
        <li v-for="page in pages" :key="page.id">
            <a :href="'/' + page.slug">{{ page.title }}</a>
            <button v-if="page.children" class="more-btn" @click="showChildDropdown(page)">
            <font-awesome-icon :icon="['fas', 'plus']" />
            </button>
        </li>
        </ul>
        <ul v-if="openChild" class="col-2 dropdown level-2">
        <li v-for="child in openDropdown" :key="child.id">
            <a :href="'/' + child.slug">{{ child.title }}</a>
            <button v-if="child.children" class="more-btn" @click="showGrandchildDropdown(child)">
            <font-awesome-icon :icon="['fas', 'plus']" />
            </button>
        </li>
        </ul>
        <ul v-if="openGrandchild" class="col-3 dropdown level-3">
        <li v-for="grandchild in openCol3Dropdown" :key="grandchild.id">
            <a :href="grandchild.slug">{{ grandchild.title }}</a>
        </li>
        </ul>
    </div>
    </nav>

</template>

<script>
export default {
    props: {
        pages: Array,
    },
    data() {
        return {
            toggled: false,
            openChild: false,
            openDropdown: null,
            openGrandchild: false,
            openCol3Dropdown: null,
            isMobile: false,
        }
    },
    created() {
    },
    mounted() {
        this.checkViewport();
        window.addEventListener('resize', this.checkViewport);
    },
        beforeDestroy() {
        window.removeEventListener('resize', this.checkViewport);
    },
    methods: {
        toggle() {
            this.toggled = !this.toggled;
        },
        checkViewport() {
            this.isMobile = window.innerWidth <= 640; 
        },
        toggleChild(page) {
            this.openChild = this.openChild === page.id ? null : page.id;
            this.openGrandchild = null;
        },
        toggleGrandchild(child) {
            this.openGrandchild = this.openGrandchild === child.id ? null : child.id;
        },
        showChildDropdown(page) {
            if (this.openDropdown === page.children) {
                this.openDropdown = null;
                this.openChild = false;
                this.openCol3Dropdown = null;
                this.openGrandchild = false;
            } else {
                this.openDropdown = page.children;
                this.openChild = true;
                this.openGrandchild = false;
            }
        },
        showGrandchildDropdown(child) {
            if (this.openCol3Dropdown === child.children) {
                this.openCol3Dropdown = null;
                this.openGrandchild = false;
            } else {
                this.openCol3Dropdown = child.children;
                this.openGrandchild = true;
            }
        },
        
    },
}
</script>

<style scoped>
    .toggle-btn {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        cursor: pointer;
        flex-direction: column;
        gap: 4px;
        padding: 10px;
        z-index: 1000;
    }
    .toggle {
        transition: all 0.2s;
        position: relative;
        height: 2px;
        width: 18px;
        background-color: var(--black);
    }
    .toggle-yes {
        background-color: var(--white);
    }
    .toggle-yes:first-of-type {
        transform: rotate(45deg) translate(4px, 4px);
    }
    .toggle-yes:nth-of-type(2) {
        width: 0px;
        background-color: var(--white);
    }
    .toggle-yes:nth-of-type(3) {
        transform: rotate(-45deg) translate(4px, -4px);
    }
    .hamburger-nav {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        max-height: 100vh;
        background-color: var(--primary-colour);
        z-index: 101;
        overflow: scroll;
        .hamburger-inner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 40px;
            padding-top: 100px;
            gap: 10px;
            max-width: var(--width-max);
            margin: 0px auto;
            min-height: 100vh;
            background-color: var(--primary-colour);
            height: 100%;
            ul li {
                display: flex;
                width: 100%;
                background-color: var(--secondary-colour);
                border: 1px solid var(--white);
                border-bottom: none;
                @media (hover:hover) {
                    &:hover {
                        background-color: var(--tertiary-colour); 
                        a {
                            color: var(--text-tertiary);
                            text-decoration: none;
                        }
                        .more-btn {
                            color: var(--text-tertiary);
                        }
                    }
                }
                a {
                    padding: 10px 20px;
                    width: 100%;
                }
                .more-btn {
                    padding: 0px 10px;
                }
            }
            li:last-of-type {
                border-bottom: 1px solid var(--white);
            }
        }
    }
    .hamburger-nav.mobile {
        position: absolute;
        top: 100%;
        border-radius: 0px 0px 10px 10px;
        .hamburger-inner {
            display: flex;
            flex-direction: column;
            padding: 0px 20px 20px 20px;
            min-height: unset;
            ul.mobile-nav {
                li {
                    flex-direction: column;
                    .nav-item {
                        width: 100%;
                        display: flex;
                        font-weight: 600;
                    }
                    .nav-item.open {
                        background-color: var(--tertiary-colour);
                        color: var(--white);
                    }
                    .child-nav {
                        li {
                            .nav-item {
                                font-weight: 500;
                                padding-left: 14px;
                                background-color: var(--secondary-colour);
                                color: var(--black);
                            }
                            .child-nav {
                                li {
                                    .nav-item {
                                        padding-left: 14px;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>