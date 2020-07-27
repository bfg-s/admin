<template>
    <div>
        <div class="input-group input-group-sm mb-1">
            <div class="input-group-prepend">
                <button
                        v-for="(m, ind) in default_methods"
                        :key="`def_${ind}`"
                        @click="add(m)"
                        type="button"
                        class="btn btn-primary btn-sm"
                        v-if="!hasSlug(m)"
                        :title="default_methods_titles[m]"
                >{{m}}</button>
            </div>
            <input v-model="add_data" @keyup.enter="add" type="text" class="form-control fix-rounded-right" required>
            <div class="input-group-append">
                <button @click="add" type="button" class=" btn btn-success btn-sm"><i class="fas fa-plus"></i></button>
            </div>
        </div>

        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th scope="col">Slug</th>
                    <th scope="col"></th>
                    <th scope="col" class="text-right">Roles</th>
                </tr>
            </thead>

            <tbody>

                <tr v-for="(gate, index) in gates" :key="`gate_${index}`">
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <button type="button" @click="drop(index)" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <input v-model="gates[index].slug" @change="gates[index].slug = serialized(gates[index].slug); save()" type="text" class="form-control fix-rounded-right" required>
                        </div>
                    </td>
                    <th scope="col">
                        <div class="input-group input-group-sm">
                            <input v-model="gates[index].description" @change="save" type="text" class="form-control fix-rounded-right" required>
                        </div>
                    </th>
                    <td class="text-right">
                        <div class="btn-group dropleft">
                            <button type="button" class="btn btn-link btn-sm dropdown-toggle dropdown-toggle-split mr-0 p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                        class="badge badge-success m-0 ml-1"
                                        v-for="(role, role_index) in gate.roles"
                                        :key="`gas_role_${role_index}`"
                                >{{role.name}}</span>
                            </button>
                            <div class="dropdown-menu">
                                <a
                                    v-for="(role, i) in roles" :key="`role_${i}`"
                                    @click.stop="switchRole(index, role)"
                                    :class="`dropdown-item ${hasRoleIn(index, role) ? 'active':''}`"
                                    href="javascript:void(0)"
                                >
                                    <i v-if="!hasRoleIn(index, role)" class="fas fa-user-plus"></i>
                                    <i v-else class="fas fa-user-minus"></i>
                                    {{role.name}}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a
                                        @click="setAll(index)"
                                        :class="`dropdown-item ${gate.roles.length === roles.length ? 'active':''}`"
                                        href="javascript:void(0)"
                                >
                                    <i class="fas fa-users"></i>
                                    Set all
                                </a>
                                <div class="dropdown-divider"></div>
                                <a
                                    v-for="(role, i) in roles"
                                    :key="`only_role_${i}`"
                                    @click.stop="setOnly(index, role)"
                                    :class="`dropdown-item ${onlyHas(index, role) ? 'active':''}`"
                                    href="javascript:void(0)"
                                >
                                    <i v-if="onlyHas(index, role)" class="fas fa-user-check"></i>
                                    <i v-else class="fas fa-user-alt"></i>
                                    Only {{role.name}}
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>

    </div>
</template>

<script>
    import filter from "lodash/filter";

    export default {
        name: 'GatesTools',
        props: ['action', 'lte', 'roles', 'funcs'],
        data () {
            return {
                default_methods: [
                    'index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'restore', 'force_destroy'
                ],
                default_methods_titles: {
                    'index': '[GET] Model data list',
                    'create': '[GET] Model create form',
                    'store': '[POST] Model new data',
                    'show': '[GET] Model show data',
                    'edit': '[GET] Model edit form',
                    'update': '[PUT/PATCH] Model update data',
                    'destroy': '[DELETE] Model delete data',
                    'restore': '[DELETE] Model restore deleted data',
                    'force_destroy': '[DELETE] Model force delete data',
                },
                add_data: '',
                gates: [
                ],
                timer: null,
                can_save: false
            };
        },
        watch: {
        },
        mounted () {
            this.setGates(this.funcs);
        },
        methods: {
            setGates (data) {
                this.$set(this, 'gates', []);
                data.map((f) => {
                    this.gates.push({
                        id: f.id,
                        description: f.description,
                        roles: f.roles,
                        slug: f.slug
                    });
                });
            },
            save () {
                if (this.timer) { clearTimeout(this.timer); }
                this.timer = setTimeout(() => {
                    this.timer = null;
                    jax.lte_admin.update_functions(this.gates, this.action[0]).then((data) => {
                        this.setGates(data);
                    });
                }, 500);
            },
            add (data = null) {
                let slugs = (data ? data : this.add_data).split(',');
                slugs.map((slug) => {
                    if (!this.hasSlug(slug)) {
                        let s = "str::to_slug".exec("str::to_translit".exec(slug.trim()));
                        this.gates.push({
                            description: this.default_methods_titles[s] ? this.default_methods_titles[s] : '',
                            roles: this.roles,
                            slug: s
                        });
                    } else {
                        "toast:error".exec(`Slug "${slug}" is already has!`);
                    }
                });
                this.add_data = "";
                this.save();
            },
            drop (index) {
                if (this.gates[index]) {
                    "alert::confirm".exec(`Delete [${this.gates[index].slug}]?`).then((data) => {
                        if (data.value) {
                            if (this.gates[index].id) {
                                jax.lte_admin.drop_function(this.gates[index].id, this.action[0]).then((data) => {
                                    this.setGates(data);
                                });
                            }
                        }
                    })
                }
            },
            hasRoleIn (index, role) {
                if (this.gates[index]) {
                    return !!filter(this.gates[index].roles, ['id', Number(role.id)]).length;
                }
                return false;
            },
            hasSlug (slug) {
                return !!filter(this.gates, ['slug', slug]).length;
            },
            onlyHas (index, role) {
                if (this.gates[index] && this.gates[index].roles.length === 1) {
                    return this.hasRoleIn(index, role);
                }
                return false;
            },
            switchRole (index, role) {
                if  (this.gates[index]) {
                    if (this.hasRoleIn(index, role)) {

                        if (this.gates[index].roles.length !== 1) {
                            this.gates[index].roles = filter(this.gates[index].roles, (r) => {
                                return r.id !== role.id;
                            });
                            this.save();
                        }
                    }

                    else {
                        this.gates[index].roles.push(role);
                        this.save();
                    }
                }
            },
            setOnly (index, role) {
                if  (this.gates[index]) {
                    this.$set(this.gates[index], 'roles', [role]);
                    this.save();
                }
            },
            setAll (index) {
                if  (this.gates[index]) {
                    this.$set(this.gates[index], 'roles', this.roles);
                    this.save();
                }
            },
            serialized: function (value) {
                if (!value) return ''
                return "str::to_slug".exec("str::to_translit".exec(value.trim()))
            }
        }
    }
</script>