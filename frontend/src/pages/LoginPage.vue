<template>
  <q-page class="flex flex-center">
    <q-card style="width: 380px">
      <q-card-section>
        <div class="text-h6">Login</div>
      </q-card-section>

      <q-card-section class="q-gutter-md">
        <q-input
          v-model="email"
          label="E-mail"
          type="email"
          outlined
          :rules="[val => !!val || 'E-mail obrigatório']"
          lazy-rules
        />
        <q-input
          v-model="password"
          label="Senha"
          type="password"
          outlined
          :rules="[val => !!val || 'Senha obrigatória']"
          lazy-rules
        />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          label="Entrar"
          color="primary"
          :loading="carregando"
          @click="handleLogin"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
import { api } from 'src/boot/axios';

const router = useRouter();
const $q = useQuasar();

const email = ref('');
const password = ref('');
const carregando = ref(false);

const handleLogin = async (): Promise<void> => {
  if (!email.value || !password.value) {
    $q.notify({ type: 'warning', message: 'Preencha e-mail e senha.' });
    return;
  }

  carregando.value = true;

  try {
    const { data } = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    localStorage.setItem('token', data.access_token);
    await router.push('/pacientes');
  } catch (error: unknown) {
    const status = (error as { response?: { status?: number } })?.response?.status;

    if (status === 401) {
      $q.notify({ type: 'negative', message: 'E-mail ou senha incorretos.' });
    } else {
      $q.notify({ type: 'negative', message: 'Erro ao realizar login. Tente novamente.' });
    }
  } finally {
    carregando.value = false;
  }
};
</script>
