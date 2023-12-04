<template>
    <Money
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :options="moneyOptions"
    />
</template>

<script setup>
import Money from "v-money";

defineProps(["modelValue"]);
defineEmits(["update:modelValue"]);

const moneyOptions = {
    type: Object,
    default: () => ({
        decimal: ",",
        thousands: ".",
        prefix: "$ ",
        precision: 2,
        masked: false,
    }),
};
</script>
