<template>
    <div ref="dbs">

        <draggable class="ds-row-list-group" :list="lines" group="rows">
            <div v-for="(line, index) in lines">
                <a @click.prevent="dropRow(index)" style="position: absolute;right: 43px; cursor: pointer"><i class="fas fa-trash"></i></a>
                <draggable class="ds-list-group" :list="line" group="cols" :key="`linke-${index}`" :move="(evt) => evt.from !== evt.to || line.length < 4">
                    <div
                        class="ds-list-group-item"
                        v-for="(element, index2) in line"
                        :key="`col-${element.id}`"
                    >
                        <a @click.prevent="dropElement(index, index2)" style="position: absolute;right: 3px;top: 0; cursor: pointer"><i class="fas fa-trash"></i></a>
                        <a @click.prevent="toggleOpen(index,index2,element.id)" style="position: absolute;right: 20px;top: 0; cursor: pointer"><i class="fas fa-cogs"></i></a>
                        <h5><i :class="element.icon"></i> {{ element.name }}</h5>
                        <div v-if="isOpen(index,index2,element.id)">
                            <template v-for="(settingDefault, settingName) in element.settings">
                                <template v-if="element.settingsType[settingName] === 'string'">
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="lines[index][index2].settings[settingName]"
                                        :placeholder="element.settingsDescription[settingName]"
                                        :key="`input-for-${settingName}-${element.id}`"
                                    >
                                </template>
                                <template v-if="element.settingsType[settingName] === 'model_select'">
                                    <select
                                        class="form-control"
                                        :key="`input-for-${settingName}-${element.id}`"
                                        :placeholder="element.settingsDescription[settingName]"
                                        v-model="lines[index][index2].settings[settingName]"
                                    >
                                        <option disabled :selected="! settingDefault">{{ element.settingsDescription[settingName] }}</option>
                                        <option v-for="(model, modelIndex) in models" :value="model" :key="`input-for-${settingName}-${element.id}-${modelIndex}`">
                                            {{ model }}
                                        </option>
                                    </select>
                                </template>
                                <template v-if="element.settingsType[settingName] === 'boolean'">
                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success" :key="`input-for-${settingName}-${element.id}`">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            v-model="lines[index][index2].settings[settingName]"
                                            :checked="settingDefault"
                                            :id="`input-for-${settingName}-${element.id}`"
                                        >
                                        <label class="custom-control-label" :for="`input-for-${settingName}-${element.id}`">{{ element.settingsDescription[settingName] }}</label>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <ul v-else style="margin: 0;">
                            <template v-for="(settingDefault, settingName) in element.settings">
                                <li v-if="(element.settingsType[settingName] === 'string' || element.settingsType[settingName] === 'model_select') && lines[index][index2].settings[settingName]">
                                    {{ element.settingsDescription[settingName] }}: {{ lines[index][index2].settings[settingName] }}
                                </li>
                            </template>
                        </ul>
                    </div>
                </draggable>
            </div>
        </draggable>

        <div class="input-group input-group-sm" style="padding: 10px;">
            <select v-model="addWidgetIndex" class="form-control">
                <option v-for="(widget, widgetsIndex) in widgets" :value="widgetsIndex" :key="`wo-${widgetsIndex}`">
                    {{ widget.name }} - {{ widget.description }}
                </option>
            </select>
            <span class="input-group-append">
                <button class="btn btn-warning" type="button" @click.prevent="addWidgetToLastLine">{{ l.add_widget }}</button>
            </span>
            <span class="input-group-append">
                <button class="btn btn-primary" type="button" @click.prevent="addLine">{{ l.add_line }}</button>
            </span>
            <span class="input-group-append">
                <button class="btn btn-success" type="button" @click.prevent="save">{{ l.save }}</button>
            </span>
        </div>
    </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
    name: "bfg-dashboard-settings-component",
    props: ['widgets_hash', 'models_hash', 'dashboard_hash', 'lines_hash'],
    components: {
        draggable,
    },
    data () {
        return {
            widgets: JSON.parse(atob(this.widgets_hash)),
            models: JSON.parse(atob(this.models_hash)),
            dashboard: JSON.parse(atob(this.dashboard_hash)),
            lines: JSON.parse(atob(this.lines_hash)) ?? [],
            addWidgetIndex: null,
            openStates: {},
            l: window.langs,
        };
    },
    mounted () {

    },
    methods: {
        isOpen (index, index2, id) {
            if (! this.openStates[index]) {
                this.$set(this.openStates, index, {});
            }
            if (! this.openStates[index][index2]) {
                this.$set(this.openStates[index], index2, {});
            }
            if (! this.openStates[index][index2][id]) {
                this.$set(this.openStates[index][index2], id, false);
            }
            return this.openStates[index][index2][id]
        },
        toggleOpen (index, index2, id) {
            if (this.isOpen(index, index2, id)) {
                this.$set(this.openStates[index][index2], id, false);
            } else {
                this.$set(this.openStates[index][index2], id, true);
            }
        },
        save () {
            NProgress.start();
            const token = exec('token');
            axios.post(window.save_dashboard, {
                _token: token,
                dashboard_id: this.dashboard.id,
                lines: this.lines,
            }).then(data => exec(data.data)).finally(d => {
                NProgress.done();
            });
        },
        dropRow (index1) {
            const result = this.lines.filter((i,k) => k!==index1);
            this.$set(this, 'lines', result);
        },
        dropElement (index1, index2) {
            const result = this.lines[index1].filter((i,k) => k!==index2);
            this.$set(this.lines, index1, result);
        },
        addWidgetToLastLine () {
            if (this.widgets[this.addWidgetIndex]) {

                if (! this.lines.length) {
                    this.addLine();
                }
                const id = Math.random()*10000000000000000000;
                this.lines[this.lines.length - 1].push({
                    ...this.widgets[this.addWidgetIndex],
                    settings: Object.assign({}, this.widgets[this.addWidgetIndex].settings),
                    id: id,
                });
                this.toggleOpen(this.lines.length - 1, this.lines[this.lines.length - 1].length - 1, id);
                this.addWidgetIndex = null;
            }
        },
        addLine () {
            this.lines.push([]);
        }
    }
}
</script>

<style scoped>
.ds-row-list-group {
    display: flex;
    flex-direction: column;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin: 10px;
}
.ds-list-group {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin: 10px;
}
.ds-list-group-item {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin: 10px;
    cursor: move;
    position: relative;
    overflow: hidden;
    flex: 1;
    flex-basis: 100%;
}
</style>
