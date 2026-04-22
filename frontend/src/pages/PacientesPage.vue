<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Pacientes</div>
      <q-btn label="Novo paciente" color="primary" @click="abrirNovo" />
    </div>

    <q-table
      :rows="pacientes"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhum paciente cadastrado"
      :loading="carregandoTabela"
    >
      <template #body-cell-telefones="props">
        <q-td>{{ telefonesFormatados(props.row) }}</q-td>
      </template>
      <template #body-cell-acoes="props">
        <q-td>
          <q-btn flat icon="edit" @click="editarPaciente(props.row)" />
          <q-btn flat icon="delete" color="negative" @click="confirmarRemocao(props.row.id)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 460px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Novo' }} paciente</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input
            v-model="form.nome"
            label="Nome *"
            outlined
            :rules="[val => !!val || 'Nome é obrigatório']"
            lazy-rules
          />

          <q-input
            v-model="form.data_nascimento"
            label="Data de nascimento *"
            outlined
            mask="##/##/####"
            :rules="[
              val => !!val || 'Data é obrigatória',
              val => val.length === 10 || 'Informe a data completa',
            ]"
            lazy-rules
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

          <!-- Telefones (1,n) -->
          <div>
            <div class="text-subtitle2 q-mb-xs">Telefones * (mínimo 1)</div>
            <div
              v-for="(tel, idx) in form.telefones"
              :key="idx"
              class="row items-center q-gutter-sm q-mb-xs"
            >
              <q-input
                v-model="form.telefones[idx]"
                :label="`Telefone ${idx + 1}`"
                outlined
                dense
                class="col"
                mask="(##) #####-####"
              />
              <q-btn
                flat
                round
                icon="remove_circle"
                color="negative"
                :disable="form.telefones.length === 1"
                @click="removerTelefone(idx)"
              />
            </div>
            <q-btn
              flat
              icon="add"
              color="primary"
              label="Adicionar telefone"
              dense
              @click="adicionarTelefone"
            />
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Salvar" :loading="salvando" @click="salvarPaciente" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from 'src/boot/axios';

interface PacienteTelefone {
  id: number;
  telefone: string;
}

interface Paciente {
  id: number | null;
  nome: string;
  data_nascimento: string;
  telefones: PacienteTelefone[];
}

interface FormPaciente {
  id: number | null;
  nome: string;
  data_nascimento: string;
  telefones: string[];
}

const $q = useQuasar();
const pacientes = ref<Paciente[]>([]);
const modalAberto = ref(false);
const carregandoTabela = ref(false);
const salvando = ref(false);
const dataCalendario = ref('');

const formInicial: FormPaciente = {
  id: null,
  nome: '',
  data_nascimento: '',
  telefones: [''],
};

const form = ref<FormPaciente>({ ...formInicial, telefones: [''] });

const telefonesFormatados = (paciente: Paciente): string => {
  if (!paciente.telefones?.length) return '-';
  return paciente.telefones.map(t => t.telefone).join(' | ');
};

const aplicarDataSelecionada = (valor: string): void => {
  form.value.data_nascimento = valor;
};

const formatarDataParaApi = (data: string): string => {
  const [dia, mes, ano] = data.split('/');
  return `${ano}-${mes}-${dia}`;
};

const formatarDataParaTela = (data: string): string => {
  if (!data) return '';
  const [ano, mes, dia] = data.split('-');
  return `${dia}/${mes}/${ano}`;
};

const adicionarTelefone = (): void => {
  form.value.telefones.push('');
};

const removerTelefone = (idx: number): void => {
  form.value.telefones.splice(idx, 1);
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
  { name: 'telefones', label: 'Telefones', field: 'telefones', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregarPacientes = async (): Promise<void> => {
  carregandoTabela.value = true;
  try {
    const { data } = await api.get('/pacientes');
    pacientes.value = data.data ?? [];
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar pacientes.' });
  } finally {
    carregandoTabela.value = false;
  }
};

const abrirNovo = (): void => {
  form.value = { ...formInicial, telefones: [''] };
  dataCalendario.value = '';
  modalAberto.value = true;
};

const editarPaciente = (paciente: Paciente): void => {
  form.value = {
    id: paciente.id,
    nome: paciente.nome,
    data_nascimento: formatarDataParaTela(paciente.data_nascimento),
    telefones: paciente.telefones?.length
      ? paciente.telefones.map(t => t.telefone)
      : [''],
  };
  dataCalendario.value = form.value.data_nascimento;
  modalAberto.value = true;
};

const salvarPaciente = async (): Promise<void> => {
  const telefonesValidos = form.value.telefones.filter(t => t.trim() !== '');

  if (!form.value.nome || !form.value.data_nascimento || telefonesValidos.length === 0) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos e ao menos um telefone.' });
    return;
  }

  salvando.value = true;

  const payload = {
    nome: form.value.nome,
    data_nascimento: formatarDataParaApi(form.value.data_nascimento),
    telefones: telefonesValidos,
  };

  try {
    if (form.value.id) {
      await api.put(`/pacientes/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Paciente atualizado com sucesso.' });
    } else {
      await api.post('/pacientes', payload);
      $q.notify({ type: 'positive', message: 'Paciente criado com sucesso.' });
    }
    modalAberto.value = false;
    await carregarPacientes();
  } catch (error: unknown) {
    const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
    $q.notify({ type: 'negative', message: msg || 'Erro ao salvar paciente.' });
  } finally {
    salvando.value = false;
  }
};

const confirmarRemocao = (id: number | null): void => {
  if (!id) return;
  $q.dialog({
    title: 'Confirmar exclusão',
    message: 'Deseja remover este paciente?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await api.delete(`/pacientes/${id}`);
      $q.notify({ type: 'positive', message: 'Paciente removido com sucesso.' });
      await carregarPacientes();
    } catch {
      $q.notify({ type: 'negative', message: 'Erro ao remover paciente.' });
    }
  });
};

onMounted(() => { void carregarPacientes(); });
</script>
