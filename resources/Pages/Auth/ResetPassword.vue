<template>
    <q-page class="bg-light-white window-height window-width row justify-center items-center">
        <q-img
            src="images/congreso.jpg"
            fit="cover"
            class="header-img absolute-bottom"
            position="50% 100%"
            height="100vh"
        />
        <div class="column">
            <div class="row">
                <q-card square bordered class="q-pa-lg shadow-1">
                    <h5 class="text-h5 text-center q-my-md">
                        Plataforma de Información Legislativa
                    </h5>

                    <q-form :autofocus="true" class="q-gutter-sm" @submit.prevent="resetPassword">
                        <q-card-section>
                            <q-input
                                v-model="email"
                                square
                                filled
                                type="email"
                                label="Email"
                                lazy-rules
                                class="q-gutter-sm"
                                :rules="[(val) => (val && val.length > 0) || 'Ingrese un email']"
                            >
                                <template #prepend> <q-icon name="person" /> </template>
                            </q-input>

                            <q-input
                                v-model="password"
                                square
                                filled
                                type="password"
                                label="Contraseña"
                                class="q-gutter-sm"
                                lazy-rules
                                :rules="[
                                    (val) => (val && val.length > 0) || 'Ingrese nuevo password',
                                ]"
                            >
                                <template #prepend> <q-icon name="lock" /> </template>
                            </q-input>
                            <q-input
                                v-model="password_confirmation"
                                square
                                filled
                                type="password"
                                label="Repetir Contraseña"
                                class="q-gutter-sm"
                                lazy-rules
                                :rules="[
                                    (val) => (val && val.length > 0) || 'Repitea nuevo password',
                                ]"
                            >
                                <template #prepend> <q-icon name="lock" /> </template>
                            </q-input>
                        </q-card-section>
                        <q-card-actions>
                            <p
                                v-for="(error, index) in errors.password"
                                v-show="errors"
                                :key="index"
                                class="text-negative text-center"
                            >
                                {{ error }}
                            </p>
                            <q-btn
                                unelevated
                                color="primary"
                                size="lg"
                                class="full-width"
                                label="Ingresar"
                                type="submit"
                            />
                        </q-card-actions>
                    </q-form>
                </q-card>
            </div>
        </div>
    </q-page>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default {
    name: 'ResetPassword',
    setup() {
        const $q = useQuasar();
        const token = ref('');
        const email = ref('');
        const password = ref('');
        const password_confirmation = ref('');
        const route = useRoute();
        const router = useRouter();
        let errors = ref('');
        onMounted(() => {
            token.value = route.params.token;
        });
        const resetPassword = () => {
            const body = {
                email: email.value,
                token: token.value,
                password: password.value,
                password_confirmation: password.value,
            };

            api.post('/auth/reset-password', body)
                .then((response) => {
                    $q.notify({
                        color: 'positive',
                        message: response.data.status,
                    });
                    router.push({ path: '/login' });
                })
                .catch((e) => {
                    errors.value = e.response.data.errors;
                });
        };
        return {
            onMounted,
            resetPassword,
            email,
            token,
            password,
            password_confirmation,
            errors,
        };
    },
};
</script>
