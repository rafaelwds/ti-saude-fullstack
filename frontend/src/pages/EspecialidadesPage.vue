<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Especialidades</div>
      <q-btn label="Nova especialidade" color="primary" @click="abrirNova" />
    </div>

    <q-table
      :rows="especialidades"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhuma especialidade cadastrada"
      :loading="carregando"
    >
      <template #body-cell-acoes="props">
        <q-td>
          <q-btn flat icon="edit" @click="editar(props.row)" />
          <q-btn flat icon="delete" color="negative" @click="confirmarRemocao(props.row.id)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Nova' }} especialidade</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.espec_codigo" label="Código" outlined />
          <q-input
            v-model="form.espec_nome"
            label="Nome *"
            outlined
            :rules="[val => !!val || 'Nome é obrigatório']"
            lazy-rules
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Salvar" :loading="salvando" @click="salvar" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from 'src/boot/axios';

interface Especialidade {
  id: number | null;
  espec_codigo: string;
  espec_nome: string;
}

const $q = useQuasar();
const especialidades = ref<Especialidade[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);

const formInicial: Especialidade = { id: null, espec_codigo: '', espec_nome: '' };
const form = ref<Especialidade>({ ...formInicial });

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'espec_codigo', label: 'Código', field: 'espec_codigo', align: 'left' as const },
  { name: 'espec_nome', label: 'Nome', field: 'espec_nome', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const { data } = await api.get('/especialidades');
    especialidades.value = data.data ?? [];
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar especialidades.' });
  } finally {
    carregando.value = false;
  }
};

const abrirNova = (): void => {
  form.value = { ...formInicial };
  modalAberto.value = true;
};

const editar = (item: Especialidade): void => {
  form.value = { ...item };
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.espec_nome) {
    $q.notify({ type: 'warning', message: 'Nome é obrigatório.' });
    return;
  }

  salvando.value = true;
  const payload = { espec_codigo: form.value.espec_codigo || null, espec_nome: form.value.espec_nome };

  try {
    if (form.value.id) {
      await api.put(`/especialidades/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Especialidade atualizada.' });
    } else {
      await api.post('/especialidades', payload);
      $q.notify({ type: 'positive', message: 'Especialidade criada.' });
    }
    modalAberto.value = false;
    await carregar();
  } catch (error: unknown) {
    const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
    $q.notify({ type: 'negative', message: msg || 'Erro ao salvar.' });
  } finally {
    salvando.value = false;
  }
};

const confirmarRemocao = (id: number | null): void => {
  if (!id) return;
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover esta especialidade?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/especialidades/${id}`);
        $q.notify({ type: 'positive', message: 'Especialidade removida.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
