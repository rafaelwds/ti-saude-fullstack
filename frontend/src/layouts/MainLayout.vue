<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          v-if="mostrarMenu"
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleMenu"
        />
        <q-toolbar-title>Ti Saúde</q-toolbar-title>
        <q-space />
        <q-btn v-if="mostrarLogout" flat label="Sair" icon="logout" @click="fazerLogout" />
      </q-toolbar>
    </q-header>

    <q-drawer v-if="mostrarMenu" v-model="menuAberto" show-if-above bordered>
      <q-list>
        <q-item-label header>Menu</q-item-label>

        <q-item clickable v-ripple to="/pacientes" active-class="text-primary">
          <q-item-section avatar><q-icon name="people" /></q-item-section>
          <q-item-section>Pacientes</q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/especialidades" active-class="text-primary">
          <q-item-section avatar><q-icon name="medical_services" /></q-item-section>
          <q-item-section>Especialidades</q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/medicos" active-class="text-primary">
          <q-item-section avatar><q-icon name="person" /></q-item-section>
          <q-item-section>Médicos</q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/consultas" active-class="text-primary">
          <q-item-section avatar><q-icon name="event" /></q-item-section>
          <q-item-section>Consultas</q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/procedimentos" active-class="text-primary">
          <q-item-section avatar><q-icon name="healing" /></q-item-section>
          <q-item-section>Procedimentos</q-item-section>
        </q-item>

        <q-separator />

        <q-item clickable v-ripple to="/planos-saude" active-class="text-primary">
          <q-item-section avatar><q-icon name="health_and_safety" /></q-item-section>
          <q-item-section>Planos de Saúde</q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/vinculos" active-class="text-primary">
          <q-item-section avatar><q-icon name="link" /></q-item-section>
          <q-item-section>Vínculos</q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { api } from 'src/boot/axios';

const route = useRoute();
const router = useRouter();
const $q = useQuasar();

const menuAberto = ref(true);
const mostrarLogout = computed(() => route.path !== '/login');
const mostrarMenu = computed(() => route.path !== '/login');

const toggleMenu = (): void => {
  menuAberto.value = !menuAberto.value;
};

const fazerLogout = async (): Promise<void> => {
  try {
    await api.post('/logout');
  } catch {
    // ignora erro de rede no logout — o token já foi removido
  } finally {
    localStorage.removeItem('token');
    $q.notify({ type: 'info', message: 'Sessão encerrada.' });
    await router.push('/login');
  }
};
</script>
