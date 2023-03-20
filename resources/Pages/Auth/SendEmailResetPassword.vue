<template>
    <q-page class="row justify-center items-center">
        <q-img
            src="images/congreso.jpg"
            fit="cover"
            class="header-img absolute-bottom"
            position="50% 100%"
            height="100%"
        >
        </q-img>
        <div class="column">
            <div class="row">
                <q-card square bordered class="q-pa-lg shadow-1">
                    <h5 class="text-h5 text-center q-my-md">
                        Plataforma de Información Legislativa
                    </h5>

                    <q-form
                        :autofocus="true"
                        class="q-gutter-sm"
                        @submit="sendEmail"
                    >
                        <q-card-section>
                            <q-input
                                v-model="email"
                                square
                                filled
                                type="email"
                                label="Email"
                                lazy-rules
                                class="q-gutter-sm"
                                :rules="[
                                    (val) =>
                                        (val && val.length > 0) ||
                                        'Ingrese su email',
                                ]"
                            >
                                <template #prepend>
                                    <q-icon name="person" />
                                </template>
                            </q-input>
                        </q-card-section>
                        <q-card-actions>
                            <q-btn
                                unelevated
                                color="primary"
                                size="lg"
                                class="full-width"
                                :loading="enviando"
                                label="Recuperar contraseña"
                                type="submit"
                                :disable="enviando"
                            >
                                <template #loading>
                                    <div class="row">
                                        <q-spinner
                                            color="white"
                                            class="q-mr-sm"
                                        />
                                        Enviando
                                    </div>
                                </template>
                            </q-btn>
                        </q-card-actions>
                    </q-form>
                    <q-card-section class="text-center">
                        <p class="text-grey-6">
                            <q-btn flat rounded color="primary" to="/login"
                                >Volver a ingresar</q-btn
                            >
                        </p>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </q-page>
</template>

<script>
import { ref } from "vue";
import { api } from "@/boot/axios";
import { useQuasar } from "quasar";
export default {
    name: "SendEmailResetPassword",
    setup() {
        const $q = useQuasar();
        const email = ref("");
        let mensaje = ref("");
        let enviando = ref(false);
        let errors = ref([]);
        const sendEmail = () => {
            enviando.value = true;
            api.post("/auth/reset-email", { email: email.value })
                .then((response) => {
                    // console.log(response.data.status);
                    mensaje.value = response.data.status;
                    $q.notify({
                        color: "positive",
                        message: mensaje.value,
                    });

                    enviando.value = false;
                })
                .catch((e) => {
                    errors.value = e.response.data.errors;
                    enviando.value = false;
                    $q.notify({
                        color: "negative",
                        message: errors.value.email[0],
                    });
                });
        };

        return {
            sendEmail,
            mensaje,
            email,
            enviando,
            errors,
        };
    },
};
</script>
<style scoped>
.q-card {
    max-width: 400px;
}
</style>
