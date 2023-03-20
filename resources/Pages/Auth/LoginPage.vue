<template>
    <q-page class="row justify-center items-center">
        <q-card class="q-pa-lg q-animate--fade">
            <div class="text-center q-py-md">
                <img
                    src="https://cdn.quasar.dev/logo-v2/svg/logo.svg"
                    style="width: 56px; height: 56px"
                />
            </div>
            <q-form :autofocus="true" class="q-gutter-md" @submit="login">
                <q-card-section>
                    <q-input
                        v-model="email"
                        square
                        filled
                        type="email"
                        lazy-rules
                        label="Email"
                        class="q-mb-md"
                        :rules="[
                            (val) =>
                                (val && val.length > 0) || 'Ingrese un email',
                        ]"
                    >
                        <template #prepend> <q-icon name="person" /> </template>
                    </q-input>
                    <q-input
                        v-model="password"
                        square
                        filled
                        type="password"
                        label="Contraseña"
                        lazy-rules
                        autocomplete="on"
                        :rules="[
                            (val) =>
                                (val && val.length > 0) ||
                                'Ingrese un password',
                        ]"
                    >
                        <template #prepend> <q-icon name="lock" /> </template>
                    </q-input>
                    <!-- <q-checkbox v-model="rememberMe" label="Recordarme" lass="q-gutter-sm " /> -->
                </q-card-section>
                <q-card-actions>
                    <p v-show="error" class="text-negative text-center">
                        {{ error }}
                    </p>
                    <q-btn
                        unelevated
                        color="primary"
                        size="lg"
                        class="full-width"
                        label="Ingresar"
                        type="submit"
                        :disable="enviando"
                        :loading="enviando"
                    >
                        <template #loading>
                            <div class="row">
                                <q-spinner color="white" class="q-mr-md" />
                                Ingresando
                            </div>
                        </template>
                    </q-btn>
                </q-card-actions>
            </q-form>
            <q-card-section class="text-center q-pa-none">
                <p class="text-grey-6 q-mb-none">
                    <q-btn flat rounded color="primary" to="/send-email"
                        >Olvido su contraseña?</q-btn
                    >
                </p>
            </q-card-section>
        </q-card>
    </q-page>
</template>

<script setup>
import { ref } from "vue";
import { api, base } from "@/boot/axios";
import { useRouter } from "vue-router";
import { LocalStorage } from "quasar";

const error = ref("");
const email = ref("");
const password = ref("");
const router = useRouter();
const enviando = ref(false);
// const rememberMe = ref(false);

const goto = (link) => router.push(link);

const login = async () => {
    // await base.get("/sanctum/csrf-cookie");
    api.post("auth/login", {
        email: email.value,
        password: password.value,
        // rememberMe: rememberMe.value,
    })
        .then((res) => {
            LocalStorage.set("user", res.data.user);
            let gotoLink =
                router.currentRoute.value.query.redirect ?? "/dashboard";
            goto(gotoLink);
        })
        .catch((e) => console.log(e.response.data));
};
</script>

<style scoped></style>
