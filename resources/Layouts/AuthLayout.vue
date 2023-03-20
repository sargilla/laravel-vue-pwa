<template>
    <q-layout view="hHh Lpr fFf">
        <q-page-container>
            <router-view />
        </q-page-container>
    </q-layout>
</template>

<script>
import { defineComponent } from 'vue';
export default defineComponent({
    name: 'App',
});
</script>
