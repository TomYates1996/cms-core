<template>
    <footer>
        <div class="footer-top">
            <img v-if="footer.logo" class="footer-logo" :src="'/storage/' + footer.logo.image_path" :alt="footer.logo.image_alt">
            <ul>
                <li v-for="page in footer.pages" :key="page.id">
                    <a :href="'/' + page.slug">{{ page.title }}</a>
                </li>
            </ul>
            <div class="right-upper">
                <div v-if="footer.widgets" class="widgets">
                    <div class="cta" v-for="widget in footer.widgets" :key="widget.id">
                        <a :href="widget.link">
                            <h3>{{ widget.title }}</h3>
                            <p>{{ widget.description }}</p>
                        </a>
                    </div>
                </div>
                <ul class="social-links" v-if="footer.social_media">
                    <li v-for="social in footer.social_media" :key="social.id">
                        <a :href="social.link" :title="social.label">
                            <font-awesome-icon :icon="['fab', social.icon]" />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-attributions">
            <div class="attributions-inner">
                <p>&copy;TomCorp 2025</p>
                <p>Site made using TomCMS</p>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    props: {
        footer: Object,
        pages: Object,
    },
    data() {
        return {
            footerPages : [],
        }
    },
    created() {
        if (this.footer.social & !this.footer.social_media) {
            this.footer.social_media = this.footer.social;
        } else if(this.footer.social & this.footer.social_media) {
            this.footer.social_media.concat(this.footer.social)
        }
        this.footer.pages = this.pages[this.footer.section];
    },
}
</script>

<style scoped>
footer {
    display: flex;
    flex-direction: column;
    margin-top: auto;
    background-color: var(--secondary-colour);
    .footer-top {
        display: flex;
        flex-direction: column;
        gap: 14px;
        justify-content: space-between;
        align-items: start;
        padding: 20px;
        max-width: var(--width-max);
        margin: 0px auto;
        width: 100%;
        ul li a {
            font-weight: 600;
        }
        .footer-logo {
            max-width: 200px;
            height: auto;
        }
        .right-upper {
            display: flex;
            flex-direction: column;
            .social-links {
                display: flex;
                gap: 8px;
                grid-column: 4;
                font-size: 20px;
                margin-top: 14px;
            }
        }
        @media screen and (min-width: 40em) {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        @media screen and (min-width: 64em) {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .footer-attributions {
        background-color: var(--primary-colour);
        .attributions-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-primary);
            padding: 10px 20px;
            max-width: var(--width-max);
            margin: 0px auto;
            font-size: 13px;
            p:last-of-type {
                text-align: end;
            }
        }
    }
}
</style>