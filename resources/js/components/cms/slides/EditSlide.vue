<template>
    <form class="edit-page-info form" @submit.prevent="updateSlide()" aria-label="Update Slide">
    <fieldset class="form-inner">
        <legend class="form-title">Update Slide</legend>

        <div class="form-slide-title form-field">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required v-model="form.title" autofocus aria-required="true" />
        </div>

        <div class="form-slide-description form-field">
        <label for="description">Description</label>
        <input id="description" name="description" type="text" required v-model="form.description" aria-required="true" />
        </div>

        <div class="form-slide-link form-field">
            <label>Link</label>

            <!-- Static Display Mode -->
            <div v-if="!editMode">
                <span>
                <!-- Show title if it's a page slug, otherwise show raw link -->
                {{ linkType === 'page' ? getPageTitle(form.link) || form.link : form.link }}
                </span>
                <button type="button" @click="editMode = true" class="ml-2 text-blue-500 underline">Change</button>
            </div>

            <!-- Edit Mode -->
            <div v-else>
                <!-- Toggle between page and custom -->
                <div class="mb-2">
                <label>
                    <input type="radio" value="none" v-model="linkType" />
                    No Link
                </label>
                <label>
                    <input type="radio" value="page" v-model="linkType" />
                    Select a page
                </label>
                <label class="ml-4">
                    <input type="radio" value="custom" v-model="linkType" />
                    Enter custom URL
                </label>
                </div>

                <!-- Page selection -->
                <div v-if="linkType === 'page'">
                <select v-model="form.link" @change="editMode = false">
                    <option disabled value="">Please select a page</option>
                    <option v-for="page in pages" :key="page.id" :value="'/' + page.slug">
                    {{ page.title }}
                    </option>
                </select>
                </div>

                <!-- Custom URL input -->
                <div v-if="linkType === 'custom'">
                <input
                    type="text"
                    v-model="form.link"
                    placeholder="https://example.com"
                    @blur="editMode = false"
                />
                </div>
            </div>
        </div>

        <div class="form-slide-image form-field">
        <button type="button" class="cms-btn-default add-img" @click.prevent="imageList()" 
                aria-expanded="showImageGrid.toString()" aria-controls="imageGrid">
            Select Image
        </button>
        </div>

        <div v-if="imagePreview" class="form-field image-preview">
        <p class="sr-only">Preview Image</p>
        <img :src="imagePreview" alt="Selected image preview" />
        </div>

        <div v-if="showImageGrid" id="imageGrid" class="image-grid" aria-label="Select an image for the slide">
        <img v-for="image in images" :key="image.id" class="new-slide-img-option"
            :src="'/storage/' + image.image_path" alt="Image option" role="button" tabindex="0"
            @click.prevent="addImageToSlide(image)" />
        <NewImage @refreshImages="refreshImages()" />
        </div>

        <button type="submit" class="cms-btn-default" tabindex="5"
                :disabled="form.processing" :aria-busy="form.processing">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Update Slide
        </button>

        <button type="button" class="cms-btn-default cancel-update-slide"
                @click.prevent="closeUpdateSlide()" aria-label="Cancel slide update">
        Cancel
        </button>
    </fieldset>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import NewImage from '@/components/cms/slides/images/NewImage.vue';

export default {
    components: {
        LoaderCircle,
        NewImage,
    },
    props: {
        slide: Object,
        images: Array,
        pages: Array,
    },
    setup() {
        const form = useForm({
            title: '',
            description: '',
            link: null,
            image_id: null,
            image_alt: '',
            id: undefined,
        });

        return { form };
    },
    data() {
        return {
            imagePreview: null, 
            showImageGrid: false, 
            linkType: 'none',
            editMode: false,
        };
    },
    created() {
        this.form.title = this.slide.title;
        this.form.description = this.slide.description;
        this.form.link = this.slide.link;
        this.form.image_id = this.slide.image_id;
        this.form.image_alt = this.slide.image_alt;
        this.form.id = this.slide.id;

        if (this.slide.image_path) {
            this.imagePreview = `${this.slide.image_path}`;
        }
    },
    methods: {
        getPageTitle(slug) {
            const match = this.pages.find(p => p.slug === slug);
            return match ? match.title : null;
        },
        refreshImages() {
            this.$emit('refreshImages');
        },
        imageList() {
            this.showImageGrid = !this.showImageGrid;
        },
        addImageToSlide(image) {
            this.form.image_id = image.id;
            this.form.image_path = image.image_path;
            this.form.image_alt = image.image_alt;

            this.showImageGrid = false;
            this.imagePreview = `/${image.image_path}`;
        },
        uploadImage(event) {
            this.form.image = event.target.files[0];
            this.updatePreview(this.form.image);
        },
        updateSlide() {
            if (this.linkType === 'none') {
                this.form.link = null;
            }
            this.form.put(route('api.slides.update', this.form.id), {
                onSuccess: () => {
                    this.$inertia.visit('/cms/slides');
                },
                onError: (errors) => {
                    console.log('Form submission error:', errors);
                },
            });
        },
        updatePreview(imageFile) {
            const file = imageFile;
            if (file) {
                const reader = new FileReader();
                reader.onloadend = () => {
                    this.imagePreview = reader.result;
                };
                reader.readAsDataURL(file);
            }
        },
        closeUpdateSlide() {
            this.form.reset();
            this.$emit('cancelEdit');
        },
    },
};
</script>

<style scoped>
/* Styles for image grid */
.image-grid {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background-color: var(--white);
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    padding: 20px;
    overflow: scroll;
}

.new-slide-img-option {
    width: 100%;
    object-fit: cover;
    aspect-ratio: 1/1;
}

.image-preview {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

.image-preview img {
    max-width: 200px;
    object-fit: contain;
}

.form-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    padding: 20px 0px;
    .form-title {
        font-size: 22px;
        font-weight: 600;
    }
    .form-field {
        display: flex;
        gap: 10px;
        align-items: center;
        label {
            font-size: 18px;
        }
    }
    input {
        border: 1px solid var(--black);
        padding: 4px;
        border-radius: 2px;
    }
    .form-wrap {
        flex-direction: column;
        align-items: flex-start;
        .link-upper {
            display: flex;
            gap: 20px;
            div label:last-of-type {
                margin-left: 20px;
            }
        }
        .link-option {
            display: flex;
            gap: 10px;
            align-items: center;
            select {
                padding: 4px;
            }
        }
    }
}
</style>
