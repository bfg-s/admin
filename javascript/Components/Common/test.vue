<template>

    <tr v-for="(field, index) in form.fields" :key="index">
        <th>
            <div class="input-group input-group-sm">
                <input :value="form.fields[index].name" class="form-control"
                       placeholder="Field name" type="text"
                       @input="(e) => form.fields[index].name = 'str::to_st'.exec(e.target.value)"/>
                <div v-if="form.create.controller" class="input-group-append">
                    <div class="input-group-text">/</div>
                </div>
                <input v-if="form.create.controller" v-model="form.fields[index].title" class="form-control"
                       placeholer="Virtual title" type="text"/>
            </div>
        </th>
        <td v-if="form.create.migration" class="input-group-sm">
            <div class="row">
                <div v-if="aboutType(index)" class="col-auto">
                    <v-info :value="aboutType(index, 2)"/>
                </div>
                <div class="col-auto">
                    <div class="d-inline-flex">
                        <v-select2 v-model="form.fields[index].type" :from-array="true"
                                   :options="type_list" @change="(e) => changeType(index, e.target.value)"/>
                    </div>
                    <div
                        v-if="aboutType(index) && aboutType(index, 1).length"
                        class="input-group input-group-sm d-inline-flex"
                        style="width: 140px"
                    >
                        <input
                            v-for="(prop, ind) in aboutType(index, 1)"
                            :key="`props_${index}_${ind}`"
                            :placeholder="prop"
                            :value="!(ind in form.fields[index].type_props) && form.fields[index].type in type_defaults ? form.fields[index].type_props[ind] = type_defaults[form.fields[index].type][ind] : form.fields[index].type_props[ind]"
                            class="form-control"
                            type="text"
                            @input="(e) => form.fields[index].type_props[ind] = e.target.value"
                        />
                    </div>
                </div>
            </div>
        </td>
        <td v-if="form.create.model" class="input-group-sm">
            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].cast" :from-array="true"
                       :options="casts" :possible="form.fields[index].type" data-width="100%"/>
        </td>
        <td v-if="form.create.controller" class="input-group-sm">
            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].field" :from-array="true"
                       :options="fields" :possible="form.fields[index].name" data-width="100%"/>
        </td>
        <td v-if="form.create.migration" class="input-group-sm">
            <v-select2 v-if="aboutType(index, 0)" v-model="form.fields[index].key" :from-array="true" :options="keys"
                       data-width="100%"/>
        </td>
        <th v-if="form.create.migration" class="input-group-sm">
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <div v-if="aboutType(index, 0)" style="margin: -7px">
                            <div class="icheck-primary d-inline">
                                <input :id="`scaffold_nullable_field_${index}`" v-model="form.fields[index].nullable"
                                       type="checkbox"/>
                                <label :for="`scaffold_nullable_field_${index}`"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="form.create.controller" class="input-group-append">
                    <div class="input-group-text">/</div>
                </div>
                <input v-if="aboutType(index, 0)" v-model="form.fields[index].default" :disabled="form.fields[index].nullable"
                       class="form-control" placeholder="Default value" type="text"/>
                <div v-if="form.create.controller" class="input-group-append">
                    <div class="input-group-text">/</div>
                </div>
                <input v-if="aboutType(index, 0)" v-model="form.fields[index].comment" class="form-control"
                       placeholder="Comment" type="text"/>
            </div>
        </th>
        <th>
            <button v-if="index && form.fields.length > 1" class="btn btn-sm btn-danger" type="button"
                    @click="removeField(index)">
                <i class="fas fa-trash"></i>
            </button>
        </th>
    </tr>
</template>
