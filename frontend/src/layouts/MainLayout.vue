<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title>Ti Saúde</q-toolbar-title>

        <q-space />

        <q-btn v-if="mostrarLogout" flat label="Sair" icon="logout" @click="fazerLogout" />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { api } from 'src/boot/axios';

const route = useRoute();
const router = useRouter();

const mostrarLogout = computed(() => route.path !== '/login');

const fazerLogout = async (): Promise<void> => {
  try {
    await api.post('/logout');
  } catch (error) {
    console.error('erro ao fazer logout', error);
  } finally {
    localStorage.removeItem('token');
    await router.push('/login');
  }
};
</script>
