<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Vínculos Paciente-Plano</div>
      <q-btn label="Novo vínculo" color="primary" @click="abrirNovo" />
    </div>

    <q-table
      :rows="vinculos"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhum vínculo cadastrado"
      :loading="carregando"
    >
      <template #body-cell-paciente="props">
        <q-td>{{ props.row.paciente?.nome ?? '-' }}</q-td>
      </template>
      <template #body-cell-plano="props">
        <q-td>{{ props.row.plano_saude?.plano_descricao ?? '-' }}</q-td>
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
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} vínculo</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input
            v-model="form.nr_contrato"
            label="Nº Contrato *"
            outlined
            :rules="[val => !!val || 'Nº contrato é obrigatório']"
            lazy-rules
          />
          <q-select
            v-model="form.paciente_id"
            :options="opcoesPaciente"
            label="Paciente *"
            outlined
            emit-value
            map-options
            :rules="[val => !!val || 'Paciente é obrigatório']"
            lazy-rules
          />
          <q-select
            v-model="form.plano_saude_id"
            :options="opcoesPlano"
            label="Plano de Saúde *"
            outlined
            emit-value
            map-options
            :rules="[val => !!val || 'Plano é obrigatório']"
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

interface Vinculo {
  id: number | null;
  nr_contrato: string;
  paciente_id: number | null;
  plano_saude_id: number | null;
  paciente?: { nome: string };
  plano_saude?: { plano_descricao: string };
}

const $q = useQuasar();
const vinculos = ref<Vinculo[]>([]);
const opcoesPaciente = ref<{ label: string; value: number }[]>([]);
const opcoesPlano = ref<{ label: string; value: number }[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);

const formInicial: Vinculo = { id: null, nr_contrato: '', paciente_id: null, plano_saude_id: null };
const form = ref<Vinculo>({ ...formInicial });

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'paciente', label: 'Paciente', field: 'paciente', align: 'left' as const },
  { name: 'plano', label: 'Plano de Saúde', field: 'plano_saude', align: 'left' as const },
  { name: 'nr_contrato', label: 'Nº Contrato', field: 'nr_contrato', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const [resVinculos, resPacientes, resPlanos] = await Promise.all([
      api.get('/vinculos'),
      api.get('/pacientes'),
      api.get('/planos-saude'),
    ]);
    vinculos.value = resVinculos.data.data ?? [];
    opcoesPaciente.value = (resPacientes.data.data ?? []).map(
      (p: { id: number; nome: string }) => ({ label: p.nome, value: p.id })
    );
    opcoesPlano.value = (resPlanos.data.data ?? []).map(
      (pl: { id: number; plano_descricao: string }) => ({ label: pl.plano_descricao, value: pl.id })
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

const editar = (item: Vinculo): void => {
  form.value = {
    id: item.id,
    nr_contrato: item.nr_contrato,
    paciente_id: item.paciente_id,
    plano_saude_id: item.plano_saude_id,
  };
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.nr_contrato || !form.value.paciente_id || !form.value.plano_saude_id) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios.' });
    return;
  }

  salvando.value = true;
  const payload = {
    nr_contrato: form.value.nr_contrato,
    paciente_id: form.value.paciente_id,
    plano_saude_id: form.value.plano_saude_id,
  };

  try {
    if (form.value.id) {
      await api.put(`/vinculos/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Vínculo atualizado.' });
    } else {
      await api.post('/vinculos', payload);
      $q.notify({ type: 'positive', message: 'Vínculo criado.' });
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
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover este vínculo?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/vinculos/${id}`);
        $q.notify({ type: 'positive', message: 'Vínculo removido.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
