# Ti Saúde Fullstack

Projeto fullstack desenvolvido como desafio técnico, contendo:

- **Backend principal:** Laravel
- **Autenticação:** JWT
- **Documentação da API:** Swagger
- **Frontend web:** Quasar
- **Banco de dados:** MySQL
- **Ambiente local:** Docker

---

## Requisitos

Antes de começar, é necessário ter instalado:

- Docker
- Docker Compose
- **Node.js 22.22.0 ou superior**
  > Importante: o frontend em Quasar foi configurado e testado com **Node 22.22.0**.
- npm

---

## Estrutura do projeto

```bash
ti-saude-fullstack/
├── backend/         # Backend principal em Laravel
├── backend-nest/    # Backend adicional em NestJS
├── frontend/
├── docker/
├── docker-compose.yml
└── README.md
```

---

## Visão geral da entrega

### Entrega principal

A entrega oficial do desafio está implementada com:

- **Backend:** Laravel
- **Frontend:** Quasar
- **Banco principal:** `ti_saude`

### Complemento técnico

Além da entrega principal, também foi desenvolvido um **backend adicional em NestJS**, com banco separado, como demonstração extra de conhecimento técnico.

- **Backend adicional:** NestJS
- **Banco adicional:** `ti_saude_nest`

> Para avaliação principal, considerar o fluxo **Laravel + Quasar**.  
> Caso o avaliador deseje, também é possível testar a implementação complementar em **NestJS**.

---

## Como rodar o projeto principal

## 1. Clonar o repositório

```bash
git clone <URL_DO_REPOSITORIO>
cd ti-saude-fullstack
```

---

## 2. Subir o backend Laravel

### Subir os containers

```bash
docker compose up -d --build
```

### Verificar se os containers estão rodando

```bash
docker compose ps
```

### Entrar no container do Laravel

```bash
docker compose exec app bash
```

### Instalar dependências do backend

```bash
composer install
```

### Criar o arquivo `.env`

```bash
cp .env.example .env
```

### Gerar a chave da aplicação

```bash
php artisan key:generate
```

### Gerar a chave JWT

```bash
php artisan jwt:secret
```

### Rodar migrations e seeders

```bash
php artisan migrate --seed
```

### Limpar cache da aplicação

```bash
php artisan optimize:clear
```

### Gerar documentação Swagger

```bash
php artisan l5-swagger:generate
```

---

## Usuário padrão para teste

Após executar `php artisan migrate --seed`, o sistema criará automaticamente um usuário de teste:

- **E-mail:** `rafael@email.com`
- **Senha:** `123456`

Esse usuário pode ser utilizado diretamente no frontend ou nos testes da API.

---

## 3. Rodar o frontend Quasar

> Importante: o frontend foi validado com **Node.js 22.22.0**.

Abra outro terminal e rode:

```bash
cd frontend
npm install
npm run dev
```

---

## URLs do projeto principal

### Backend API

```text
http://localhost:8000
```

### Swagger

```text
http://localhost:8000/api/documentation
```

### Frontend Web

```text
http://localhost:9000
```

### phpMyAdmin

```text
http://localhost:8080
```

---

## Banco de dados local

### Banco principal Laravel

- **Host:** `db`
- **Banco:** `ti_saude`
- **Usuário:** `laravel`
- **Senha:** `root`

### Acesso pelo phpMyAdmin

Você pode acessar com:

#### Opção 1

- Usuário: `root`
- Senha: `root`

#### Opção 2

- Usuário: `laravel`
- Senha: `root`

---

## Fluxo sugerido para avaliação principal

### 1. Testar via Swagger ou Postman

#### Registrar usuário

**POST**

```text
POST /api/register
```

Body de exemplo:

```json
{
  "name": "Rafael",
  "email": "rafael@email.com",
  "password": "123456"
}
```

#### Fazer login

**POST**

```text
POST /api/login
```

Body de exemplo:

```json
{
  "email": "rafael@email.com",
  "password": "123456"
}
```

#### Consultar usuário autenticado

**GET**

```text
GET /api/me
```

> Utilizar o token JWT retornado no login.

#### Testar CRUD de pacientes

Endpoints disponíveis:

- `GET /api/pacientes`
- `POST /api/pacientes`
- `PUT /api/pacientes/{id}`
- `DELETE /api/pacientes/{id}`

---

### 2. Testar via frontend

No frontend web é possível:

- realizar login
- listar pacientes
- cadastrar paciente
- editar paciente
- excluir paciente

---

## Funcionalidades implementadas

### Backend principal (Laravel)

- Cadastro de usuário
- Login com JWT
- Consulta de usuário autenticado
- Logout
- CRUD de pacientes
- Documentação Swagger

### Frontend

- Tela de login
- Proteção de rota
- Listagem de pacientes
- Cadastro de pacientes
- Edição de pacientes
- Exclusão de pacientes

---

## Complemento técnico: backend adicional em NestJS

Caso o avaliador queira analisar também a implementação em **NestJS**, ela está disponível na pasta:

```bash
backend-nest/
```

Essa implementação foi desenvolvida como **complemento técnico**, sem substituir a entrega principal em Laravel.

### Banco utilizado pelo NestJS

- **Banco:** `ti_saude_nest`

### Como rodar o backend NestJS

- **Node.js 22.22.0 ou superior**
  > Importante: o frontend em Quasar foi configurado e testado com **Node 22.22.0**.
  > O backend adicional em **NestJS** requer **Node 20 ou superior**, portanto o Node 22.22.0 atende ambos.

Abra outro terminal e rode:

```bash
cd backend-nest
npm install
npm run start:dev
```

### URL da API NestJS

```text
http://localhost:3000
```

### Swagger do NestJS

```text
http://localhost:3000/api/documentation
```

### Endpoints principais do NestJS

#### Autenticação

- `POST /auth/register`
- `POST /auth/login`
- `GET /auth/me`

#### Pacientes

- `GET /pacientes`
- `POST /pacientes`
- `GET /pacientes/{id}`
- `PUT /pacientes/{id}`
- `DELETE /pacientes/{id}`

> O backend NestJS utiliza banco separado (`ti_saude_nest`) para não interferir na entrega principal em Laravel.

---

## Comandos úteis

### Parar os containers do projeto principal

```bash
docker compose down
```

### Ver logs dos containers

```bash
docker compose logs -f
```

### Entrar no container do backend Laravel

```bash
docker compose exec app bash
```

---

## Observações importantes

- O backend principal roda com Docker.
- O frontend roda localmente com Quasar.
- Para usar o frontend, o backend Laravel precisa estar rodando.
- O frontend foi configurado para uso com **Node.js 22.22.0 ou superior**.
- O backend principal para avaliação é o **Laravel**.
- O **NestJS** está disponível como implementação complementar, caso o avaliador queira analisar outra stack.

---

## Autor

Rafael Fernando
