<template>
    <button
        ref="info" class="btn btn-sm btn-link"
        type="button"
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
