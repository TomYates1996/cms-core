<template>
    <div class="text-section">
        <h3 v-if="slide.link" class="slide-title">
            <a :href="slide.link">{{ slide.title }}</a>
        </h3>
        <h3 v-if="!slide.link" class="slide-title">
            {{ slide.title }}
        </h3>
        <p v-if="slide.price_range">{{ slide.price_range }}</p>
        <p class="slide-desc">{{ slide.description.length > 100 ? slide.description.slice(0, 100) + '...' : slide.description }}</p>
        <a v-if="slide.link && widget.slide_link_text" :href="slide.link" class="btn-read-more">    
            {{ widget.slide_link_text }}
        </a>
    </div>
</template>

<script>
export default {
    props: {
        slide: Object,
        widget: Object,
    },
}
</script>

<style>
    .text-section {
        padding: 10px 0px;
        display: flex;
        gap: 10px;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        .slide-title {
            font: var(--slide-title-default);
        }
        .slide-desc {
            font: var(--slide-desc-default);
        }
        .btn-read-more {
            display: flex;
        }
    }
</style>