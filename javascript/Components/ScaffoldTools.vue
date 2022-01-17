<template>
    <div class="scaffold p-2">
        <v-loading :active="loading"/>
        <div class="row">
            <div class="form-group col input-group-sm">
                <label for="scaffolding_table_name">Table name</label>
                <input :value="form.table_name" @input="(e) => form.table_name = 'str::to_st'.exec(e.target.value)" type="text" class="form-control" id="scaffolding_table_name">
            </div>
            <div class="form-group col input-group-sm">
                <label for="scaffolding_model">Model</label>
                <input :value="form.model.join('\\')" @input="form.model = $event.target.value.split('\\')" type="text" class="form-control" id="scaffolding_model">
            </div>
            <div class="form-group col input-group-sm">
                <label for="scaffolding_controller">Controller</label>
                <input :value="form.controller.join('\\')" @input="form.controller = $event.target.value.split('\\')" type="text" class="form-control" id="scaffolding_controller">
            </div>
        </div>
        <div class="form-row mb-3">
            <div class="col icheck-primary d-inline">
                <div class="icheck-primary d-inline mr-2">
                    <input v-model="form.create.migration" type="checkbox" id="scaffold_create_migration" />
                    <label for="scaffold_create_migration">Create migration</label>
                </div>
                <div class="icheck-primary d-inline" v-if="form.create.migration">
                    <input v-model="form.create.migrate" type="checkbox" id="scaffold_run_migrate" />
                    <label for="scaffold_run_migrate">Run migrate</label>
                </div>
            </div>
            <div class="col icheck-primary d-inline">
                <input v-model="form.create.model" type="checkbox" id="scaffold_create_model" />
                <label for="scaffold_create_model">Create model</label>
            </div>
            <div class="col icheck-primary d-inline">
                <div class="col icheck-primary d-inline">
                    <input v-model="form.create.controller" type="checkbox" id="scaffold_create_controller" />
                    <label for="scaffold_create_controller">Create controller</label>
                </div>
                <div class="icheck-primary d-inline" v-if="form.create.migration">
                    <input v-model="form.create.controller_permissions" type="checkbox" id="scaffold_controller_permissions" />
                    <label for="scaffold_controller_permissions">Create controller permissions</label>
                </div>
            </div>
        </div>
        <div class="row" v-if="form.table_name.length >= 2 && any_create">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Field name<span v-if="form.create.controller">/Virtual title</span></th>
                        <th scope="col" v-if="form.create.migration">Type</th>
                        <th scope="col" v-if="form.create.model">Cast</th>
                        <th scope="col" v-if="form.create.controller">Field</th>
                        <th scope="col" v-if="form.create.migration">Key</th>
                        <th scope="col" v-if="form.create.migration">Nullable/Default value/Comment</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <draggable v-model="form.fields" handle=".scaffold-move-handle" tag="tbody">
                    <tr v-for="(field, index) in form.fields" :key="index">
                        <th>
                            <button class="btn btn-link btn-sm" style="cursor: move">
                                <i class="fas fa-bars scaffold-move-handle"></i>
                            </button>
                        </th>
                        <td>
                            <div class="input-group input-group-sm">
                                <input placeholder="Field name" :value="form.fields[index].name" @input="(e) => form.fields[index].name = 'str::to_st'.exec(e.target.value)" class="form-control" type="text" />
                                <div class="input-group-append" v-if="form.create.controller">
                                    <div class="input-group-text">/</div>
                                </div>
                                <input placeholder="Virtual title" v-model="form.fields[index].title" v-if="form.create.controller" class="form-control" type="text" />
                            </div>
                        </td>
                        <td v-if="form.create.migration">

                            <div class="d-flex bd-highlight input-group-sm">

                                <v-select2 class="flex-fill bd-highlight" v-model="form.fields[index].type" @change="(e) => changeType(index, e.target.value)" :options="type_list" :from-array="true" />

                                <input
                                    v-for="(prop, ind) in aboutType(index, 1)"
                                    :key="`props_${index}_${ind}`"
                                    type="text"
                                    :style="`max-width: ${aboutType(index, 1).length > 1 ? '70':'140'}px`"
                                    class="form-control flex-fill bd-highlight"
                                    :placeholder="prop"
                                    :value="!(ind in form.fields[index].type_props) && form.fields[index].type in type_defaults ? form.fields[index].type_props[ind] = type_defaults[form.fields[index].type][ind] : form.fields[index].type_props[ind]"
                                    @input="(e) => form.fields[index].type_props[ind] = e.target.value"
                                />
                            </div>

                        </td>
                        <td class="input-group-sm" v-if="form.create.model">
                            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].cast" :possible="form.fields[index].type" :options="casts" :from-array="true" data-width="100%" />
                        </td>
                        <td class="input-group-sm" v-if="form.create.controller">
                            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].field" name="field" :possible="form.fields[index].name" :options="fields" :from-array="true" data-width="100%" />
                        </td>
                        <td class="input-group-sm" v-if="form.create.migration">
                            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].key" :options="keys" :from-array="true" data-width="100%" />
                        </td>
                        <th class="input-group-sm" v-if="form.create.migration">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <div v-if="aboutType(index, 0)" style="margin: -7px">
                                            <div class="icheck-primary d-inline">
                                                <input v-model="form.fields[index].nullable" type="checkbox" :id="`scaffold_nullable_field_${index}`" />
                                                <label :for="`scaffold_nullable_field_${index}`"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group-append" v-if="form.create.controller">
                                    <div class="input-group-text">/</div>
                                </div>
                                <input placeholder="Default value" v-if="aboutType(index, 0)" v-model="form.fields[index].default" :disabled="form.fields[index].nullable" class="form-control" type="text" />
                                <div class="input-group-append" v-if="form.create.controller">
                                    <div class="input-group-text">/</div>
                                </div>
                                <input placeholder="Comment" v-if="aboutType(index, 0)" v-model="form.fields[index].comment" class="form-control" type="text" />
                            </div>
                        </th>
                        <th>
                            <button v-if="index && form.fields.length > 1" @click="removeField(index)" type="button" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </th>
                    </tr>
                </draggable>
            </table>
        </div>
        <div class="row" v-if="form.table_name.length >= 2 && any_create">
            <div class="col">
                <div class="input-group input-group-sm d-inline-flex" :style="`width: ${(String(add_num).length*6)+122}px`">
                    <input v-model="add_num" @keyup.enter="addNum()" @click="(e) => e.target.select()" type="text" class="form-control" />
                    <div class="input-group-append">
                        <button @click="addNum()" type="button" class="input-group-btn btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Add field
                        </button>
                    </div>
                </div>
            </div>
            <div class="col text-right">

                <div class="icheck-primary d-inline-flex ml-2">
                    <input v-model="form.updated_at" type="checkbox" id="scaffold_create_created_updated_at" />
                    <label for="scaffold_create_created_updated_at">Updated at</label>
                </div>
                <div class="icheck-primary d-inline-flex ml-2">
                    <input v-model="form.created_at" type="checkbox" id="scaffold_create_created_created_at" />
                    <label for="scaffold_create_created_created_at">Created at</label>
                </div>
                <div class="icheck-primary d-inline-flex ml-2">
                    <input v-model="form.soft_delete" type="checkbox" id="scaffold_create_soft_deletes" />
                    <label for="scaffold_create_soft_deletes">Soft deletes</label>
                </div>
            </div>
        </div>
        <div class="row" v-if="form.table_name.length >= 2 && any_create">
            <div class="col text-right">
                <div class="input-group input-group-sm d-inline-flex" style="width: 230px">
                    <div class="input-group-prepend">
                        <div class="input-group-text">Primary key: </div>
                    </div>
                    <input  v-model="form.primary" type="text" class="form-control" />
                    <div class="input-group-append">
                        <button @click="create" type="button" class="input-group-btn btn btn-sm btn-primary d-inline-flex">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import merge from 'lodash/merge';
