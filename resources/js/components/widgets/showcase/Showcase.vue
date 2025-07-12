<template>
    <WidgetStructure :widget="widget" :aspectRatios="aspectRatios" :mosaicRatios="mosaicRatios"/>
</template>

<script>
import WidgetStructure from '../components/WidgetStructure.vue';


export default {
    components: {
        WidgetStructure,
    },
    props: {
        widget: Object,
    },
    data() {
        return {
            // Add image aspect ratios 
            aspectRatios: [
                { width: 369, height: 228, at: 640 },
            ],
            mosaicRatios: [
                { width: 94, height: 192, at: 640 },
            ],
        }
    },
}
</script>