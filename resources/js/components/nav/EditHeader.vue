<template>
    <div class="edit-widget form tab-inners">
      <fieldset class="form-inner">
        <legend class="form-title">Edit Header</legend>
        <div class="form-slide-link form-field">
          <label for="header-link">Link</label>
          <input id="header-link" name="header-link" type="text" v-model="header.link" required aria-required="true" />
        </div>
        <div class="form-slide-image form-field">
          <button type="button" @click="imageList()" class="cms-btn-default add-img" aria-label="Select a logo image">Logo</button>
          <div v-if="showImageGrid" class="image-grid" role="grid" aria-labelledby="image-grid-label">
            <span id="image-grid-label" class="sr-only">Select an image</span>
            <img v-for="(image, index) in images" :key="image.id" class="new-slide-img-option" @click="addImageToSlide(image, index)" :src="'/storage/' + image.image_path" :alt="'Select ' + image.name" role="gridcell" />
            <button @click="showNewImage = !showNewImage" :class="showNewImage ? 'toggle-new-img new-image-open cancel-new-image' : 'toggle-new-img'" aria-label="New image"><font-awesome-icon :icon="['fas', 'plus']" /></button>
            <NewImage v-if="showNewImage" @refreshImages="getImages" />
            <button class="cancel-new-image new-layout" v-if="showNewImage" @click="showNewImage = false">Cancel New Image</button>
        </div>
        <button class="cancel-new-image cancel-image-grid new-layout" v-if="showImageGrid && !showNewImage" @click="showImageGrid = false">Back</button>
        </div>
        <div class="form-slide-link form-field">
            <label for="menu_type">Menu Type</label>
            <select id="menu_type" name="menu type" v-model="header.menu_type" required aria-label="Select menu type for the header" aria-required="true">
                <option value="dropdown">Dropdown</option>
                <option value="hamburger">Hamburger</option>
            </select>
        </div>
        <div class="form-slide-link form-field">
            <label for="section">Section</label>
            <select id="section" name="section" v-model="header.section" required aria-label="Select section for the header" aria-required="true">
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
                <option value="footer">Footer</option>
            </select>
        </div>
        <div class="form-slide-link form-field" v-if="header.menu_type === 'hamburger'">
            <label for="section-hamburger">Hamburger Section</label>
            <select id="section-hamburger" name="section hamburger" v-model="header.section_hamburger" required aria-label="Select section for the hamburger" aria-required="true">
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
                <option value="footer">Footer</option>
            </select>
        </div>
        <div class="form-actions btn-row">
          <button type="button" @click="saveEdit()" class="cms-btn-default" aria-label="Save header changes">Save Changes</button>
          <button type="button" @click="cancelEdit()" class="cms-btn-default cancel-update-slide" aria-label="Cancel editing header">Cancel</button>
        </div>
      </fieldset>
    </div>
  </template>
  
  
  <script>
import NewImage from '../cms/slides/images/NewImage.vue';

  export default {
    components: {
        NewImage
    },
    props: {
        images: Array,
        header: Object,
    },
    data() {
        return {
            showImageGrid: false,
            showNewImage: false,
        }
    },
    methods: {
        getImages() {
            this.showNewImage = false;
            this.$emit('getImages');
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image, index) {
            this.showNewImage = false;
            this.header.logo = this.images[index];
            this.showImageGrid = false;
        },
        saveEdit() {
            this.$emit('saveEdit')
        },
        cancelEdit() {
            this.$emit('cancelEdit')
        },
      },
  }
</script>
  
<style scoped>
    .edit-widget {
        padding: 20px;
    }
    
</style>