import map from 'lodash/map';
import VSelect2 from "./Common/Select2";
import VInfo from "./Common/Informer";
import draggable from "vuedraggable";

export default {
        name: 'ScaffoldTools',
        components: {VInfo, VSelect2, draggable},
        props: ['fields'],
        data () {
            return {
                dragging: false,
                enabled: true,
                loading: false,
                add_num: 1,
                form: {
                    table_name: '',
                    model: ['App', 'Models'],
                    controller: ['App', 'LteAdmin', 'Controllers'],
                    create: {
                        migration: true,
                        model: true,
                        controller: true,
                        controller_permissions: true,
                        migrate: true
                    },
                    fields: [],
                    primary: 'id',
                    created_at: true,
                    updated_at: true,
                    soft_delete: false
                },
                type_defaults: {
                    char: [100], dateTime: [0], dateTimeTz: [0], decimal: [8,2], double: [8,2], float: [8,2],
                    string: [100], time: [0], timeTz: [0], timestamp: [0], timestampTz: [0], unsignedDecimal: [8,2]
                },
                type_with_params: require('./ScafoldCollection.json'),
                keys: ['NULL', 'Unique', 'Index'],
                casts: [
                    'integer', 'real', 'float', 'double', 'decimal', 'string', 'boolean', 'object', 'array',
                    'collection', 'date', 'datetime', 'timestamp'
                ]
            };
        },
        watch: {
            'form.create.migration' (val) {
                this.form.create.migrate = val;
            },
            'form.table_name' (val, old) {
                let postfix = "Controller";
                let index = this.form.model.indexOf(ljs.help.camelize(old, true));
                let index2 = this.form.controller.indexOf(ljs.help.camelize(old, true)+postfix);
                let num = index >= 0 ? index : this.form.model.length;
                let num2 = index2 >= 0 ? index2 : this.form.controller.length;
                if (val) {
                    this.form.model[num] = ljs.help.camelize(val, true);
                    this.form.controller[num2] = ljs.help.camelize(val, true)+postfix;
                } else {
                    this.$delete(this.form.model, num);
                    this.$delete(this.form.controller, num2);
                }
                if (!this.form.fields.length && val.length >= 2) {
                    this.addId();
                } else if (val.length < 2) {
                    this.form.fields = [];
                }
            }
        },
        computed: {
            type_list () {let obj = {}; map(this.type_with_params, (field, key) => obj[key] = key); return obj;},
            field_list () { let obj = {}; map(this.fields, (key) => obj[key] = key); return obj; },
            cast_list () { let obj = {}; map(this.casts, (key) => obj[key] = key); return obj; },
            any_create () { return this.form.create.migration || this.form.create.model || this.form.create.controller || this.form.create.migrate }
        },
        mounted () {
        },
        methods: {
            addId () {
                this.addFields({
                    name: 'id',
                    title: 'ID',
                    type: 'bigIncrements',
                    cast: 'integer',
                    field: 'info'
                });
            },
            addNum () {
                if (!ljs.help.isNumber(this.add_num)) {

                    String(this.add_num)
                        .split(",")
                        .map((p) => {
                            if (p.trim()) {
                                this.addFields({
                                    name: 'str::to_st'.exec(p.trim()),
                                    title: ljs.help.camelize(p.trim(), true),
                                    field: this.fields.indexOf(p.trim()) !== -1 ? p.trim() : 'input'
                                })
                            }
                        });
                } else {
                    for (let i = 0;i<this.add_num;i++) {
                        if (this.form.fields.length) {
                            this.addFields();
                        } else {
                            this.addId();
                        }
                    }
                }
                this.add_num = 1;
            },
            addFields  (params = {}) {
                if ('name' in params) {
                    if (!!this.form.fields.filter((i) => i.name === params.name).length) return ;
                }
                this.form.fields.push(merge({
                    name: null,
                    title: null,
                    type: 'string',
                    type_props: [],
                    cast: 'string',
                    field: 'input',
                    nullable: false,
                    key: 'NULL',
                    default: null,
                    comment: null,
                    model: true
                }, params));
            },
            removeField (index) {
                if (this.form.fields.length > 1) {
                    this.$delete(this.form.fields, index);
                }
            },
            create () {

                this.loading = true;
                jax.lte_scaffolding(this.form)
                    .finally(() => {
                        this.loading = false;
                    });
            },
            selectAlert (title, value, list, cb) {
                ljs.swal.fire({
                    title: title,
                    inputValue: value,
                    input: 'select',
                    inputOptions: list,
                    showCancelButton: true,
                    onOpen: (o) => {
                        $(o).find('select').select2({
                            minimumResultsForSearch: 15,
                        });
                    },
                    onDestroy: (o) => {
                        $(o).find('select').select2('destroy');
                    }
                }).then(function (result) {
                    if (typeof cb === 'function' && result.value) cb(result.value);
                })
            },
            aboutType (index, key = null) {
                if (!this.form.fields[index] || !this.form.fields[index].type) return undefined;
                let type = this.type_with_params[this.form.fields[index].type];
                if (!type) return undefined;
                return key !== null ? type[key] : type;
            },
            changeType (index, type) {
                if (this.form.fields[index]) {
                    this.form.fields[index]['type_props'] = [];
                    this.form.fields[index]['key'] = 'NULL';
                    this.form.fields[index]['default'] = null;
                    this.form.fields[index]['comment'] = null;
                    this.form.fields[index]['model'] = this.type_with_params[type][0];
                }
            }
        }
    }
</script>

<style>
.scaffold .select2-selection__rendered {
    line-height: 23px !important;
}
.scaffold .select2-container .select2-selection--single {
    height: 20px !important;
}
.scaffold .select2-selection__arrow {
    height: 20px !important;
}
.scaffold .select2-selection--single {
    min-height: 31px !important;
}
.select2-container--open {
    z-index: 9999!important;
}
</style>
