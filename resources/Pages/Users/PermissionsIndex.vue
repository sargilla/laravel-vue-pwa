<template>
    <q-page padding>
        <h6 class="q-pl-sm q-my-sm text-h6">Permisos</h6>
        <q-table
            v-model:pagination="paginationSettings"
            :rows="permissions"
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
            <template #body-cell-roles="props">
                <q-td :props="props" class="break">
                    {{ props.row.roles_string }}
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
        field: "name",
        name: "name",
        sortable: true,
        label: "Permiso",
        align: "left",
    },
    {
        name: "roles.name",
        field: "roles_string",
        label: "Roles",
        sortable: true,
        searchable: true,
        align: "left",
        options: true,
        options_url: `/roles`,
    },
    {
        field: "actions",
        name: "actions",
        label: "Acciones",
        searchable: false,
        sortable: false,
        align: "center",
        noDataField: true,
        style: "width: 60px",
    },
];
const $q = useQuasar();
const permissions = ref([]);
const searchInput = ref(null);
const { filter, paginationSettings, loading, onRequest } = DataTablesHook({
    sortBy: "name",
    url: "/getPermissions",
    fetchData: permissions,
    columns,
});
onMounted(async () => {
    onRequest({});
});

const create = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        persistent: true,
        componentProps: {
            title: "Nuevo permiso",
            columns,
            confirmButton: "Crear",
            data: { ...data },
            action: {
                method: "post",
                url: "/permissions",
            },
        },
    }).onOk(() => onRequest({}));
};

const edit = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        persistent: true,
        componentProps: {
            title: `Editar permiso ${data.name}`,
            columns,
            confirmButton: "Guardar",
            data: { ...data },
            action: {
                method: "patch",
                url: `/permissions/${data.id}`,
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
                url: `/permissions/${data.id}`,
            },
        },
    }).onOk(() => onRequest({}));
};
</script>
