<template>
<div class="modal-content">
  <div v-if="!showSlideList" class="tab-list" role="tablist" aria-label="Tabs for toggling between saved and new widgets">
    <button :class="newTab ? 'active cms-btn-alt no-hover' : 'inactive cms-btn-default'" @click.prevent="newTab = true" role="tab" :aria-selected="newTab ? 'true' : 'false'" aria-controls="new-tab-content" id="add-new-tab">Add New</button>
    <button :class="newTab ? 'inactive cms-btn-default' : 'active cms-btn-alt no-hover'" @click.prevent="newTab = false" role="tab" :aria-selected="newTab ? 'false' : 'true'" aria-controls="saved-tab-content" id="saved-options-tab">Saved Options</button>
  </div>
  <div v-if="newTab" class="tab-inners">
    <div v-if="showSlideList" class="slides-buttons" role="tablist" aria-label="Tabs for toggling between saved and new widgets">
      <button class="cms-btn-default" @click.prevent="addSlides()" role="button">Confirm Choice</button>
      <button class="cms-btn-default" @click.prevent="cancelSlides()" role="button">Back</button>
    </div>
    <form v-if="!showSlideList" class="form form-new-widget" aria-labelledby="select-widget-title">
      <fieldset class="form-inner">
        <legend id="select-widget-title" class="form-title">Add a New Widget</legend>
  
        <div class="widget-type-select form-field">
          <label for="widget-type">Select Widget Type</label>
          <select id="widget-type" v-model="newWidget.type" aria-required="true" required>
            <option v-for="widget in widgetOptions.filter(w => w.showWidget)" :key="widget.id" :value="{ name: widget.name, variant: widget.variant }">{{ widget.label }}</option>
          </select>
        </div>
        <div v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasHeader" class="widget-title form-field">
          <label for="widget-title">Title</label>
          <input id="widget-title" name="widget-title" type="text" v-model="newWidget.title" aria-required="false" />
        </div>
        <div v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasHeader" class="widget-subtitle form-field">
          <label for="widget-subtitle">Subtitle</label>
          <input id="widget-subtitle" name="widget-subtitle" type="text" v-model="newWidget.subtitle" aria-required="false" />
        </div>
        <div v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasHeader" class="widget-description form-field">
          <label for="widget-description">Description</label>
          <input id="widget-description" name="widget-description" type="text" v-model="newWidget.description" aria-required="false" />
        </div>
        <label for="widget-link" v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings">Link</label>
        <select id="widget-link" v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings" v-model="newWidget.link">
          <option value="">-- Select a link --</option>
          <optgroup v-for="(items, category) in pages" :key="category" :label="category">
            <option v-for="page in items" :key="page.id" :value="page.slug">
              {{ page.title }}
            </option>
          </optgroup>
        </select>
        <div v-if="newWidget.link && newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings" class="widget-link-text form-field">
          <label for="widget-link-text">Link Text</label>
          <input id="widget-link-text" name="widget-link-text" type="text" v-model="newWidget.link_text" aria-required="false" />
        </div>

        <div class="widget-slide-link-text form-field" v-if="newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings">
          <label for="widget-slide-link-text">Slide Link Text</label>
          <input id="widget-slide-link-text" name="widget-slide-link-text" type="text" v-model="newWidget.slide_link_text" aria-required="false" />
        </div>
         <div v-if="newWidget.type &&  widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings">
          <label><strong>Feed Type:</strong></label>
          <div class="feed-options-list" style="margin-top: 8px;">
            <label style="margin-right: 15px;">
              <input type="radio" value="slides" v-model="newWidget.feed_type" />
              Slides
            </label>
            <label style="margin-right: 15px;" v-if="$page.props.cms.blog">
                <input type="radio" value="blog" v-model="newWidget.feed_type" />
                  Blog
            </label>
            <label style="margin-right: 15px;" v-if="$page.props.cms.listings">
              <input type="radio" value="listings" v-model="newWidget.feed_type" />
              Listings
            </label>
            <label v-if="$page.props.cms.events"> 
              <input type="radio" value="events" v-model="newWidget.feed_type" />
              Events
            </label>
            <label v-if="$page.props.cms.products">
              <input type="radio" value="products" v-model="newWidget.feed_type" />
              Products
            </label>
          </div>
        </div>
        <section class="form-field" aria-labelledby="selected-slides-heading" v-if="newWidget.feed_type === 'slides' && newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings">
          <h2 id="selected-slides-heading" class="form-subtitle">Selected Slides</h2>
          <ul class="slide-list selected-slides">
            <li v-if="slides.filter(slide => slide.selected).length < 1">No slides</li>
            <li v-for="slide in slides.filter(slide => slide.selected)" :key="slide.id" class="slide-item" :aria-label="'Slide: ' + slide.title">
              <article class="slide-card">
                <img v-if="slide.image" class="slide-list-img" :src="'/storage/' + slide.image.image_path" :alt="slide.image.image_alt || ('Image for ' + slide.title)" />
                <div class="slide-info">
                  <h3 class="slide-title">{{ slide.title }}</h3>
                  <div class="slide-actions">
                    <!-- <button type="button" class="edit-slide" @click="editSlide(slide)" :aria-label="'Edit slide ' + slide.title" title="Edit Slide"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button> -->
                    <button type="button" class="remove-slide-btn" @click.prevent="removeSlide(slide)" :aria-label="'Remove slide ' + slide.title + ' from selection'" title="Remove Slide">Remove</button>
                  </div>
                </div>
              </article>
            </li>
          </ul>
        </section>
  
        <div class="form-field" v-if="newWidget.feed_type === 'slides' && newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasSettings">
          <button @click.prevent="showSlideListF()" class="cms-btn-default" aria-label="Open slide selector">Select Slides</button>
        </div>

        <!-- Category Selector for Grid Variants -->
      <div
        v-if="['listings_grid', 'events_grid', 'product_grid'].includes(newWidget.type?.variant)"
        class="widget-categories form-field"
      >
        <label for="widget-categories">Specific Categories</label>
        <select
          id="widget-categories"
          multiple
          v-model="newWidget.selected_categories"
          aria-required="false"
          style="min-width: 200px; min-height: 100px; background-color: 'red';"
        >
         <option v-for="category in categories.categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

        <div v-if="newWidget.feed_type !== 'slides' && newWidget.type && widgetOptions.find(option => option.variant === newWidget.type.variant)?.hasHeader" class="widget-slide-count form-field">
          <label for="widget-slide-count">Amount to show</label>
          <input
            id="widget-slide-count"
            name="widget-slide-count"
            type="number"
            v-model="newWidget.to_show"
            aria-required="false"
        />
        </div>

        <QuillEditor v-if="newWidget.type && newWidget.type.name === 'text'" v-model="newWidget.content" />
  
        <div class="form-actions">
          <button type="button" @click="addWidget()" class="cms-btn-default" aria-label="Add Widget">Add Widget</button>
          <button type="button" @click="cancelAdd()" class="cms-btn-default cancel-update-slide" aria-label="Cancel Adding Widget">Cancel</button>
        </div>
      </fieldset>
    </form>
  
    <div v-if="showSlideList" class="slide-box slide-popup">
     <ul class="slide-list">
      <li v-for="slide in slides.filter(s => !s.selected)" :key="slide.id" class="slide-item" :class="{ 'chosen': isChosen(slide) }"> 
          <article class="slide-card">
            <div class="slide-actions">
              <!-- <button type="button" class="edit-slide" @click="editSlide(slide)" :aria-label="'Edit slide ' + slide.title" title="Edit Slide"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button> -->
              <button type="button" class="add-slide-btn" @click.prevent="addSlide(slide)" :aria-label="'Add slide ' + slide.title + ' from selection'" title="Add Slide"><font-awesome-icon :icon=" isChosen(slide) ? ['fas', 'minus'] : ['fas', 'plus'] " /> </button>
            </div>
                <img v-if="slide.image" class="slide-list-img" :src="'/storage/' + slide.image.image_path" :alt="slide.image.image_alt || ('Image for ' + slide.title)" />
                <div class="slide-info">
                  <h3 class="slide-title">{{ slide.title }}</h3>
                </div>
            </article>
          </li>
          </ul>
          <!-- <button class="cms-btn-default" @click.prevent="showModal.newSlide = true">New Slide</button> -->
        </div>
      </div>
      <div v-if="!newTab" class="tab-inners">
        <ul class="saved-item-list" >
          <li v-for="(item, index) in savedWidgets" :key="index" class="saved-item">
            <button type="button" class="add-saved-element" @click.prevent="savedWidget(item)" aria-label="Add saved widget to page">
              <span class="visually-hidden">Widget -> {{ item.type }} - {{ item.name }}</span>
              <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
            </button>
            <button @click.prevent="deleteSaved(item)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
          </li>
        </ul>
      </div>
      <!-- <NewSlide v-if="showModal.newSlide" :pages="pages" :images="images" @refreshImages="getImages" @cancelNew="newSlide()"/> -->

