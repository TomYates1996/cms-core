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
            // Add image aspect ratios 
            aspectRatios: [
                { width: 600, height: 360, at: 640 },
            ],
        }
    },
}
</script>