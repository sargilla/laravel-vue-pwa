import { onMounted, onUnmounted } from 'vue';
import { onBeforeRouteLeave } from 'vue-router';

/** Un hook que recibe una funcion que devuelve un boolean para evitar que se pueda salir de la pagina o un mensaje para mostrar al querer cambiar de url. */
export function useBeforeUnload(callback: () => boolean | string) {
    const cb = (event: Event) => {
        const result = callback();
        if (result) {
            event.preventDefault();
            event.returnValue = false;
            return Boolean(result);
        }
    };
    onUnmounted(() => {
        console.log('onUnmounted');
        window.removeEventListener('beforeunload', cb);
    });
    onMounted(() => {
        console.log('onMounted');
        window.addEventListener('beforeunload', cb);
    });

    onBeforeRouteLeave((to, from) => {
        const result = callback();
        if (result) {
            const answer = window.confirm(
                typeof result === 'string' ? result : 'Estas seguro de dejar cambios sin guardar?'
            );
            // cancel the navigation and stay on the same page
            if (!answer) return false;
        }
    });
    return false;
}
