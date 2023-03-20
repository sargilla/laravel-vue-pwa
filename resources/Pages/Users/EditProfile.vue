<template>
    <q-page padding>
        <div class="row justify-center">
            <div class="col-6">
                <h6 class="q-pl-sm q-my-sm text-h6">Editar mi perfil</h6>
            </div>
        </div>
        <div class="row justify-center">
            <div class="col-12 col-md-6 q-mb-xl">
                <q-card class="q-my-sm">
                    <q-form
                        :autofocus="true"
                        autocomplete="off"
                        @submit="updateProfile"
                        @keyup="resetErrors"
                    >
                        <q-card-section class="q-gutter-md text-center">
                            <!-- <div class="row column items-center justify-center q-gutter-md">
                                <q-avatar size="150px">
                                    <q-img :src="avatar" spinner-color="white" class="avatar">
                                        <div class="absolute-full flex flex-center edit">
                                            <q-file
                                                accept=".jpg, image/*"
                                                borderless
                                                style="max-width: 50px"
                                                @update:model-value="onFileChange"
                                            >
                                                <template #default>
                                                    <div class="flex items-center justify-center">
                                                        <q-icon
                                                            name="edit"
                                                            color="white"
                                                            size="3em"
                                                        />
                                                    </div>
                                                </template>
                                            </q-file>
                                        </div>
                                    </q-img>
                                </q-avatar>
                            </div> -->
                            <q-input
                                v-model="name"
                                label="Nombre"
                                filled
                                :error-message="errorMessage('name')"
                                :error="isNotValid('name')"
                            />
                            <q-input
                                v-model="email"
                                label="Email"
                                filled
                                type="email"
                                :error-message="errorMessage('email')"
                                :error="isNotValid('email')"
                            />
                            <q-input
                                v-model="password"
                                label="Contraseña"
                                type="password"
                                filled
                                :error-message="errorMessage('password')"
                                :error="isNotValid('password')"
                                autocomplete="nope"
                            />
                            <q-input
                                v-model="password_confirmation"
                                filled
                                label="Repetir Contraseña"
                                type="password"
                                autocomplete="nope"
                            />
                        </q-card-section>

                        <!-- <q-card-section>
                            <q-card class="q-pa-lg">
                                <h4 class="q-my-sm">Notificaciones del sistema</h4>
                                <q-toggle
                                    v-for="(notificacion, key, index) in notificaciones"
                                    :key="index"
                                    v-model="notificacion.notificable"
                                    :label="notificacion.label"
                                >
                                </q-toggle>
                                <h4 class="q-my-sm">Notificaciones en escritorio y mobile</h4>
                                <div class="text-center">
                                    <q-btn
                                        color="primary"
                                        label="Volver a pedir recibir notificaciones"
                                        @click.prevent="removeNeverShowNotificactionBanner"
                                    />
                                    <div class="row justify-center items-center">
                                        <p class="text-body-1 q-mt-md col-8">
                                            Recuerde que debe resetear también el permiso de las
                                            notificaciones en su navegador para poder volver a
                                            vincular las notificaciones de la aplicación con su
                                            dispositivo
                                        </p>
                                    </div>
                                    <div class="row justify-center items-center">
                                        <div class="col-8 q-mt-md">
                                            <q-img src="/images/restablecer_permisos.jpg" />
                                        </div>
                                    </div>
                                </div>
                            </q-card>
                        </q-card-section> -->
                        <!-- <q-separator /> -->

                        <q-card-actions align="right">
                            <q-btn
                                color="primary"
                                :loading="enviando"
                                :disable="enviando"
                                type="submit"
                                style="width: 150px"
                            >
                                <template #loading>
                                    <div class="row">
                                        <q-spinner-hourglass color="white" class="on-left" />
                                        Guardando
                                    </div>
                                </template>
                                Guardar
                            </q-btn>
                        </q-card-actions>
                    </q-form>
                </q-card>
            </div>
        </div>
    </q-page>
</template>

<script setup>
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
import { LocalStorage } from 'quasar';
import { useRouter } from 'vue-router';
import { ref, onMounted, watch } from 'vue';

const route = useRouter();
const $q = useQuasar();
let name = ref('');
let email = ref('');
let enviando = ref(false);
let password = ref('');
let password_confirmation = ref('');
let errors = ref('');
const user = ref(LocalStorage.getItem('user'));

onMounted(() => {
    name.value = user.value.name;
    email.value = user.value.email;
});

const updateProfile = () => {
    enviando.value = true;
    const body = {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
    };

    api.patch('/users/editar-perfil', body)
        .then((response) => {
            enviando.value = false;
            $q.notify({
                type: 'positive',
                message: response.data.message,
            });
            LocalStorage.set('user', response.data.user);
        })
        .catch((e) => {
            errors.value = e.response.data.errors;
            enviando.value = false;
            $q.notify({
                message: 'Hubo un problema al guardar',
                type: 'negative',
            });
        });
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
    errors.value = '';
};
// const onFileChange = (file) => {
//     createImage(file);
// };

// const createImage = (file, type) => {
//     let reader = new FileReader();
//     reader.onload = (e) => {
//         avatar.value = e.target.result;
//         uploadFile(file);
//     };
//     reader.readAsDataURL(file);
// };

// const uploadFile = (file) => {
//     let data = new FormData();
//     data.append('nombre', avatarName.value);
//     data.append('archivo', file);
//     api.post('/users/avatar', data)
//         .then((response) => {
//             // console.log(response.data);
//             avatar.value = `/images/avatar/${response.data}`;
//         })
//         .catch((e) => {
//             $toastr.error(e.response.data.mensaje);
//         });
// };
// const removeImage = () => {
//     api.delete('/users/avatar').then(() => {
//         avatar.value = '';
//     });
// };
// const onRejected = () => {
//     console.log('mala imagen');
// };
// const removeNeverShowNotificactionBanner = () => {
//     $q.localStorage.remove('neverShowNotificationBanner');
//     location.reload();
// };
</script>

<style>
.avatar .edit {
    visibility: hidden;
    opacity: 0;
    transition: 0.3s;
}

.avatar:hover .edit {
    visibility: visible;
    opacity: 1;
    transition: 0.3s;
    background-color: rgba(0, 0, 0, 0.4);
}
</style>
