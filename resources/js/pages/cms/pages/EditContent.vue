<template>
  <div class="page-wrapper edit-content-page">
    <div class="page-left detail">
      <div class="main-buttons">
        <button v-if="!isBlog" @click="layout ? saveLayout() : savePage()" class="cms-btn-default">Save {{ layout ? 'layout' : 'page' }}</button>
        <button v-if="isBlog" @click="saveBlog()" class="cms-btn-default">Save blog post</button>
        <Link 
             v-if="$page.props.auth.user && !isBlog"
             :href="layout ? '/cms/layouts' : '/cms/pages/' + page.section"
             method="get"
             class="cms-btn-default"
          >
              Cancel Changes
          </Link>
        <Link 
             v-if="$page.props.auth.user && isBlog"
             :href="'/cms/blog'"
             method="get"
             class="cms-btn-default"
          >
              Cancel Changes
          </Link>
      </div>
      <div class="expanding">
        <button :class="['header-edit-sidebar-btn', { active: headerCmsVisible }]" @click="headerVisible()" v-if="!isBlog">Header</button>
        <button :class="['widgets-edit-sidebar-btn', { active: widgetsCmsVisble }]" @click="widgetsVisible()">Widgets</button>
        <button :class="['footer-edit-sidebar-btn', { active: footerCmsVisble }]" @click="footerVisible()" v-if="!isBlog">Footer</button>
      </div>
      <div class="expanded-content">
        <div v-if="headerCmsVisible" class="sidebar-option sidebar-header">
          <div v-if="localContent.header" class="widget-option">
              <p>Header - {{ localContent.header.section }}</p>
              <button @click="editHeader()" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
              <button @click="showSavedNav(savedHeaders , 'headers')" class="download-btn"><font-awesome-icon :icon="['fas', 'download']"/></button>
              <button @click="saveHeader(localContent.header, 'header')" class="save-btn"><font-awesome-icon :icon="localContent.header.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
          </div>
        </div>
        <div v-if="widgetsCmsVisble" class="sidebar-option sidebar-widgets">
          <div class="widget-options">
            <div v-for="(widget, index) in localContent.widgets" :key="index" class="widget-option">
                <p>{{ widget.label }}</p>
                <button @click="editElement('widgets', index)" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
                <div class="move-buttons">
                  <button @click="orderUp(index)" :disabled="index === 0" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-up']" /></button>
                  <button @click="orderDown(index)" :disabled="index === localContent.widgets.length - 1" class="edit-btn"><font-awesome-icon :icon="['fas', 'angle-down']" /></button>
                </div>
                <button @click="deleteElement('widgets', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
                <button @click="saveWidget(widget, 'widgets', index)" class="save-btn"><font-awesome-icon :icon="widget.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
                <button @click="cloneWidget(widget, 'widgets', index)" class="save-btn"><font-awesome-icon :icon="['fas', 'clone']" /></button>
            </div>
          </div>
          <button @click="openAddItem('widgets')" class="cms-btn-default">Add Widget</button>
        </div>
        <div class="sidebar-option sidebar-footer" v-if="!isBlog && footerCmsVisble">
          <div v-if="localContent.footer" class="widget-option">
            <p>Footer - {{ localContent.footer.section }}</p>
            <button @click="editFooter()" class="edit-btn"><font-awesome-icon :icon="['fas', 'pen-to-square']" /></button>
            <!-- <button @click="deleteElement('footers', index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button> -->
            <button @click="showSavedNav(savedFooters , 'footers')" class="download-btn"><font-awesome-icon :icon="['fas', 'download']"/></button>
            <button @click="saveHeader(localContent.footer, 'footer')" class="save-btn"><font-awesome-icon :icon="localContent.footer.is_saved ? ['fas', 'lock'] : ['fas', 'unlock']" /></button>
          </div>
        </div>
      </div>
            <!-- <EditSlide v-if="showEditSlide" :slide="slideToEdit"/> -->
          </div>
          <div class="page-right">
            <ul class="saved-item-list" v-if="showModal.saved[useType]">
              <li v-for="(item, index) in localSaved[useType]" :key="index" class="saved-item">
                <button type="button" class="add-saved-element" @click="addSaved(item, useType)" aria-label="Add saved {{ useType }} to page">
                  <span class="visually-hidden">{{ useType }} -> {{ item.type }} - {{ item.name }}</span>
                  <span aria-hidden="true"><font-awesome-icon :icon="['fas', 'plus']" /></span>
                </button>
                <button @click="deleteSavedElement(useType, index)" class="delete-btn"><font-awesome-icon :icon="['fas', 'trash-can']" /></button>
              </li>
            </ul>
            <!-- <button @click="closeWhatsOpen" class="close-open" v-if="anyTrue">| Close |</button> -->

              <SavedNavList v-if="showModal.saved.footers || showModal.saved.headers" :itemList="boxContent" :onClickAction="onClickAction" @footers="setNewFooter" @headers="setNewHeader" />

              <HamburgerHeader :header="localContent.header" v-if="!anyTrue && localContent.header" :allPages="pages" :pages="localContent.header.pages" :link="localContent.header.link" :logo="localContent.header.logo"/>
              <NewWidget v-if="showModal.new.widgets" :pages="pages" @deleteSaved="deleteSavedElement" :savedWidgets="localSaved.widgets" :slides="slides" @addWidget="addWidget" @cancelAdd="cancelAdd('widgets')" :page="page" />
              <EditWidget v-if="showModal.edit.widgets" :pages="pages" :widget="itemInfo" :slides="slides" @saveEdit="saveEdit" @cancelEdit="cancelEdit('widgets')"/>
              <EditHeader v-if="showModal.edit.header" :header="itemInfo" :images="images" @getImages="getImages()" @saveEdit="saveHeaderEdit()" @cancelEdit="cancelEdit('header')"/>
              <EditFooter v-if="showModal.edit.footer" :footer="itemInfo" :pages="pages" :socialMedia="localContent.socialMedia" @created="addSocialLink" :images="images" @saveEdit="saveFooter()" @cancelEdit="cancelEdit('footer')" @getImages="getImages()"/>
              <div v-if="!anyTrue" class="widget-container">
                <component v-for="widget in localContent.widgets" :key="widget.id" :is="widget.variant" :widget="widget"/>
              </div>
              <Footer v-if="!anyTrue && localContent.footer" :footer="localContent.footer" :pages="pages" />
        </div>
    </div>
  </template>
  
