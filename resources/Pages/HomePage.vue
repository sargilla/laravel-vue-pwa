<template>
    <q-page padding>
        <div>
            Puedes <router-link to="login">ingresar</router-link> para ver todas
            las funcionalidades olaaaaaaa
        </div>
        <div>{{ message }}</div>
    </q-page>
</template>

<script setup>
import { api } from "@/boot/axios";
import { ref, onMounted } from "vue";
const message = ref();
onMounted(() => {
    api.get("message")
        .then(({ data }) => (message.value = data))
        .catch((e) => console.log(e.response));
});
</script>

<style lang="scss" scoped></style>
