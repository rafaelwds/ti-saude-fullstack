<template>
  <q-page padding>
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h5">Consultas</div>
      <q-btn label="Nova consulta" color="primary" @click="abrirNova" />
    </div>

    <q-table
      :rows="consultas"
      :columns="columns"
      row-key="id"
      no-data-label="Nenhuma consulta cadastrada"
      :loading="carregando"
    >
      <template #body-cell-tipo="props">
        <q-td>
          <q-badge :color="props.row.particular ? 'orange' : 'blue'">
            {{ props.row.particular ? 'Particular' : 'Plano' }}
          </q-badge>
        </q-td>
      </template>
      <template #body-cell-acoes="props">
        <q-td>
          <q-btn flat icon="edit" @click="editar(props.row)" />
          <q-btn flat icon="delete" color="negative" @click="confirmarRemocao(props.row.id)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="modalAberto">
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ form.id ? 'Editar' : 'Nova' }} consulta</div>
        </q-card-section>

        <q-card-section class="q-gutter-md">
          <q-input v-model="form.cons_codigo" label="Código" outlined dense />

          <div class="row q-gutter-md">
            <q-input
              v-model="form.data"
              label="Data *"
              outlined
              dense
              mask="##/##/####"
              class="col"
              :rules="[val => !!val || 'Data é obrigatória']"
              lazy-rules
            >
              <template #append>
                <q-icon name="event" class="cursor-pointer">
                  <q-popup-proxy cover>
                    <q-date v-model="dataCalendario" mask="DD/MM/YYYY" @update:model-value="form.data = $event">
                      <div class="row items-center justify-end">
                        <q-btn v-close-popup label="Fechar" color="primary" flat />
                      </div>
                    </q-date>
                  </q-popup-proxy>
                </q-icon>
              </template>
            </q-input>

            <q-input
              v-model="form.hora"
              label="Hora *"
              outlined
              dense
              mask="##:##"
              placeholder="HH:MM"
              class="col"
              :rules="[val => !!val || 'Hora é obrigatória']"
              lazy-rules
            />
          </div>

          <q-select
            v-model="form.paciente_id"
            :options="opcoesPaciente"
            label="Paciente *"
            outlined
            dense
            emit-value
            map-options
            :rules="[val => !!val || 'Paciente é obrigatório']"
            lazy-rules
          />

          <q-select
            v-model="form.medico_id"
            :options="opcoesMedico"
            label="Médico *"
            outlined
            dense
            emit-value
            map-options
            :rules="[val => !!val || 'Médico é obrigatório']"
            lazy-rules
          />

          <q-toggle v-model="form.particular" label="Consulta particular" @update:model-value="onParticular" />

          <q-select
            v-if="!form.particular"
            v-model="form.plano_saude_id"
            :options="opcoesPlano"
            label="Plano de Saúde"
            outlined
            dense
            emit-value
            map-options
            clearable
          />

          <q-select
            v-model="form.procedimentos"
            :options="opcoesProcedimento"
            label="Procedimentos"
            outlined
            dense
            multiple
            emit-value
            map-options
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

interface Consulta {
  id: number | null;
  cons_codigo: string;
  data: string;
  hora: string;
  particular: boolean;
  paciente_id: number | null;
  medico_id: number | null;
  plano_saude_id: number | null;
  procedimentos: number[];
  paciente?: { nome: string };
  medico?: { med_nome: string };
  plano_saude?: { plano_descricao: string };
}

const $q = useQuasar();
const consultas = ref<Consulta[]>([]);
const opcoesPaciente = ref<{ label: string; value: number }[]>([]);
const opcoesMedico = ref<{ label: string; value: number }[]>([]);
const opcoesPlano = ref<{ label: string; value: number }[]>([]);
const opcoesProcedimento = ref<{ label: string; value: number }[]>([]);
const modalAberto = ref(false);
const carregando = ref(false);
const salvando = ref(false);
const dataCalendario = ref('');

const formInicial: Consulta = {
  id: null, cons_codigo: '', data: '', hora: '', particular: false,
  paciente_id: null, medico_id: null, plano_saude_id: null, procedimentos: [],
};
const form = ref<Consulta>({ ...formInicial });

const onParticular = (val: boolean): void => {
  if (val) form.value.plano_saude_id = null;
};

