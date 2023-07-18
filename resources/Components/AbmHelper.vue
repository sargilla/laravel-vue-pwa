<template>
    <q-dialog ref="dialogRef" class="tw-z-40" persistent @hide="onDialogHide">
        <q-card class="q-dialog-plugin">
            <q-card-section class="row items-center">
                <div class="text-h6">{{ title }}</div>
                <q-space>
                    <q-btn v-close-popup icon="close" flat round dense> </q-btn>
                </q-space>
            </q-card-section>
            <q-card-section class="q-mx-md column">
                <q-form v-for="(row, i) in fields" :key="i" class="q-gutter-md" @submit.prevent>
                    <div v-if="row.files" class="row">
                        <FileUploader v-model="row.data" class="column" />
                    </div>

                    <div v-else>
                        <div v-if="!row.options">
                            <div v-if="row.text_area">
                                <q-input
                                    v-model="row.data"
                                    :label="row.label"
                                    class="q-my-md"
                                    type="textarea"
                                    filled
                                    square
                                    clearable
                                >
                                </q-input>
                            </div>
                            <div v-else-if="row.checkbox">
                                <q-checkbox v-model="row.data" :label="row.label" />
                            </div>
                            <div v-else>
                                <q-input
                                    v-model="row.data"
                                    :label="row.label"
                                    class="q-my-md"
                                    filled
                                    square
                                    clearable
                                >
                                </q-input>
                            </div>
                        </div>
                        <div v-else class="q-my-md">
                            <q-select
                                :ref="setSelectRef"
                                v-model="row.data"
                                :label="row.label"
                                :options="row.filtered_options"
                                :loading="row.loading"
                                class="tw-relative"
                                filled
                                use-input
                                use-chips
                                :menu-shrink="true"
                                input-debounce="0"
                                :multiple="row.options_multiple"
                                popup-content-class="tw-w-84 tw-z-50 tw-overflow-auto tw-absolute tw-top-0  tw-bottom-0"
                                menu-anchor="top start"
                                menu-self="bottom start"
                                @filter="
                                    (...params) => {
                                        filterFn(row.uuid, ...params);
                                    }
                                "
                                @add="clearFilter"
                            >
                                <template #selected-item="scope">
                                    <q-chip
                                        v-if="
                                            typeof scope.opt === 'object' ? scope.opt.id : scope.opt
                                        "
                                        removable
                                        :tabindex="scope.tabindex"
                                        class="q-my-xs my-chip"
                                        @remove="scope.removeAtIndex(scope.index)"
                                    >
                                        <div class="text-chip">
                                            {{
                                                typeof scope.opt !== 'object'
                                                    ? scope.opt
                                                    : scope.opt[row.track_by ?? 'name']
                                            }}
                                        </div>
                                    </q-chip>
                                </template>
                                <template #option="scope">
                                    <q-item v-bind="scope.itemProps">
                                        <q-item-section>
                                            <q-item-label>{{
                                                typeof scope.opt !== 'object'
                                                    ? scope.opt
                                                    : scope.opt[row.track_by ?? 'name']
                                            }}</q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </template>
                                <template #no-option="scope">
                                    <q-item>
                                        <q-item-section v-show="scope" class="text-grey">
                                            Sin resultado
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-select>
                        </div>
                    </div>
                </q-form>
            </q-card-section>
            <q-card-actions align="right">
                <q-btn
                    :label="confirmButton"
                    color="primary"
                    :loading="submitting"
                    @click.prevent="onOKClick"
                    @keydown.enter.prevent="onOKClick"
                />
                <q-btn :label="discardButton" color="primary" flat @click="onCancelClick" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
import { ref, onMounted, onBeforeUpdate } from 'vue';
import { useDialogPluginComponent } from 'quasar';
import { api } from "@/boot/axios";
import { useQuasar } from 'quasar';
import FileUploader from './FileUploader.vue';

