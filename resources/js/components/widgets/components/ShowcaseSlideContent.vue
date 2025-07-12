<template>
    <div class="item laptop">
        <div class="image-section">
            <ResponsiveImage :slide="widget.slides[0]" :aspectRatios="aspectRatios"/>
        </div>
        <TextContent :slide="widget.slides[0]" :widget="widget"/>
    </div>
    <div class="item mobile" v-if="widget.slides.length > 1">
        <div class="image-section">
            <ResponsiveImage :slide="widget.slides[1]" :aspectRatios="mosaicRatios"/>
        </div>
        <TextContent :slide="widget.slides[1]" :widget="widget"/>
    </div>
</template>

<script>
import ResponsiveImage from '../components/ResponsiveImage.vue';
import TextContent from '../components/TextContent.vue';

export default {
    components: {
        TextContent,
        ResponsiveImage,
    },
    props: {
        widget: Object,
        aspectRatios: Array,
        mosaicRatios: Array,
    },

}
</script>

<style scoped>
    .image-section {
        display: flex;
        img {
        }
    }
</style>