</div>

</template>

<script>
import axios from 'axios';
import NewSlide from '../slides/NewSlide.vue';
import { widgetOptions } from '@/utils/widgetOptions.js';
import QuillEditor from '../reusable/QuillEditor.vue';

export default {
    components: {
      NewSlide,
      QuillEditor,
    },
    props: {
        slides: Array,
        page: Object,
        savedWidgets: Array,
        pages: Object,
      },
      data() {
        return {
          widgetOptions,
          newWidget: {
            feed_type : 'slides',
            to_show : 2,
            selected_categories: [],
          },
          showModal: {
              newSlide: false,
          },
          categories: [],
          newTab: true,
          showSlideList: false,
          chosenSlides: [],
          images: [],
        }
    },
    emits: [
        'cancelAdd',    
        'addHeader',  
        'addWidget',  
        'getImages',    
        'deleteSaved'   
    ],
    mounted() {
      this.getCategories();
    },
    methods: {
      // getImages() {
      //       axios.get('/api/images/all')
      //       .then((response) => {
      //           this.images = response.data.images; 
      //       })
      //   },
      getCategories() {
          axios.get('/api/categories/index') 
            .then(res => {
              this.categories = res.data;
            })
            .catch(err => {
              console.error("Failed to load categories", err);
            });
        },
        isChosen(slide) {
          return this.chosenSlides.some(c => c.id === slide.id);
        },
        addWidget() {
            this.newWidget.slides = this.slides.filter(slide => slide.selected);
            this.newWidget.variant = this.newWidget.type.variant;
            this.newWidget.type = this.newWidget.type.name;            
            this.$emit('addWidget', this.newWidget)
        },
        savedWidget(item) {
            this.newWidget = item;
            this.$emit('addWidget', this.newWidget)
        },
        deleteSaved(item) {
          if (confirm("Do you want to delete or unsave the widget?\n\nClick 'OK' to delete all, 'Cancel' to simply unsave.")) {
            axios.delete(`/widget/delete/${item.id}`)
                .then(() => {
                    let index = this.savedWidgets.findIndex(savedWidget => savedWidget.id === item.id);
                    if (index !== -1) {
                        this.savedWidgets.splice(index, 1); 
                    }
                })
                .catch(error => {
                    console.error('Failed:', error);
                });
          } else {
            axios.post(`/widget/unsave/${item.id}`)
                .then(() => {
                    item.is_saved = false;
                    let index = this.savedWidgets.findIndex(savedWidget => savedWidget.id === item.id);
                    if (index !== -1) {
                        this.savedWidgets.splice(index, 1); 
                    }
                })
                .catch(error => {
                    console.error('Failed:', error);
                });
            }
        },
        cancelAdd() {
            this.$emit('cancelAdd')
        },
        addSlides() {
            this.chosenSlides.forEach(el => {
              el.selected = true
            })
            this.newWidget.slides = this.slides.filter(slide => slide.selected);
            this.showSlideList = false;
        },
        cancelSlides() {
          this.showSlideList = false;          
          this.chosenSlides = [];
        },
        showSlideListF() {
          this.showSlideList = true;
          this.chosenSlides = this.slides.filter(slide => slide.selected);
        },
        addSlide(slide) {
          let index = this.chosenSlides.findIndex(s => s.id === slide.id);
          if (index === -1) {
            this.chosenSlides.push(slide); 
          } else {
            this.chosenSlides.splice(index, 1);
          }
        },
        removeSlide(slide) {
          slide.selected = false;
        },
    },
}
</script>

