<template>
    <div class="edit-widget edit-footer-wrap">
        <h2>Edit Footer</h2>
        <button @click="imageList()" class="add-img">Logo</button>
        <div v-if="showImageGrid" class="image-grid">
            <img class="new-slide-img-option" @click="addImageToSlide(image, index)" v-for="(image, index) in images" :key="image.id" :src="'/storage/' + image.image_path" alt="">
            <button @click="showNewImage = !showNewImage" :class="showNewImage ? 'toggle-new-image new-image-open cancel-new-image' : 'toggle-new-image'"" aria-label="New image"><font-awesome-icon :icon="['fas', 'plus']" /></button>
            <NewImage v-if="showNewImage" @refreshImages="getImages()"/>
            <button class="cancel-new-image new-layout" v-if="showNewImage" @click="showNewImage = false">Cancel New Image</button>
        </div>
        <button class="cancel-new-image cancel-image-grid new-layout" v-if="showImageGrid && !showNewImage" @click="showImageGrid = false">Back</button>
        <!-- Section Select -->
        <div class="form-slide-link form-field">
            <label for="footer-section">Section</label>
            <select id="footer-section" name="footer-section" v-model="footer.section" required aria-label="Select section for the footer" aria-required="true">
              <option value="primary">Primary</option>
              <option value="secondary">Secondary</option>
              <option value="footer">Footer</option>
            </select>
          </div>

          <ul v-if="footer.social_media">
            <li v-for="social in footer.social_media" :key="social.id">
              {{ social.label }}
              <font-awesome-icon :icon="['fab', social.icon]" />
              <button><font-awesome-icon :icon="['fas', 'trash']" @click="removeSocial(social)" /></button>
            </li>
          </ul>

          <button class="cms-btn-default" @click.prevent="showAdd = !showAdd">Add Social Media</button>
          <AddList v-if="showAdd" :items="socialMedia" :type="'social'" @created="$emit('created', $event)" @selected="addSocial"/>

        <!-- Footer Widget -->
        <div class="footer-cta">
            <ul class="current-ctas">
                <li class="cta-line" v-for="cta in footer.widgets" :key="cta.id">
                    <p>{{ cta.title }}</p>
                    <button class="cms-btn-default" v-if="!showAddCTA" @click.prevent="toggleShowEditCTA(cta)">{{ showEditCTA ? 'Cancel Edit' : 'Edit CTA' }}</button>
                    <button class="delete-cta" @click.prevent="removeCTA(cta)"><font-awesome-icon :icon="['fas', 'trash']" /></button>
                </li>
            </ul>
            <form v-if="showEditCTA" @submit.prevent="saveEditCTA()" class="edit-page-info form tab-inners" aria-labelledby="form-header">
                <fieldset class="form-inner">
                    <legend id="form-header" class="form-title">Edit CTA</legend>
                    <label for="title">Title: 
                        <input type="text" id="title" v-model="editCTA.title">
                    </label>
                    <label for="description">Description: 
                        <input type="text" id="description" v-model="editCTA.description">
                    </label>
                    <label for="link">Link: 
                        <input type="url" id="link" v-model="editCTA.link">
                    </label>
                    <button type="submit" class="cms-btn-default" aria-label="Add CTA">Save Changes</button>
                    <button type="button" @click="cancelAdd()" class="cms-btn-default cancel-update-slide cancel-cta" aria-label="Cancel Editing CTA">Cancel</button>
                </fieldset>
            </form>
            <button v-if="!showEditCTA" class="cms-btn-default" @click.prevent="showAddCTA = !showAddCTA">{{ showAddCTA ? 'Cancel CTA' : 'Add CTA' }}</button>
            <form v-if="showAddCTA" @submit.prevent="addCTA()" class="edit-page-info form tab-inners" aria-labelledby="form-header">
                <fieldset class="form-inner">
                    <legend id="form-header" class="form-title">Add a New CTA</legend>
                    <label for="title">Title: 
                        <input type="text" id="title" v-model="newCTA.title">
                    </label>
                    <label for="description">Description: 
                        <input type="text" id="description" v-model="newCTA.description">
                    </label>
                    <label for="link">Link: 
                        <input type="url" id="link" v-model="newCTA.link">
                    </label>
                    <button type="submit" class="cms-btn-default" aria-label="Add CTA">Save CTA</button>
                    <button type="button" @click="cancelAdd()" class="cms-btn-default cancel-update-slide cancel-cta" aria-label="Cancel Adding CTA">Cancel</button>
                </fieldset>
            </form>
        </div>
  
        <button @click="saveEdit()">Save Changes</button>
        <button @click="showAdd ? showAdd = false : cancelEdit()" class="cms-btn-default cancel-update-slide">{{ showAdd ? 'Back' : 'Cancel' }}</button>
    </div>
</template>
  
  <script>
import NewImage from '../cms/slides/images/NewImage.vue';
import AddList from '../cms/reusable/AddList.vue';
import axios from 'axios';

  export default {
    components: {
        NewImage,
        AddList,
    },
    props: {
        images: Array,
        footer: Object,
        socialMedia: Array,
        pages: Object,
    },
    data() {
        return {
            showImageGrid: false,
            showNewImage: false,
            showAdd: false,
            showAddCTA: false,
            showEditCTA: false,
            newCTA: {
                type : 'cta'
            },
            editCTA: {},
        }
    },
    methods: {
        // async addCTA() {
        //     const response = await axios.post('/api/store/cta', {
        //         footer_id: this.footer.id,
        //         title: this.newCTA.title,
        //         type: 'cta',
        //         description: this.newCTA.description,
        //     });
        //     this.footer.widgets.push(response.data.widget);
        //     this.showAddCTA = false;
        // },
        addCTA() {
            if (this.footer.widgets && this.footer.widgets.length > 0) {
                this.footer.widgets.push(this.newCTA);
            } else {
                this.footer.widgets = [this.newCTA];
            }
            // this.footer.widgets = 
            //     footer_id: this.footer.id,
            //     title: this.newCTA.title,
            //     type: 'cta',
            //     description: this.newCTA.description,
        },
        getImages() {
            this.showNewImage = false;
            this.$emit('getImages')
        },
        imageList() {
            this.getImages();
            this.showImageGrid = true;
        },
        addImageToSlide(image, index) {
            this.showNewImage = false;
            this.footer.logo = this.images[index];
            this.showImageGrid = false;
        },
        saveEdit() {
            this.$emit('saveEdit')
        },
        cancelEdit() {
            this.$emit('cancelEdit')
        },
        addSocial(item) {
            this.showAdd = false;
            if (this.footer.social_media) {
                this.footer.social_media.push(item);
            } else {
                this.footer.social_media = [item]
            }
        },
        removeSocial(social) {
            let index = this.footer.social_media.findIndex(item => item === social);
            if (index !== -1) {
                this.footer.social_media.splice(index, 1);
            }
        },
        toggleShowEditCTA(cta) {
            this.editCTA = cta;
            this.showEditCTA = !this.showEditCTA;
        },
        saveEditCTA() {
            const index = this.footer.widgets.findIndex(el => el.id === this.editCTA.id);
            if (index !== -1) {
                this.footer.widgets.splice(index, 1, { ...this.editCTA });
            }
            this.showEditCTA = false;
        },
        removeCTA(ctaIndex) {
            this.footer.widgets.splice(ctaIndex, 1);
        },
    },
    watch: {
        'footer.section'(newValue, oldValue) {
            this.footer.pages = this.pages[newValue];
        }
    },
}
</script>
  
  <style>
    
  </style>