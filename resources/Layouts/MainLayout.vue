<template>
    <q-layout view="hHh lpR fFf" class="bg-grey-1">
        <q-header
            elevated
            class="bg-white text-grey-8 q-py-xs"
            height-hint="58"
        >
            <q-toolbar>
                <q-btn
                    flat
                    dense
                    round
                    @click="toggleLeftDrawer"
                    aria-label="Menu"
                    icon="menu"
                />
                <q-btn
                    flat
                    no-caps
                    no-wrap
                    class="q-ml-xs"
                    v-if="$q.screen.gt.xs"
                >
                    <q-img
                        src="https://cdn.quasar.dev/logo-v2/svg/logo.svg"
                        style="width: 28px; height: 28px"
                    />
                    <q-toolbar-title
                        shrink
                        class="text-weight-bold"
                        clickable
                        @click="goto('/')"
                    >
                        {{ app_name }}
                    </q-toolbar-title>
                </q-btn>
                <q-space />
                <div class="q-gutter-sm row items-center no-wrap" v-if="user">
                    <q-btn-dropdown rounded flat aria-label="Ver Acciones">
                        <template #label>
                            <q-avatar size="26px">
                                <img :src="avatar" alt="John Doe" />
                            </q-avatar>
                        </template>
                        <div class="row no-wrap q-pa-md">
                            <div
                                class="column items-center"
                                style="min-width: 200px"
                            >
                                <q-avatar size="72px">
                                    <img :src="avatar" alt="{{ user.name }}" />
                                </q-avatar>
                                <div
                                    class="text-subtitle1 q-mt-md q-mb-xs text-center"
                                >
                                    {{ user.name }}
                                </div>
                                <q-btn
                                    v-close-popup
                                    color="primary"
                                    label="Editar mi perfil"
                                    push
                                    size="sm"
                                    :to="{ name: 'Profile' }"
                                    class="q-mt-md"
                                />
                                <q-btn
                                    v-close-popup
                                    color="warning"
                                    label="Recargar App"
                                    push
                                    size="sm"
                                    class="q-my-md"
                                    @click.prevent="recargarApp"
                                />
                                <q-btn
                                    v-close-popup
                                    color="negative"
                                    label="Salir"
                                    push
                                    size="sm"
                                    @click="logout"
                                />
                            </div>
                        </div>
                    </q-btn-dropdown>
                </div>
            </q-toolbar>
        </q-header>
        <q-drawer
            v-model="leftDrawerOpen"
            show-if-above
            bordered
            class="bg-grey-2"
            :width="240"
        >
            <q-scroll-area class="fit">
                <q-list padding>
                    <q-item
                        v-for="link in linkFilter(pages)"
                        :key="link.text"
                        v-ripple
                        clickable
                        @click="goto(link.link)"
                    >
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-separator
                        class="q-my-md"
                        v-if="linkFilter(usuarios).length"
                    />
                    <q-item
                        v-for="link in linkFilter(usuarios)"
                        :key="link.text"
                        v-ripple
                        clickable
                        @click="goto(link.link)"
                    >
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>
                    <q-separator
                        class="q-my-md"
                        v-if="linkFilter(general).length"
                    />
                    <q-item
                        v-for="glink in linkFilter(general)"
                        :key="glink.text"
                        v-ripple
                        clickable
                        @click="goto(glink.link)"
                    >
                        <q-item-section avatar>
                            <q-icon color="grey" :name="glink.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ glink.text }}</q-item-label>
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-scroll-area>
        </q-drawer>
        <q-page-container>
            <router-view />
        </q-page-container>
    </q-layout>
</template>

<script setup>
import { ref, onUpdated } from "vue";
import { api } from "@/boot/axios";
import { pages, usuarios, general } from "./menus";
import { LocalStorage } from "quasar";
import { useRouter } from "vue-router";

const leftDrawerOpen = ref(false);
function toggleLeftDrawer() {
    leftDrawerOpen.value = !leftDrawerOpen.value;
}

const router = useRouter();
const avatar = ref("default-avatar.png");
const user = ref(LocalStorage.getItem("user"));
const app_name = import.meta.env.VITE_APP_NAME;

const recargarApp = () => location.reload();
const goto = (link) => router.push(link);
const logout = () =>
    api
        .post("auth/logout")
        .then(() => {
            LocalStorage.remove("user");
            goto("/login");
        })
        .catch((e) => console.log(e.response.data));

const linkFilter = (links) => {
    return links?.filter((link) => {
        if (link.permiso) {
            return user.value
                ? [...user.value.permissions].includes(link.permiso)
                : false;
        }
        return true;
    });
};
const refreshUser = () => {
    user.value = LocalStorage.getItem("user");
};

onUpdated(refreshUser);
</script>
<style lang="sass">
.YL
  &__toolbar-input-container
    min-width: 100px
    width: 55%
  &__toolbar-input-btn
    border-radius: 0
    border-style: solid
    border-width: 1px 1px 1px 0
    border-color: rgba(0,0,0,.24)
    max-width: 60px
    width: 100%
  &__drawer-footer-link
    color: inherit
    text-decoration: none
    font-weight: 500
    font-size: .75rem
    &:hover
      color: #000
</style>
