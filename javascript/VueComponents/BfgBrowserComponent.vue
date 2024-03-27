<template>
    <div class="file-browser">
        <draggable v-model="values" @start="drag=true" @end="drag=false" class="file-browser-previews" handle=".move-handle">
            <div v-for="(val, valIndex) in values" class="file-browser-preview move-handle" :key="`preview-${valIndex}`">
                <img :src="val"  :alt="val"/>
                <div class="file-browser-preview-controls">
                    <div class="file-browser-preview-control-btn" @click="preview(val)">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="file-browser-preview-control-btn">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </div>
            <div class="file-browser-preview file-browser-add" :key="`preview-add`">
                <i class="fas fa-plus-circle"></i>
            </div>
        </draggable>


        <template v-for="(val, valIndex) in values">
            <input type="hidden" :name="`${fieldName}${!String(fieldName).endsWith('[]') ? '[]':''}`" :value="val" :key="`hidden-${valIndex}`" />
        </template>

        <i class="fas fa-cloud-download-alt file-browser-logo-download"></i>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
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
        }
    },
    mounted() {
        console.log(this.fieldName, this.values);
    },
    methods: {
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
</style>
