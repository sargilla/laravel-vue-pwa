<template>
    <q-page padding>
        <h6 class="q-pl-sm q-my-sm text-h6">Usuarios</h6>
        <q-table
            v-model:pagination="paginationSettings"
            :rows="users"
            :columns="columnsTable"
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
                    <!-- <q-btn
                        flat
                        round
                        color="secondary"
                        icon="mdi-eye"
                        size="sm"
                        @click="
                            $router.push({
                                name: 'UsersShow',
                                params: { id: props.row.id, nombre: props.row.name },
                            })
                        "
                    /> -->
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
        <BottomButtons @crear="create()" />
    </q-page>
</template>

<script setup>
import { useQuasar } from 'quasar';
import { onMounted, ref } from 'vue';
import AbmHelper from '@/Components/AbmHelper.vue';
import { DataTablesHook } from '@/Hooks/DataTablesHook';
import BottomButtons from '@/Components/BottomButtons.vue';

const users = ref([]);
const $q = useQuasar();
const searchInput = ref(null);
const columnsTable = [
    {
        field: 'name',
        name: 'name',
        sortable: true,
        label: 'Nombre',
        align: 'left',
        style: 'white-space:normal',
    },
    {
        field: 'email',
        name: 'email',
        label: 'Email',
        sortable: true,
        align: 'left',
    },
    {
        name: 'roles.name',
        field: 'roles_string',
        label: 'Roles',
        align: 'left',
        options: true,
        options_url: `/roles`,
        style: 'white-space:break-spaces',
    },
    {
        field: 'actions',
        name: 'actions',
        label: 'Acciones',
        searchable: false,
        sortable: false,
        align: 'center',
        noDataField: true,
        style: 'width: 60px',
    },
];

const { filter, paginationSettings, loading, onRequest } = DataTablesHook({
    sortBy: 'name',
    url: '/getUsers',
    fetchData: users,
    columns: [...columnsTable],
});

onMounted(async () => {
    onRequest({});
});

const create = (data = {}) => {
    $q.dialog({
        component: AbmHelper,
        cancel: true,
        componentProps: {
            title: 'Nuevo usuario',
            columns: [
                ...columnsTable,
                { label: 'Contraseña', field: 'password', name: 'password' },
                {
                    label: 'Confirmacion de contraseña',
                    field: 'password_confirmation',
                    name: 'password_confirmation',
                },
            ],
            confirmButton: 'Crear',
            data: { ...data },
            action: {
                method: 'post',
                url: '/users',
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
            title: 'Editar Usuario',
            columns: [
                ...columnsTable,
                { label: 'Contraseña', name: 'password' },
                { label: 'Confirmacion de contraseña', name: 'password_confirmation' },
            ],

            confirmButton: 'Guardar',
            data: { ...data },
            action: {
                method: 'patch',
                url: `/users/${data.id}`,
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
            title: `Desea eliminar el usuario ${data.name}?`,
            action: {
                method: 'delete',
                url: `/users/${data.id}`,
            },
        },
    }).onOk(() => onRequest({}));
};
</script>
