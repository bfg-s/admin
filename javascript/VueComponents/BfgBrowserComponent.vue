<template>
    <div
        :class="{'file-browser': true, 'add-files-dropped' : move}"
        @dragover.prevent="theDrugOver"
        @dragleave.prevent="theDrugLive"
        @drop.prevent="handleDrop"
    >
        <draggable v-model="values" @start="drag=true" @end="drag=false" class="file-browser-previews" handle=".move-handle">
            <div v-for="(val, valIndex) in values" v-if="! dropIndexes[valIndex]" class="file-browser-preview move-handle" :key="`preview-${valIndex}`">
                <img :src="val" :alt="val"/>
                <div class="file-browser-preview-controls">
                    <div class="file-browser-preview-control-btn" @click="preview(val)">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="file-browser-preview-control-btn" @click="drop(val, valIndex)">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </div>
            <div class="file-browser-preview file-browser-add" :key="`preview-add`" @click="open">
                <i class="fas fa-plus-circle"></i>
            </div>
        </draggable>


        <template v-for="(val, valIndex) in values">
            <input v-if="! dropIndexes[valIndex]" type="hidden" :name="`${fieldName}${!String(fieldName).endsWith('[]') ? '[]':''}`" :value="val" :key="`hidden-${valIndex}`" />
        </template>
        <input v-if="! values.length" :name="fieldName.replace(/\[]$/, '')" value="[__EMPTY_ARRAY__]" type="hidden" />
        <i class="fas fa-cloud-download-alt file-browser-logo-download"></i>
        <input type="file" ref="file" style="display: none" @change="previewFiles" multiple accept="image/*" />
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import {debounce} from "lodash";
export default {
    name: "bfg-browser-component",
    components: {
        draggable,
    },
    props: {
        value: {},
        fieldName: {required: true},
    },
    data() {
        return {
            values: Array.isArray(this.value) ? this.value : (this.value ? [this.value] : []),
            drag: false,
            dark: window.darkMode,
            dropIndexes: {},
            move: false
        }
    },
    mounted() {

    },
    methods: {
        theDrugLive: debounce(function() {
            this.move = false;
        }, 300),
        theDrugOver (event) {
            if (event.dataTransfer.types.includes('Files')) {
                this.move = true;
            }
        },
        async handleDrop(event) {
            const files = event.dataTransfer.files;
            await this.uploadImages(files);
            this.move = false;
        },
        preview (img) {
            let images = [];
            this.values.forEach((val) => {
                if (val !== img) {
                    images.push({
                        src: val,
                    });
                }
            });
            $.fancybox.open([
                {
                    src: img,
                },
                ...images
            ]);
        },
        open () {
            this.$refs.file.click();
        },
        async previewFiles(event) {
            const files = event.target.files;
            await this.uploadImages(files);
        },
        async uploadImages (files) {
            const loadedFiles = [];
            for (let i = 0; i < files.length; i++) {
                if (files[i].type.startsWith('image/')) {
                    loadedFiles.push(files[i]);
                }
            }

            for (let i = 0; i < loadedFiles.length; i++) {
                const formData = new FormData();
                formData.append('upload', loadedFiles[i]);

                try {
                    const response = await axios.post(window.uploader, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data && response.data.file) {
                        this.values.push(response.data.file);
                    }
                } catch (error) {
                    console.error(`Произошла ошибка при отправке файла ${i+1}`, error);
                    break;
                }
            }
        },
        drop (file, valIndex) {
            //this.$set(this.dropIndexes, valIndex, true);
            //this.dropIndexes[valIndex] = true;
            //console.log(this.dropIndexes);
            axios.post(window.uploader_drop, {
                file
            }).then(() => {
                this.values = this.values
                    .filter((val, index) => index !== valIndex);
            }).catch((error) => {
                console.error('Произошла ошибка при удалении файла', error);
            });
        }
    }
}
</script>

<style scoped>
    .file-browser {
        width: 100%;
        min-height: 300px;
        border: 1px dashed #ccc;
        border-radius: 0.25rem;
        position: relative;
    }
    .file-browser-logo-download {
        position: absolute;
        top: calc(50% - 115px);
        left: calc(50% - 115px);
        font-size: 230px;
        opacity: 0.2;
    }
    .file-browser-previews {
        display: flex;
        flex-wrap: wrap;
    }
    .file-browser-preview {
        width: 140px;
        height: 140px;
        margin: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border: 1px solid #ccc;
        border-radius: 0.25rem;
        z-index: 1;
    }
    .file-browser-preview img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }
    .file-browser-preview-controls {
        position: absolute;
        top: 5px;
        right: 5px;
        display: flex;
        flex-wrap: wrap;
    }
    .file-browser-preview-control-btn i {
        opacity: 0.2;
    }

    .file-browser-preview-control-btn {
        border: 1px solid black;
        padding: 0 7px 0 7px;
        border-radius: 0.25rem;
        background-color: #343a40;
        color: white !important;
        margin-left: 5px;
        cursor: pointer;
    }
    .file-browser-add {
        cursor: pointer;
        background-color: #343a40;
        color: white;
    }
    .file-browser-add i {
        font-size: 80px;
        opacity: 0.2;
    }
    .add-files-dropped {
        background-color: rgba(0, 0, 0, 0.4);
    }
    .add-files-dropped .file-browser-preview {
        opacity: 0.2;
    }
</style>
