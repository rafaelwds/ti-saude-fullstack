<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Procedimentos</div>
      <q-btn label="Novo procedimento" color="primary" @click="abrirNovo" />
    </div>

    <q-table
      :rows="procedimentos"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhum procedimento cadastrado"
      :loading="carregando"
    >
      <template #body-cell-proc_valor="props">
        <q-td>{{ formatarValor(props.row.proc_valor) }}</q-td>
      </template>
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
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} procedimento</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.proc_codigo" label="Código" outlined />
          <q-input
            v-model="form.proc_nome"
            label="Nome *"
            outlined
            :rules="[val => !!val || 'Nome é obrigatório']"
            lazy-rules
          />
          <q-input
            v-model="form.proc_valor"
            label="Valor (R$) *"
            outlined
            type="number"
            min="0"
            step="0.01"
            prefix="R$"
            :rules="[val => !!val || 'Valor é obrigatório', val => Number(val) >= 0 || 'Valor inválido']"
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

interface Procedimento {
  id: number | null;
  proc_codigo: string;
  proc_nome: string;
  proc_valor: string | number;
}

const $q = useQuasar();
const procedimentos = ref<Procedimento[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);

const formInicial: Procedimento = { id: null, proc_codigo: '', proc_nome: '', proc_valor: '' };
const form = ref<Procedimento>({ ...formInicial });

const formatarValor = (val: string | number) =>
  `R$ ${Number(val).toFixed(2).replace('.', ',')}`;

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'proc_codigo', label: 'Código', field: 'proc_codigo', align: 'left' as const },
  { name: 'proc_nome', label: 'Nome', field: 'proc_nome', align: 'left' as const },
  { name: 'proc_valor', label: 'Valor', field: 'proc_valor', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const { data } = await api.get('/procedimentos');
    procedimentos.value = data.data ?? [];
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar procedimentos.' });
  } finally {
    carregando.value = false;
  }
};

const abrirNovo = (): void => {
  form.value = { ...formInicial };
  modalAberto.value = true;
};

const editar = (item: Procedimento): void => {
  form.value = { ...item };
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.proc_nome || form.value.proc_valor === '') {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios.' });
    return;
  }

  salvando.value = true;
  const payload = {
    proc_codigo: form.value.proc_codigo || null,
    proc_nome: form.value.proc_nome,
    proc_valor: Number(form.value.proc_valor),
  };

  try {
    if (form.value.id) {
      await api.put(`/procedimentos/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Procedimento atualizado.' });
    } else {
      await api.post('/procedimentos', payload);
      $q.notify({ type: 'positive', message: 'Procedimento criado.' });
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
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover este procedimento?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/procedimentos/${id}`);
        $q.notify({ type: 'positive', message: 'Procedimento removido.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
