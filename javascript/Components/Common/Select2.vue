<template>
    <select ref="select" style="width: 100%">
        <option
            v-for="(option, index) in options"
            :key="`option_${index}_${_uid}`"
            :selected="select(value,option)"
            :value="fromArray ? option : index"
        >{{ option }}
        </option>
    </select>
</template>

<script>
export default {
    name: 'v-select2',
    props: {
        options: {
            default: () => {
                return {}
            }
        },
        value: {required: true},
        fromArray: {type: Boolean, default: false},
        possible: {},
        name: {}
    },
    watch: {
        possible(val) {
            if (val && val.trim()) {
                let f = this.options.indexOf(val);
                if (this.fromArray && f !== -1) {
                    this.setVal(val);
                } else if (!this.fromArray && val in this.options) {
                    this.setVal(val);
                }
            }
        },
        value(val) {
            if (this.name === 'field') {
                console.log(val, this.options);
            }
            this.setVal(val);
        }
    },
    beforeDestroy() {
        this.destroy();
    },
    mounted() {
        this.build();
        if (window.ass === undefined) {
            window.ass = [];
        }
        if (this.name === 'field') {
            window.ass.push($(this.$refs['select']));
        }
    },
    methods: {
        select(v1, v2) {
            return v1 === v2;
        },
        destroy() {
            $(this.$refs['select']).select2('destroy');
        },
        build() {
            $(this.$refs['select'])
                .select2()
                .on('change', (e) => {
                    this.$emit('input', e.target.value);
                    this.$emit('change', e);
                }).val(this.value).trigger('change');
        },
        setVal(val) {
            $(this.$refs['select'])
                .val(val).trigger('change');
            this.$emit('input', val);
        }
    }
}
</script>
