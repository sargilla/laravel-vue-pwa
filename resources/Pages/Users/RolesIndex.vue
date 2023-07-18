<template>
    <q-page padding>
        <h6 class="q-pl-sm q-my-sm text-h6">Roles</h6>
        <q-table
            v-model:pagination="paginationSettings"
            :rows="roles"
            :columns="columns"
            :loading="loading"
            :filter="filter"
            row-key="id"
            class="q-mb-lg"
            @request="onRequest"
        >
            <template #top-right>
                <q-input
                    ref="searchInput"
                    v-model="filter"
                    borderless
                    dense
                    debounce="300"
                    placeholder="Busqueda"
                >
                    <template #append>
                        <q-icon name="search" @click="searchInput.focus()" />
                    </template>
                </q-input>
            </template>
            <template #body-cell-actions="props">
                <q-td :props="props">
                    <q-btn
                        flat
                        round
                        color="primary"
                        icon="edit"
                        size="sm"
                        @click="edit(props.row)"
                    />
                    <q-btn
                        flat
                        round
                        color="negative"
                        icon="delete"
                        size="sm"
                        @click="remove(props.row)"
                    />
                </q-td>
            </template>
        </q-table>
        <Buttons @crear="create()" />
    </q-page>
</template>

<script setup>
import { useQuasar } from "quasar";
import { onMounted, ref } from "vue";
import AbmHelper from "@/Components/AbmHelper.vue";
import Buttons from "@/Components/BottomButtons.vue";
import { DataTablesHook } from "@/Hooks/DataTablesHook";
const columns = [
    {
        name: "name",
        field: "name",
        label: "Rol",
        sortable: true,
        align: "left",
        style: "white-space: break-spaces",
    },
    {
        name: "permissions.name",
        field: "permissions_string",
        label: "Permisos",
        sortable: false,
        align: "left",
        options: true,
        options_url: `/permissions`,
        style: "white-space: break-spaces",
    },
    {
        name: "actions",
        field: "actions",
        label: "Acciones",
        sortable: false,
        searchable: false,
        align: "center",
        noDataField: true,
        style: "width: 60px",
    },
];

const roles = ref([]);
const $q = useQuasar();
const searchInput = ref(null);
const permissions = ref([]);
const { filter, paginationSettings, loading, onRequest } = DataTablesHook({
    sortBy: "name",
    url: "/getRoles",
    fetchData: roles,
    columns,
});

onMounted(async () => {
    onRequest({});
});

const edit = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        persistent: true,
        componentProps: {
            title: `Editar el rol ${data.name}`,
            columns,
            selections: permissions.value,
            confirmButton: "Guardar",
            data: { ...data },
            action: {
                method: "put",
                url: `/roles/${data.id}`,
            },
        },
    }).onOk(() => onRequest({}));
};

const create = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        persistent: true,
        componentProps: {
            title: "Nuevo rol",
            columns,
            confirmButton: "Crear",
            data: { ...data },
            action: {
                method: "post",
                url: "/roles",
            },
        },
    }).onOk(() => onRequest({}));
};
const remove = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        persistent: true,
        componentProps: {
            title: `Desea eliminar el rol ${data.name}?`,
            action: {
                method: "delete",
                url: `/roles/${data.id}`,
            },
        },
    }).onOk(() => onRequest({}));
};
</script>
