<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Médicos</div>
      <q-btn label="Novo médico" color="primary" @click="abrirNovo" />
    </div>

    <q-table
      :rows="medicos"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhum médico cadastrado"
      :loading="carregando"
    >
      <template #body-cell-especialidade="props">
        <q-td>{{ props.row.especialidade?.espec_nome ?? '-' }}</q-td>
      </template>
      <template #body-cell-acoes="props">
        <q-td>
          <q-btn flat icon="edit" @click="editar(props.row)" />
          <q-btn flat icon="delete" color="negative" @click="confirmarRemocao(props.row.id)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 440px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} médico</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.med_codigo" label="Código" outlined />
          <q-input
            v-model="form.med_nome"
            label="Nome *"
            outlined
            :rules="[val => !!val || 'Nome é obrigatório']"
            lazy-rules
          />
          <q-input
            v-model="form.med_crm"
            label="CRM *"
            outlined
            :rules="[val => !!val || 'CRM é obrigatório']"
            lazy-rules
          />
          <q-select
            v-model="form.especialidade_id"
            :options="opcoesEspecialidade"
            label="Especialidade *"
            outlined
            emit-value
            map-options
            :rules="[val => !!val || 'Especialidade é obrigatória']"
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

interface Especialidade { id: number; espec_nome: string; }
interface Medico {
  id: number | null;
  med_codigo: string;
  med_nome: string;
  med_crm: string;
  especialidade_id: number | null;
  especialidade?: Especialidade;
}

const $q = useQuasar();
const medicos = ref<Medico[]>([]);
const opcoesEspecialidade = ref<{ label: string; value: number }[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);

const formInicial: Medico = { id: null, med_codigo: '', med_nome: '', med_crm: '', especialidade_id: null };
const form = ref<Medico>({ ...formInicial });

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'med_codigo', label: 'Código', field: 'med_codigo', align: 'left' as const },
  { name: 'med_nome', label: 'Nome', field: 'med_nome', align: 'left' as const },
  { name: 'med_crm', label: 'CRM', field: 'med_crm', align: 'left' as const },
  { name: 'especialidade', label: 'Especialidade', field: 'especialidade', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const [resMedicos, resEspec] = await Promise.all([
      api.get('/medicos'),
      api.get('/especialidades'),
    ]);
    medicos.value = resMedicos.data.data ?? [];
    opcoesEspecialidade.value = (resEspec.data.data ?? []).map(
      (e: Especialidade) => ({ label: e.espec_nome, value: e.id })
    );
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar dados.' });
  } finally {
    carregando.value = false;
  }
};

const abrirNovo = (): void => {
  form.value = { ...formInicial };
  modalAberto.value = true;
};

const editar = (item: Medico): void => {
  form.value = { ...item };
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.med_nome || !form.value.med_crm || !form.value.especialidade_id) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios.' });
    return;
  }

  salvando.value = true;
  const payload = {
    med_codigo: form.value.med_codigo || null,
    med_nome: form.value.med_nome,
    med_crm: form.value.med_crm,
    especialidade_id: form.value.especialidade_id,
  };

  try {
    if (form.value.id) {
      await api.put(`/medicos/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Médico atualizado.' });
    } else {
      await api.post('/medicos', payload);
      $q.notify({ type: 'positive', message: 'Médico criado.' });
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
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover este médico?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/medicos/${id}`);
        $q.notify({ type: 'positive', message: 'Médico removido.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
