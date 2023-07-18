import { ref, onBeforeUpdate } from 'vue';

export const useClearSelect = () => {
    const selectRef = ref({});

    onBeforeUpdate(() => {
        selectRef.value = null;
    });

    const setSelectRef = (el) => {
        if (el) {
            selectRef.value = el;
        }
    };
    const clearFilter = () => {
        selectRef.value.updateInputValue('');
    };
    return [setSelectRef, clearFilter];
};