<style scoped>
.slide-list.selected-slides {
  border: 2px solid var(--black);
  border-radius: 6px;
  padding: 10px;
}

.slide-list {
  gap: 10px;
  display: flex;
  flex-direction: column;
  .chosen {
    background-color: #e4e4e4;
    border-radius: 5px;
  }
  li.slide-item {
    display: flex;
    padding: 5px;
    .add-slide-btn {
      display: flex;
      align-items: center;
      height: 100%;
    }

    .slide-list-img {
      height: 40px;
      width: 40px;
      border-radius: 5px;
    }

    .slide-card {
      display: flex;
      gap: 10px;

      .slide-info {
        display: flex;
        align-items: center;
        gap: 10px;

        .slide-actions {
          display: flex;
          gap: 10px;
        }
      }
    }
  }
}

.modal-content {
  padding: 20px;
}

.tab-list {
  display: flex;
  padding-left: 20px;
  gap: 2px;

  button {
    border-bottom: 0px;
    border-bottom-right-radius: 0px;
    border-bottom-left-radius: 0px;
  }
}

.tab-inners {
  border: 2px solid var(--black);
  border-radius: 6px;
  padding: 20px;

  .form-inner {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 10px;
    padding: 20px 0px 0px 0px;

    .form-title {
      font-size: 22px;
      font-weight: 600;
    }

    .widget-type-select {
      select {
        padding: 4px 10px;
      }
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

    .form-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      width: 100%;
      margin-top: 20px;
    }
  }
}

.slide-popup {
  padding: 20px 0px;
}

.slides-buttons {
  display: flex;
  gap: 10px;
}

</style>