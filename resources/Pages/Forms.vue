<template>
    <q-page padding>
        <h6 class="text-h6">Forms</h6>

        <div class="q-pa-md" style="max-width: 300px">
            <q-field
                filled
                v-model="price"
                label="Price with v-money component"
                hint="Mask: $ #,###.00 #"
            >
                <template
                    v-slot:control="{
                        id,
                        floatingLabel,
                        modelValue,
                        emitValue,
                    }"
                >
                    <money
                        :id="id"
                        class="q-field__input text-right"
                        :model-value="modelValue"
                        @update:model-value="emitValue"
                        v-bind="moneyFormatForComponent"
                        v-show="floatingLabel"
                    />
                </template>
            </q-field>
        </div>
    </q-page>
</template>
<script setup>
import { ref } from "vue";

import vueAutonumeric from "vue-autonumeric";

const price = ref(10);
</script>