export default {
    components: {
        FileUploader,
    },
    props: {
        columns: {
            type: Array,
            default: () => [],
        },
        confirmButton: {
            type: String,
            default: 'Ok',
        },
        discardButton: {
            type: String,
            default: 'Cancelar',
        },
        title: {
            type: String,
            default: 'Confirme la acción',
        },
        data: {
            type: Object,
            default: () => {},
        },
        selections: {
            type: Array,
            default: () => [],
        },
        selectionsLabel: {
            type: String,
            default: 'Elija una opcion',
        },
        action: {
            type: Object,
            default: () => {},
        },
        isDataTables: {
            type: Boolean,
            default: false,
        },
    },

    emits: [...useDialogPluginComponent.emits],

    setup(props) {
        // REQUIRED; must be called inside of setup()
        const $q = useQuasar();
        // REQUIRED; must be called inside of setup()
        const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent();
        const deepCopy = (array) =>
            array
                .filter((field) => {
                    return field['noDataField'] ? field.options : true;
                })
                .map((item) => Object.assign({}, item));
        const rows = deepCopy(props.columns);
        const submitting = ref(false);
        const selects = ref([]);
        const setSelectRef = (el) => {
            if (el) {
                selects.value.push(el);
            }
        };
        const fields = ref(
            rows.map((item) => {
                //spliteamos el nombre siempre para dar compatibilidad a datables y qtables sin agregar campos
                item.name = item.name.split('.')[0];
                if (item.options) {
                    item.uuid = crypto.randomUUID();
                    if (item.options_multiple === undefined) {
                        item.options_multiple = true;
                    }
                    item.data = props.data[item.name] ?? null;
                    item.options_values = undefined;
                    item.loading = false;
                    item.filtered_options = [];
                    //guardamos en el campo data las opciones que ya vienen en la seleccion de la tabla
                } else if (item.checkbox) {
                    item.data = props.data[item] ?? true;
                } else {
                    item.data = props.data[item?.name] ?? '';
                }

                return item;
            })
        );
        onBeforeUpdate(() => {
            selects.value = [];
        });
        onMounted(() => {
            fields.value.map((item) => {
                if (item.options) {
                    if (!item.options_data) {
                        item.loading = true;
                        api.get(item.options_url).then(({ data }) => {
                            item.options_values = data;
                            item.filtered_options = data;
                            item.data = item.data?.map((option) => {
                                return item.options_values.filter(
                                    (item) => item.id === option.id
                                )[0];
                            });
                            item.loading = false;
                        });
                    } else {
                        item.options_values = [...item.options_data];
                        item.filtered_options = [...item.options_data];
                    }
                }
            });
        });

        const filterFn = (uuid, ...params) => {
            const [val, update, abort] = params;
            const item = fields.value.find((item) => item.uuid === uuid);
            const needle = val.toLowerCase();
            const filterIfIsObject = (v) =>
                v[item.track_by ?? 'name'].toLowerCase().indexOf(needle) > -1;
            const filterIfIsString = (v) => v.toLowerCase().indexOf(needle) > -1;
            update(() => {
                if (val === '') {
                    item.filtered_options = [...item.options_values];
                } else {
                    item.filtered_options = item.options_values.filter((v) =>
                        typeof v === 'object' ? filterIfIsObject(v) : filterIfIsString(v)
                    );
                }
            });
        };

        const clearFilter = () => {
            selects.value.forEach((item) => {
                item.updateInputValue('');
            });
        };
        return {
            dialogRef,
            onDialogHide,
            fields,
            submitting,
            filterFn,
            clearFilter,
            setSelectRef,
            onOKClick: function () {
                const data = {};
                fields.value.map((field) => {
                    if (field.options && !field.options_multiple && field.data != null) {
                        field.data = field?.options_tag
                            ? field.data[field?.options_tag]
                            : field.data;
                    }
                    return field;
                });
                fields.value.map((field) =>
                    Object.defineProperty(data, field.name, {
                        value: field.data,
                        writable: true,
                        configurable: true,
                        enumerable: true,
                    })
                );
                submitting.value = true;
                if (props.action.url) {
                    api[props.action.method](props.action.url, { ...data })
                        .then((res) => {
                            submitting.value = false;
                            onDialogOK(res);
                        })
                        .catch((e) => {
                            submitting.value = false;
                            $q.notify({
                                message: Object.values(e.response.data.errors)[0].join('\n'),
                                color: 'red',
                            });
                        });
                } else {
                    onDialogOK(data);
                }
            },

            // we can passthrough onDialogCancel directly
            onCancelClick: onDialogCancel,
        };
    },
};
</script>
<style lang="scss">
.my-chip {
    height: auto;
}
.text-chip {
    white-space: break-spaces;
    word-break: break-all;
}
.tw-w-84 {
    width: 22rem;
}
</style>
