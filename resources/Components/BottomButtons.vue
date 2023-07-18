<template>
    <q-page-sticky position="bottom-right">
        <div class="q-pa-md">
            <q-fab
                v-if="props.visible"
                color="primary"
                icon="keyboard_arrow_up"
                direction="up"
                glossy
            >
                <q-fab-action color="accent" icon="add" @click="emits('crear')" glossy />
                <q-fab-action color="secondary" icon="undo" @click="$router.go(-1)" glossy />
            </q-fab>
            <q-btn v-else color="primary" fab icon="undo" @click="$router.go(-1)" glossy />
        </div>
    </q-page-sticky>
</template>
<script setup="props, emits">
const emits = defineEmits(['crear']);
const props = defineProps({
    visible: { type: Boolean, default: true },
});
</script>
