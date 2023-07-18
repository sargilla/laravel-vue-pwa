<template>
    <q-page padding>
        <h6 class="q-pl-sm q-my-sm text-h6">Access Tokens</h6>
        <q-card>
            <q-card-section>
                <div class="text-h6">Tokens Creados</div>
            </q-card-section>
            <q-markup-table v-if="tokens.length > 0">
                <thead>
                    <tr>
                        <th class="text-left">Nombre</th>
                        <th class="text-center">Expira</th>
                        <th class="text-center">Último uso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(token, index) in tokens" :key="index">
                        <td class="text-left">{{ token.name }}</td>
                        <td class="text-center">{{ token.expires_at }}</td>
                        <td class="text-center">{{ token.last_used_at }}</td>
                        <td class="text-center" style="width: 60px">
                            <q-btn
                                flat
                                round
                                color="negative"
                                icon="delete"
                                size="sm"
                                @click="borrarToken(token)"
                            />
                        </td>
                    </tr>
                </tbody>
            </q-markup-table>
            <q-card v-else>
                <q-card-section class="bg-warning text-white text-center">
                    <div class="text-h6">Todavía no tienes tokens</div>
                </q-card-section>
            </q-card>
        </q-card>
        <q-card class="q-my-sm"
            ><q-form
                :autofocus="true"
                autocomplete="off"
                @submit="createToken"
                @keyup="resetErrors"
            >
                <q-card-section class="bg-primary text-white">
                    <div class="text-h6">Crear nuevo access token</div>
                </q-card-section>
                <q-card-section>
                    <q-input
                        v-model="token_name"
                        label="Nombre"
                        filled
                        :error-message="errorMessage('name')"
                        :error="isNotValid('name')"
                    >
                        <template #after>
                            <q-btn
                                type="submit"
                                color="primary"
                                :loading="enviando"
                                :disable="enviando"
                                @click="createToken"
                            >
                                <template #loading>
                                    <div class="row">
                                        <q-spinner-hourglass
                                            color="white"
                                            class="on-left"
                                        />
                                        Creando
                                    </div>
                                </template>
                                Crear Token
                            </q-btn>
                        </template>
                    </q-input>
                </q-card-section>
            </q-form>
        </q-card>
    </q-page>
</template>

<script setup>
import { api } from "@/boot/axios";
import { useQuasar, copyToClipboard } from "quasar";
import { ref, onMounted } from "vue";

const $q = useQuasar();
const token_name = ref("");
const enviando = ref(false);
const errors = ref("");
const tokens = ref([]);

onMounted(() => getTokens());

const getTokens = async () => {
    let { data } = await api.get("access-token");
    tokens.value = data;
};

const copyToken = async (text) => {
    await copyToClipboard(text);
    $q.notify({
        type: "positive",
        message: "Copiado!",
    });
};

const createToken = () => {
    enviando.value = true;
    api.post("/access-token", { token_name: token_name.value })
        .then(({ data }) => {
            enviando.value = false;
            $q.notify({
                type: "positive",
                multiLine: true,
                message: `${data.message} <strong>${data.token}</strong>`,
                timeout: 0,
                html: true,
                actions: [
                    {
                        icon: "mdi-content-copy",
                        color: "white",
                        noDismiss: true,
                        handler: () => copyToken(data.token),
                    },
                    { icon: "close", color: "white" },
                ],
            });
            getTokens();
        })
        .catch((e) => {
            errors.value = e.response.data.errors;
            enviando.value = false;
            $q.notify({
                message: "Hubo un problema al guardar",
                type: "negative",
            });
        });
};

const borrarToken = async (token) => {
    const { data } = await api.delete("/access-token/" + token.id);
    if (data) {
        $q.notify({
            message: "Se borro el token correctamente",
            type: "positive",
        });
        getTokens();
    } else {
        $q.notify({
            message: "No se pudo borrar",
            type: "negative",
        });
    }
};

const isNotValid = (field) => {
    return errors.value ? errors.value.hasOwnProperty(field) : false;
};

const errorMessage = (field) => {
    if (errors.value && errors.value[field]) {
        return errors.value[field][0];
    }
};

const resetErrors = () => {
    errors.value = "";
};
</script>
