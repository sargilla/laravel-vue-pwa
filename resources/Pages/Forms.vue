<template>
    <q-page padding>
        <h6 class="text-h6">Forms</h6>

        <div class="q-pa-md" style="max-width: 300px">
            <q-input
                filled
                v-model="price"
                label="Price with 2 decimals"
                mask="#.##"
                fill-mask="#"
                reverse-fill-mask
                hint="Mask: #.00"
                input-class="text-right"
            />
        </div>
    </q-page>
</template>
<script setup>
import { ref } from "vue";

const price = ref(10);
</script>
