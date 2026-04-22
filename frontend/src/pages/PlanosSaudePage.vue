<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Planos de Saúde</div>
      <q-btn label="Novo plano" color="primary" @click="abrirNovo" />
    </div>

    <q-table
      :rows="planos"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhum plano de saúde cadastrado"
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
      <q-card style="min-width: 420px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} plano de saúde</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.plano_codigo" label="Código" outlined />
          <q-input
            v-model="form.plano_descricao"
            label="Descrição *"
            outlined
            :rules="[val => !!val || 'Descrição é obrigatória']"
            lazy-rules
          />
          <q-input
            v-model="form.plano_telefone"
            label="Telefone"
            outlined
            mask="(##) ####-####"
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

interface PlanoSaude {
  id: number | null;
  plano_codigo: string;
  plano_descricao: string;
  plano_telefone: string;
}

const $q = useQuasar();
const planos = ref<PlanoSaude[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);

const formInicial: PlanoSaude = { id: null, plano_codigo: '', plano_descricao: '', plano_telefone: '' };
const form = ref<PlanoSaude>({ ...formInicial });

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'plano_codigo', label: 'Código', field: 'plano_codigo', align: 'left' as const },
  { name: 'plano_descricao', label: 'Descrição', field: 'plano_descricao', align: 'left' as const },
  { name: 'plano_telefone', label: 'Telefone', field: 'plano_telefone', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const { data } = await api.get('/planos-saude');
    planos.value = data.data ?? [];
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar planos.' });
  } finally {
    carregando.value = false;
  }
};

const abrirNovo = (): void => {
  form.value = { ...formInicial };
  modalAberto.value = true;
};

const editar = (item: PlanoSaude): void => {
  form.value = { ...item };
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.plano_descricao) {
    $q.notify({ type: 'warning', message: 'Descrição é obrigatória.' });
    return;
  }

  salvando.value = true;
  const payload = {
    plano_codigo: form.value.plano_codigo || null,
    plano_descricao: form.value.plano_descricao,
    plano_telefone: form.value.plano_telefone || null,
  };

  try {
    if (form.value.id) {
      await api.put(`/planos-saude/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Plano atualizado.' });
    } else {
      await api.post('/planos-saude', payload);
      $q.notify({ type: 'positive', message: 'Plano criado.' });
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
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover este plano?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/planos-saude/${id}`);
        $q.notify({ type: 'positive', message: 'Plano removido.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
