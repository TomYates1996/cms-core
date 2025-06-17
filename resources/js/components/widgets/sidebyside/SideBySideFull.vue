<template>
    <WidgetStructure :widget="widget" :aspectRatios="aspectRatios"/>
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
            // Add the image ratios here 
            aspectRatios: [
                { width: 320, height: 220, at: 640 },
                { width: 480, height: 300, at: 1024 },
                { width: 1536, height: 960, at: 1440 },
            ],
        }
    },
}
</script>