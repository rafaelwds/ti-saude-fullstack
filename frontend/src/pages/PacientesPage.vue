<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Pacientes</div>
      <q-btn label="Novo paciente" color="primary" @click="abrirNovo" />
    </div>

    <q-table :rows="pacientes" :columns="columns" row-key="id" :no-data-label="mensagemTabela">
      <template #body-cell-acoes="props">
        <q-td>
          <q-btn flat icon="edit" @click="editarPaciente(props.row)" />
          <q-btn flat icon="delete" color="negative" @click="removerPaciente(props.row.id)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 420px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} paciente</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.nome" label="Nome" outlined />
          <q-input
            v-model="form.data_nascimento"
            label="Data de nascimento"
            outlined
            mask="##/##/####"
          >
            <template #append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                  <q-date
                    v-model="dataCalendario"
                    mask="DD/MM/YYYY"
                    @update:model-value="aplicarDataSelecionada"
                  >
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Fechar" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input v-model="form.telefone" label="Telefone" outlined />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Salvar" @click="salvarPaciente" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from 'src/boot/axios';

interface Paciente {
  id: number | null;
  nome: string;
  data_nascimento: string;
  telefone: string;
}

const pacientes = ref<Paciente[]>([]);
const modalAberto = ref(false);
const mensagemTabela = ref('Nenhum paciente cadastrado');

const dataCalendario = ref('');

const formInicial: Paciente = {
  id: null,
  nome: '',
  data_nascimento: '',
  telefone: '',
};

const form = ref<Paciente>({ ...formInicial });

const aplicarDataSelecionada = (valor: string): void => {
  form.value.data_nascimento = valor;
};

const formatarDataParaApi = (data: string): string => {
  const [dia, mes, ano] = data.split('/');
  return `${ano}-${mes}-${dia}`;
};

const formatarDataParaTela = (data: string): string => {
  const [ano, mes, dia] = data.split('-');
  return `${dia}/${mes}/${ano}`;
};

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'nome', label: 'Nome', field: 'nome', align: 'left' as const },
  {
    name: 'data_nascimento',
    label: 'Nascimento',
    field: 'data_nascimento',
    align: 'left' as const,
    format: (val: string) => formatarDataParaTela(val),
  },
  { name: 'telefone', label: 'Telefone', field: 'telefone', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregarPacientes = async (): Promise<void> => {
  const { data } = await api.get('/pacientes');

  pacientes.value = data.data ?? [];
  mensagemTabela.value = data.message ?? 'Nenhum paciente cadastrado';
};

const abrirNovo = (): void => {
  form.value = { ...formInicial };
  dataCalendario.value = '';
  modalAberto.value = true;
};

const editarPaciente = (paciente: Paciente): void => {
  form.value = {
    ...paciente,
    data_nascimento: formatarDataParaTela(paciente.data_nascimento),
  };
  dataCalendario.value = form.value.data_nascimento;
  modalAberto.value = true;
};

const salvarPaciente = async (): Promise<void> => {
  const payload = {
    nome: form.value.nome,
    data_nascimento: formatarDataParaApi(form.value.data_nascimento),
    telefone: form.value.telefone,
  };

  console.log('payload: ', payload);

  if (form.value.id) {
    await api.put(`/pacientes/${form.value.id}`, payload);
  } else {
    await api.post('/pacientes', payload);
  }

  modalAberto.value = false;
  await carregarPacientes();
};

const removerPaciente = async (id: number | null): Promise<void> => {
  if (!id) return;

  await api.delete(`/pacientes/${id}`);
  await carregarPacientes();
};

onMounted(() => {
  void carregarPacientes();
});
</script>