const formatarData = (val: string) => {
  if (!val) return '';
  const [ano, mes, dia] = val.split('-');
  return `${dia}/${mes}/${ano}`;
};

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left' as const },
  { name: 'data', label: 'Data', field: 'data', align: 'left' as const, format: formatarData },
  { name: 'hora', label: 'Hora', field: 'hora', align: 'left' as const },
  { name: 'paciente', label: 'Paciente', field: (r: Consulta) => r.paciente?.nome ?? '-', align: 'left' as const },
  { name: 'medico', label: 'Médico', field: (r: Consulta) => r.medico?.med_nome ?? '-', align: 'left' as const },
  { name: 'plano', label: 'Plano', field: (r: Consulta) => r.plano_saude?.plano_descricao ?? '-', align: 'left' as const },
  { name: 'tipo', label: 'Tipo', field: 'particular', align: 'left' as const },
  { name: 'acoes', label: 'Ações', field: 'acoes', align: 'left' as const },
];

const carregar = async (): Promise<void> => {
  carregando.value = true;
  try {
    const [resC, resP, resM, resPl, resPr] = await Promise.all([
      api.get('/consultas'),
      api.get('/pacientes'),
      api.get('/medicos'),
      api.get('/planos-saude'),
      api.get('/procedimentos'),
    ]);
    consultas.value = resC.data.data ?? [];
    opcoesPaciente.value = (resP.data.data ?? []).map(
      (p: { id: number; nome: string }) => ({ label: p.nome, value: p.id })
    );
    opcoesMedico.value = (resM.data.data ?? []).map(
      (m: { id: number; med_nome: string }) => ({ label: m.med_nome, value: m.id })
    );
    opcoesPlano.value = (resPl.data.data ?? []).map(
      (pl: { id: number; plano_descricao: string }) => ({ label: pl.plano_descricao, value: pl.id })
    );
    opcoesProcedimento.value = (resPr.data.data ?? []).map(
      (p: { id: number; proc_nome: string; proc_valor: string }) => ({
        label: `${p.proc_nome} (R$ ${Number(p.proc_valor).toFixed(2)})`,
        value: p.id,
      })
    );
  } catch {
    $q.notify({ type: 'negative', message: 'Erro ao carregar dados.' });
  } finally {
    carregando.value = false;
  }
};

const abrirNova = (): void => {
  form.value = { ...formInicial, procedimentos: [] };
  dataCalendario.value = '';
  modalAberto.value = true;
};

const editar = (item: Consulta): void => {
  const [ano, mes, dia] = item.data.split('-');
  form.value = {
    ...item,
    data: `${dia}/${mes}/${ano}`,
    procedimentos: item.procedimentos?.map((p: { id?: number } | number) =>
      typeof p === 'object' ? (p.id ?? 0) : p
    ) ?? [],
  };
  dataCalendario.value = form.value.data;
  modalAberto.value = true;
};

const salvar = async (): Promise<void> => {
  if (!form.value.data || !form.value.hora || !form.value.paciente_id || !form.value.medico_id) {
    $q.notify({ type: 'warning', message: 'Preencha todos os campos obrigatórios.' });
    return;
  }

  salvando.value = true;
  const [dia, mes, ano] = form.value.data.split('/');
  const payload = {
    cons_codigo:    form.value.cons_codigo || null,
    data:           `${ano}-${mes}-${dia}`,
    hora:           form.value.hora,
    particular:     form.value.particular,
    paciente_id:    form.value.paciente_id,
    medico_id:      form.value.medico_id,
    plano_saude_id: form.value.particular ? null : (form.value.plano_saude_id ?? null),
    procedimentos:  form.value.procedimentos,
  };

  try {
    if (form.value.id) {
      await api.put(`/consultas/${form.value.id}`, payload);
      $q.notify({ type: 'positive', message: 'Consulta atualizada.' });
    } else {
      await api.post('/consultas', payload);
      $q.notify({ type: 'positive', message: 'Consulta criada.' });
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
  $q.dialog({ title: 'Confirmar exclusão', message: 'Remover esta consulta?', cancel: true, persistent: true })
    .onOk(async () => {
      try {
        await api.delete(`/consultas/${id}`);
        $q.notify({ type: 'positive', message: 'Consulta removida.' });
        await carregar();
      } catch (error: unknown) {
        const msg = (error as { response?: { data?: { message?: string } } })?.response?.data?.message;
        $q.notify({ type: 'negative', message: msg || 'Erro ao remover.' });
      }
    });
};

onMounted(() => { void carregar(); });
</script>
