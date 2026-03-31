import { ApiProperty } from '@nestjs/swagger';
import { IsDateString, IsString, MaxLength } from 'class-validator';

export class CreatePacienteDto {
  @ApiProperty({ example: 'Maria Silva' })
  @IsString()
  @MaxLength(255)
  nome!: string;

  @ApiProperty({ example: '1995-08-10' })
  @IsDateString()
  data_nascimento!: string;

  @ApiProperty({ example: '81999999999' })
  @IsString()
  @MaxLength(20)
  telefone!: string;
}
