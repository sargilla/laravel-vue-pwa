<template>
    <div class="q-pa-lg row items-start q-gutter-xl">
        <div v-for="(file, i) in previews" :key="i">
            <q-card class="my-card" flat bordered>
                <q-card-section horizontal>
                    <embed
                        :type="mime"
                        :src="file.link"
                        width="250"
                        height="250"
                        style="overflow-y: hidden"
                    />
                    <div style="position: absolute; inset: -5% -5%">
                        <q-btn
                            fab
                            padding="3px"
                            color="red"
                            icon="delete"
                            size="xs"
                            @click="remove(i)"
                        />
                    </div>
                </q-card-section>
            </q-card>
        </div>

        <q-card class="my-card" flat bordered>
            <div style="min-height: 250px; display: grid">
                <div class="q-mt-lg" style="place-self: center">
                    <input
                        id="inputFile"
                        type="file"
                        style="display: none"
                        multiple
                        :onchange="handleFiles"
                        :accept="mime"
                    />

                    <label
                        for="inputFile"
                        class="q-btn q-btn-item non-selectable no-outline q-btn--standard q-btn--rectangle q-btn--rounded bg-primary text-white q-btn--actionable q-focusable q-hoverable q-btn--fab"
                        style="
                            align-self: center;
                            justify-self: center;
                            border-radius: 100%;
                            background-color: primary;
                        "
                    >
                        <q-icon name="add" color="white" />
                    </label>
                </div>

                <span style="justify-self: center">Agregar pdf</span>
            </div>
        </q-card>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { api } from "@/boot/axios";
const files = ref([]);
const previews = ref("");

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    mime: {
        type: String,
        default: "application/pdf",
    },
});
const emits = defineEmits(["update:modelValue"]);
onMounted(() => {
    previews.value = props.modelValue;
});

const handleFiles = (e) => {
    files.value = [...e.target.files];
    files.value.map((file) => upload(file));
};

const upload = async (file) => {
    let data = new FormData();

    data.append("archivo", file);

    // const config = {
    //     onUploadProgress: function (progressEvent) {},
    // };
    try {
        const { data: res } = await api.post("expedientes/archivos", data);
        previews.value = [
            ...previews.value,
            { name: file.name, link: `storage/expedientes/${res}` },
        ];
        emits("update:modelValue", previews.value);
    } catch (e) {
        files.value = null;
    }
};

const remove = (index) => {
    previews.value.splice(index);
    emits("update:modelValue", previews.value);
};
</script>

<style scoped>
.my-card {
    width: 100%;
    max-width: 250px;
    position: relative;
    place-self: center;
}
</style>
