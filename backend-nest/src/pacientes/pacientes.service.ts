import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreatePacienteDto } from './dto/create-paciente.dto';
import { UpdatePacienteDto } from './dto/update-paciente.dto';

@Injectable()
export class PacientesService {
  constructor(private readonly prisma: PrismaService) {}

  async create(data: CreatePacienteDto) {
    const paciente = await this.prisma.paciente.create({
      data: {
        nome: data.nome,
        dataNascimento: new Date(data.data_nascimento),
        telefone: data.telefone,
      },
    });

    return {
      message: 'Paciente criado com sucesso',
      data: this.mapPaciente(paciente),
    };
  }

  async findAll() {
    const pacientes = await this.prisma.paciente.findMany({
      orderBy: { id: 'desc' },
    });

    return {
      message:
        pacientes.length === 0
          ? 'Nenhum paciente cadastrado'
          : 'Pacientes listados com sucesso',
      data: pacientes.map((paciente) => this.mapPaciente(paciente)),
    };
  }

  async findOne(id: number) {
    const paciente = await this.prisma.paciente.findUnique({
      where: { id },
    });

    if (!paciente) {
      throw new NotFoundException('Paciente não encontrado');
    }

    return {
      message: 'Paciente encontrado com sucesso',
      data: this.mapPaciente(paciente),
    };
  }

  async update(id: number, data: UpdatePacienteDto) {
    const paciente = await this.prisma.paciente.findUnique({
      where: { id },
    });

    if (!paciente) {
      throw new NotFoundException('Paciente não encontrado');
    }

    const updated = await this.prisma.paciente.update({
      where: { id },
      data: {
        nome: data.nome,
        dataNascimento: data.data_nascimento
          ? new Date(data.data_nascimento)
          : undefined,
        telefone: data.telefone,
      },
    });

    return {
      message: 'Paciente atualizado com sucesso',
      data: this.mapPaciente(updated),
    };
  }

  async remove(id: number) {
    const paciente = await this.prisma.paciente.findUnique({
      where: { id },
    });

    if (!paciente) {
      throw new NotFoundException('Paciente não encontrado');
    }

    await this.prisma.paciente.delete({
      where: { id },
    });

    return {
      message: 'Paciente removido com sucesso',
    };
  }

  private mapPaciente(paciente: {
    id: number;
    nome: string;
    dataNascimento: Date;
    telefone: string;
    createdAt: Date;
    updatedAt: Date;
  }) {
    return {
      id: paciente.id,
      nome: paciente.nome,
      data_nascimento: paciente.dataNascimento.toISOString().split('T')[0],
      telefone: paciente.telefone,
      createdAt: paciente.createdAt,
      updatedAt: paciente.updatedAt,
    };
  }
}
