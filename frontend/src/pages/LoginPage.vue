<template>
  <q-page class="flex flex-center">
    <q-card style="width: 380px">
      <q-card-section>
        <div class="text-h6">Login</div>
      </q-card-section>

      <q-card-section class="q-gutter-md">
        <q-input v-model="email" label="E-mail" outlined />
        <q-input v-model="password" label="Senha" type="password" outlined />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn label="Entrar" color="primary" @click="handleLogin" />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { api } from 'src/boot/axios';

const router = useRouter();

const email = ref('');
const password = ref('');

const handleLogin = async (): Promise<void> => {
  try {
    console.log('chamou');
    const { data } = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    console.log('data ->', data);

    localStorage.setItem('token', data.access_token);
    await router.push('/pacientes');
  } catch (error) {
    console.error(error);
    alert('Login inválido');
  }
};
</script>
