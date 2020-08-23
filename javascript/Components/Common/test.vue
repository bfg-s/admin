<template>

    <tr v-for="(field, index) in form.fields" :key="index">
        <th>
            <div class="input-group input-group-sm">
                <input placeholder="Field name" :value="form.fields[index].name" @input="(e) => form.fields[index].name = 'str::to_st'.exec(e.target.value)" class="form-control" type="text" />
                <div class="input-group-append" v-if="form.create.controller">
                    <div class="input-group-text">/</div>
                </div>
                <input placeholer="Virtual title" v-model="form.fields[index].title" v-if="form.create.controller" class="form-control" type="text" />
            </div>
        </th>
        <td class="input-group-sm" v-if="form.create.migration">
            <div class="row">
                <div class="col-auto" v-if="aboutType(index)">
                    <v-info :value="aboutType(index, 2)" />
                </div>
                <div class="col-auto">
                    <div class="d-inline-flex">
                        <v-select2 v-model="form.fields[index].type" @change="(e) => changeType(index, e.target.value)" :options="type_list" :from-array="true" />
                    </div>
                    <div
                        v-if="aboutType(index) && aboutType(index, 1).length"
                        class="input-group input-group-sm d-inline-flex"
                        style="width: 140px"
                    >
                        <input
                            v-for="(prop, ind) in aboutType(index, 1)"
                            :key="`props_${index}_${ind}`"
                            type="text"
                            class="form-control"
                            :placeholder="prop"
                            :value="!(ind in form.fields[index].type_props) && form.fields[index].type in type_defaults ? form.fields[index].type_props[ind] = type_defaults[form.fields[index].type][ind] : form.fields[index].type_props[ind]"
                            @input="(e) => form.fields[index].type_props[ind] = e.target.value"
                        />
                    </div>
                </div>
            </div>
        </td>
        <td class="input-group-sm" v-if="form.create.model">
            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].cast" :possible="form.fields[index].type" :options="casts" :from-array="true" data-width="100%" />
        </td>
        <td class="input-group-sm" v-if="form.create.controller">
            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].field" :possible="form.fields[index].name" :options="fields" :from-array="true" data-width="100%" />
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
</template>