<template>
    <button
        ref="info" type="button"
        class="btn btn-sm btn-link"
    >
        <i class="fas fa-info-circle"></i>
    </button>
</template>

<script>
    export default {
        name: 'v-info',
        props: {
            value: {required: true}
        },
        mounted() {
            $(this.$refs['info']).popover({
                trigger: 'focus',
                content: this.value
            })
        }
    }
</script>