<script>
import { defineAsyncComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';
import EditSlide from '@/components/cms/slides/EditSlide.vue';
import EditWidget from '@/components/cms/widget/EditWidget.vue';
import NewWidget from '@/components/cms/widget/NewWidget.vue';
import HamburgerHeader from '@/components/nav/HamburgerHeader.vue';
import NewHeader from '@/components/nav/NewHeader.vue';
import EditHeader from '@/components/nav/EditHeader.vue';
import NewFooter from '@/components/nav/NewFooter.vue';
import EditFooter from '@/components/nav/EditFooter.vue';
import Footer from '@/components/nav/Footer.vue';
import SavedNavList from '@/components/nav/SavedNavList.vue';
import { asyncWidgets } from '@/utils/asyncWidgets';
import { widgetOptions } from '@/utils/widgetOptions.js';


export default {
    setup(){
        const formSlide = useForm({
            'title' : '',
            'image_path' : '',
            'image_alt' : '',
            'description' : '',
            'link' : '',
        });

        return { formSlide } 
    },
    props: {
        pages: Object,
        page: Object,
        widgets: Array,
        header: Object,
        footer: Object,
        savedWidgets: Array,
        savedHeaders: Array,
        savedFooters: Array,
        layout: Boolean,
        isBlog: {
          type: Boolean,
          default: false,
        }
    },
    components: {
      SavedNavList,
      Footer,
      EditFooter,
      NewFooter,
      EditHeader,
      NewHeader,
      HamburgerHeader,
      NewWidget,
      EditWidget,
      useForm,
      Link,
      EditSlide,
      defineAsyncComponent,
      ...asyncWidgets,
    },
    data() {
      return {
        footerCmsVisble: false,
        headerCmsVisible: false,
        widgetsCmsVisble: false,
        widgetOptions,
        showModal: {
          edit : {},
          new: {},
          saved: {},
        },
        showHeaderEdit: false,
        selectedWidgetType: '',  
        widgetTitle: '',
        selectedSlides: [],  
        slides: [],  
        widgetInfo: {},
        headerInfo: {},
        editIndex: {},
        showEditSlide: false,
        slideToEdit: {},
        images: [],
        showNewFooter: false,
        footerInfo: {},
        localContent: [],
        localSaved: [],
        itemInfo: {},
        useType: '',
        boxContent: [],
        onClickAction: '',
      };
    },
    created() {
        this.localContent.widgets = this.widgets;
        this.localContent.header = this.header;
        this.localContent.footer = this.footer;

        this.getWidgetLabel();
        this.getSocialMedia();
        
        this.localSaved.widgets = this.savedWidgets;
        this.localSaved.headers = this.savedHeaders;
        this.localSaved.footers = this.savedFooters;

        this.getImages();
    },
    computed: {
        // widgetOptions() {
        //     return this.$widgetOptions;
        // },
        anyTrue() {
          return Object.values(this.showModal).some(section =>
            Object.values(section).some(val => val === true)
          );
        },
   },
    methods: {
      headerVisible() {
        this.footerCmsVisble = false;
        this.widgetsCmsVisble = false;
        this.headerCmsVisible = true;
      },
      widgetsVisible() {
        this.footerCmsVisble = false;
        this.headerCmsVisible = false;
        this.widgetsCmsVisble = true;
      },
      footerVisible() {
        this.headerCmsVisible = false;
        this.widgetsCmsVisble = false;
        this.footerCmsVisble = true;
      },
      setNewFooter(footer) {
        this.localContent.footer = footer;
      },
      setNewHeader(header) {
        this.localContent.header = header;
      },
      showSavedNav(items, type) {
        this.showModal.saved.footers = true;
        this.boxContent = items;
        this.onClickAction = type;
      },
      addSocialLink(newItem) {
        this.localContent.socialMedia.push(newItem);
      },
      getSocialMedia() {
          axios.get('/api/social-media')
          .then(response => {
            this.localContent.socialMedia = response.data;
          })
          .catch(error => {
            console.error('Error fetching social media links:', error);
          });
      },
      getWidgetLabel() {
        this.localContent.widgets.forEach(widget => {
          let item = this.widgetOptions.find(option => option.variant === widget.variant);
          widget.label = item.label;
        })
      },
      getImages() {
            axios.get('/api/images/all')
            .then((response) => {
                this.images = response.data.images; 
                this.fetchSlides();   
            })
        },
      editSlide(slide) {
        this.showEditSlide = true;
        this.slideToEdit = slide;
      },
      orderUp(index) {
        if (index > 0) {
          [this.localContent.widgets[index], this.localContent.widgets[index - 1]] = [this.localContent.widgets[index - 1], this.localContent.widgets[index]];
        }
      },
      orderDown(index) {
        if (index < this.localContent.widgets.length - 1) {
          [this.localContent.widgets[index], this.localContent.widgets[index + 1]] = [this.localContent.widgets[index + 1], this.localContent.widgets[index]];
        }
      },
      editHeader() {
        if (this.localContent.header.is_saved === true) {
          if (confirm('this is a saved element and editing will update all instances of the item thoughout the site')) {
          } else {
            return;
          }
        }
          Object.keys(this.showModal.edit).forEach(key => {
            this.showModal.edit[key] = false;
          });
          this.showModal.edit.header = true;
          this.itemInfo = JSON.parse(JSON.stringify(this.localContent.header));
      },
      editFooter() {
        if (this.localContent.footer.is_saved === true) {
          if (confirm('this is a saved element and editing will update all instances of the item thoughout the site')) {
          } else {
            return;
          }
        }
          Object.keys(this.showModal.edit).forEach(key => {
            this.showModal.edit[key] = false;
          });
          this.showModal.edit.footer = true;
          this.itemInfo = JSON.parse(JSON.stringify(this.localContent.footer));
      },
      editElement(type, index) {
        if (this.localContent[type][index].is_saved === true) {
          if (confirm('this is a saved element and editing will update all instances of the item thoughout the site')) {
          } else {
            return;
          }
        }
          Object.keys(this.showModal.edit).forEach(key => {
            this.showModal.edit[key] = false;
          });
          this.showModal.edit[type] = true;
          this.itemInfo = JSON.parse(JSON.stringify(this.localContent[type][index]));
          if (type === "widgets") {
            // this.initialSlides.forEach (slide => {
            //   if (this.itemInfo.slides.some(infoSlide => infoSlide.id === slide.id)) {
            //     slide.selected = true;
            //     }
            // })
          }
          this.editIndex = index;
      },
      saveHeaderEdit() {
        this.itemInfo.pages = this.pages[this.itemInfo.section];
        
        
        this.localContent.header = this.itemInfo;
        this.showModal.edit.header = false;
        console.log(this.localContent.header);
        

        if (this.itemInfo.is_saved) {
              axios.post(`/item/update-save`, {
              template_id: this.itemInfo.template_id,
              type: 'headers',
              item: this.itemInfo,
            })
            .then(() => {
                if (this.localSaved.headers) {
                  Object.assign(this.localSaved.headers, this.itemInfo);
                }
                if (this.localContent.header?.template_id === this.itemInfo.template_id) {
                  this.localContent.header = {
                    ...this.localContent.header,
                    ...this.itemInfo
                  };
                }
            })
            .catch(error => {
              console.error('Failed:', error);
            });
        }
      },
      saveFooter() {
        this.itemInfo.pages = this.pages[this.itemInfo.section];
        this.localContent.footer = this.itemInfo;
        this.showModal.edit.footer = false;

        // if (this.itemInfo.is_saved) {
        //       axios.post(`/item/update-save`, {
        //       template_id: this.itemInfo.template_id,
        //       type: 'footers',
        //       item: this.itemInfo,
        //     })
        //     .then(() => {
        //         if (this.localSaved.footers) {
        //           Object.assign(this.localSaved.footers, this.itemInfo);
        //         }
        //         if (this.localContent.footer?.template_id === this.itemInfo.template_id) {
        //           this.localContent.footer = {
        //             ...this.localContent.footer,
        //             ...this.itemInfo
        //           };
        //         }      
        //     })
        //     .catch(error => {
        //       console.error('Failed:', error);
        //     });
        // }
      },
      saveEdit(type, slides) {
        if (type === "widgets") {
          console.log(slides);
          
          this.selectedSlides = slides;
          this.itemInfo.slides = this.selectedSlides;
        } else if (type === "footer" || type === "header") {
          this.itemInfo.pages = this.pages[this.itemInfo.section];
        }
        this.localContent[type][this.editIndex] = this.itemInfo;
        this.showModal.edit[type] = false;

        if (type === "widgets") {
        this.slides.forEach(slide => {
              slide.selected = false; 
            });
        }
        
        if (this.itemInfo.is_saved) {
              axios.post(`/item/update-save`, {
              template_id: this.itemInfo.template_id,
              type: type,
              item: this.itemInfo,
            })
            .then(() => {
                const itemToUpdate = this.localSaved[type].find(item => item.template_id === this.itemInfo.template_id);
                if (itemToUpdate) {
                  Object.assign(itemToUpdate, this.itemInfo);
                }
                this.localContent[type] = this.localContent[type].map(item => {
                if (item.template_id === this.itemInfo.template_id) {
                    return { ...item, ...this.itemInfo };
                  }
                  return item;
                });       
            })
            .catch(error => {
              console.error('Failed:', error);
            });
        }
      },
        deleteSavedElement(type, index) {
          if (confirm(`This is a saved item and deleting will remove from all pages it is present on.`)) {
            console.log(type, index);
            
              let item = this.localSaved[type][index];
              axios.post(`/item/delete-save`, {
              template_id: item.template_id,
              type: type,
            })
            .then((response) => {
                this.deleteItems(type, this.localSaved[type][index].template_id); 
                this.localSaved[type].splice(index, 1);         
            })
            .catch(error => {
              console.error('Failed:', error);
            });
          }
        },
        deleteItems(type, templateId) {
          this.localContent[type] = this.localContent[type].filter(item => item.template_id !== templateId);
        },
        cancelEdit(type) {
            this.itemInfo = this.localContent[type][this.editIndex];
            this.showModal.edit[type] = false;
            if (type === "widgets") {
              this.slides.forEach(slide => {
                slide.selected = false; 
              });
            }
        },
          deleteElement(type, index) {
            if (confirm(`Are you sure you want to delete this ${type}?`)) {
              this.localContent[type].splice(index, 1);
            }
          },
      cancelAdd(type) {
        this.showModal.new[type] = false;
        if (type === "widgets") {
          this.slides.forEach(slide => {
              slide.selected = false; 
          });
          this.widgetTitle = ''
          this.selectedWidgetType = ''
          this.selectedSlides = []
        }
      },
      fetchSlides() {
        axios.get('/api/slides')
            .then((response) => {
                this.slides = response.data.slides;
            })
            .catch((error) => {
                console.error('Error fetching slides:', error);
            });
      },
      openAddSaved(type) {
        this.showModal.saved[type] = true;
        this.useType = type;
      },
      addSaved(item, type) {
        console.log(item);
        
        this.localContent[type].push(item);
        this.showModal.saved[type] = false;
      },
      addWidget(newWidget) {
        let item = this.widgetOptions.find(option => option.variant === newWidget.variant);
        newWidget.label = item.label;
        this.localContent.widgets.push(newWidget);

        this.slides.filter(slide => slide.selected).forEach(slide => {
          slide.selected = false;
        })
        this.cancelAdd('widgets');
      },
        addHeader(newHeader) {
          newHeader.pages = this.pages[newHeader.section];
          this.localContent.header = newHeader;
          this.cancelAdd('header');
      },
      savePage() {
        router.post(`/cms/pages/save`, { 
          widgets: this.localContent.widgets, 
          page_id: this.page.id, 
          header: this.localContent.header,
          footer: this.localContent.footer,
          }, {
            onSuccess: () => {
              router.get(`/cms/pages/${this.page.section}`);
            }
          }
        );
      },
      saveLayout() {
        router.post(`/cms/layouts/save`, { 
          widgets: this.localContent.widgets, 
          layout_id: this.page.id, 
          header: this.localContent.header,
          footer: this.localContent.footer,
          }, {
            onSuccess: () => {
              router.get(`/cms/layouts`);
            }
          }
        );
      },
      saveBlog() {
        router.post(`/cms/blog/post/save`, { 
          widgets: this.localContent.widgets, 
          blog_id: this.page.id, 
          }, {
            onSuccess: () => {
              router.get(`/cms/blog`);
            }
          }
        );
      },
      openAddItem(type) {
        this.showModal.new[type] = true;
      },

      cancelAddFooter() {
        this.showModal.new.footer = false;
      },

      addFooter(newFooter) {
        this.localContent.footer = newFooter;
        this.cancelAddFooter();
      },

      saveHeader(item, type) {
        let name = '';
        if (!item.is_saved) {
          name = window.prompt("Enter a name for this saved item:", item.name || "");
          if (name === null);
        }
        
        axios.put(`/${type}/${item.id}/save`, {
          is_saved: !item.is_saved,
          name: name || null,
        })
        .then(() => {
          this.localContent[type].is_saved = !this.localContent[type].is_saved;
          this.localContent[type].name = name;
        })
        .catch(error => {
          console.error('Failed:', error);
        });
      },

      saveWidget(item, type, index) {
        let name = '';
        if (!item.is_saved) {
          name = window.prompt("Enter a name for this widget:");
          if (name === null);
        }

        if (!item.is_saved && !item.id) {
          item.is_saved = true;
          item.name = name;
          axios.post(`/${type}/create-save`, {
            item: item,
          })
          .then((response) => {
            
            const templateId = response.data.widget.template_id;
            this.localContent[type][index].is_saved = true;
            this.localContent[type][index].name = name;
            this.localContent[type][index].template_id = templateId;
            this.localContent[type][index].id = response.data.widget.id;
            this.localSaved[type].push(this.localContent[type][index]);
            console.log(this.localContent[type][index]);
          })
          .catch(error => {
            console.error('Failed:', error);
          });
        }

        if (!this[type].find(el => el.id === item.id) || item.id === undefined) {
          item.is_saved = !item.is_saved;
          name = name || null;
          console.log('this path', item);
          return;
        } 
        
        axios.put(`/${type}/${item.id}/save`, {
          is_saved: !item.is_saved,
          name: name || null,
        })
        .then(() => {
          this.localContent[type][index].is_saved = !this.localContent[type][index].is_saved;
          this.localContent[type][index].name = name;
        })
        .catch(error => {
          console.error('Failed:', error);
        });
      },
      closeWhatsOpen() {
        for (const section in this.showModal) {
          for (const key in this.showModal[section]) {
            this.showModal[section][key] = false;
          }
        }
      },
      cloneWidget(item, type, index) {
        const targetItem = JSON.parse(JSON.stringify(this.localContent[type][index]));
        const clonedItem = JSON.parse(JSON.stringify(targetItem));
        this.localContent[type].splice(index + 1, 0, clonedItem);
      },
    },
  };
  </script>
  
  <style scoped>
    .page-wrapper {
        display: flex;
        .page-left {
            display: flex;
            flex-direction: column;
            border-right: 2px solid var(--cms-black);
            width: 25%;
            gap: 10px;
            background-color: var(--cms-primary);
            .main-buttons {
              padding-top: 20px;
              display: flex;
              justify-content: center;
              gap: 10px;
              border-bottom: 2px solid var(--cms-white);
              padding-bottom: 20px;
              background-color: var(--cms-secondary);
              @media (hover:hover) {
                .cms-btn-default:hover {
                  border-color: var(--cms-white)
                }
              }
            }
            .widget-options {
                display: flex;
                gap: 8px;
            }
        }
        .page-right {
            display: flex;
            flex-direction: column;
            width: 75%;
            position: relative;
            min-height: 100vh;
        }
    }
    .slide-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      li {
        display: flex;
        align-items: center;
        gap: 10px;
        .slide-list-img {
          aspect-ratio: 1/1;
          width: 70px;
        }
      }
    }
    .edit-slide-form {
      position: fixed;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: var(--cms-white);
    }
    .sidebar-option {
      display: flex;
      gap: 20px;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      padding: 20px;
      h5 {
        border-bottom: 1px solid var(--cms-black);
        width: 100%;
      }
      .widget-options {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items:flex-start;
        width: 100%;
      }
      .widget-option {
        border: 2px solid var(--white);
        border-radius: 6px;
        padding: 4px 8px;
        background-color: var(--cms-tertiary);
        color: var(--white);
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        .move-buttons {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          .edit-btn {
            display: flex;
          }
        }
      }
    }
    .saved-item-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
      .saved-item {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        p {
          cursor: pointer;
        }
      }
    }
  </style>
  