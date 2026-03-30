# Ti Saúde Fullstack

Projeto fullstack desenvolvido com:

- Backend em Laravel
- Autenticação com JWT
- Documentação com Swagger
- Frontend web com Quasar
- Banco de dados MySQL
- Ambiente com Docker

---

## Requisitos

Antes de começar, é necessário ter instalado:

- Docker
- Docker Compose
- Node.js 22.22.0 ou superior
- npm

---

## Estrutura do projeto

```bash
ti-saude-fullstack/
├── backend/
├── frontend/
├── docker/
├── docker-compose.yml
└── README.md
```

---

## Como rodar o backend

### 1. Ir para a raiz do projeto

```bash
cd ti-saude-fullstack
```

### 2. Subir os containers

```bash
docker compose up -d --build
```

### 3. Entrar no container do Laravel

```bash
docker compose exec app bash
```

### 4. Instalar dependências do backend

```bash
composer install
```

### 5. Criar o arquivo `.env`

Se ainda não existir:

```bash
cp .env.example .env
```

### 6. Gerar a chave da aplicação

```bash
php artisan key:generate
```

### 7. Gerar a chave JWT

```bash
php artisan jwt:secret
```

### 8. Rodar as migrations

```bash
php artisan migrate
```

### 9. Limpar cache

```bash
php artisan optimize:clear
```

---

## Como rodar o frontend

### 1. Abrir outro terminal

### 2. Ir para a pasta do frontend

```bash
cd frontend
```

### 3. Instalar dependências

```bash
npm install
```

### 4. Rodar o projeto

```bash
npm run dev
```

---

## URLs do projeto

### Backend API

```bash
http://localhost:8000
```

### Swagger

```bash
http://localhost:8000/api/documentation
```

### Frontend Web

```bash
http://localhost:9000
```

### phpMyAdmin

```bash
http://localhost:8080
```

---

## Banco de dados local

- Host: `db`
- Banco: `ti_saude`
- Usuário: `laravel`
- Senha: `root`

---

## Fluxo para testar

### 1. Registrar usuário

Endpoint:

```bash
POST /api/register
```

Exemplo de body:

```json
{
  "name": "Rafael",
  "email": "rafael@email.com",
  "password": "123456"
}
```

### 2. Fazer login

Endpoint:

```bash
POST /api/login
```

Exemplo de body:

```json
{
  "email": "rafael@email.com",
  "password": "123456"
}
```

### 3. Usar o token

O frontend já salva o token automaticamente após o login.

### 4. Testar o CRUD de pacientes

No frontend web é possível:

- listar pacientes
- cadastrar paciente
- editar paciente
- excluir paciente

---

## Funcionalidades implementadas

### Backend

- Cadastro de usuário
- Login com JWT
- Consulta de usuário autenticado
- Logout
- CRUD de pacientes
- Swagger

### Frontend

- Tela de login
- Proteção de rota
- Listagem de pacientes
- Cadastro de pacientes
- Edição de pacientes
- Exclusão de pacientes

---

## Comandos úteis

### Parar os containers

```bash
docker compose down
```

### Ver logs

```bash
docker compose logs -f
```

### Entrar no container do backend

```bash
docker compose exec app bash
```

---

## Observações

- O backend roda com Docker.
- O frontend roda localmente com Quasar.
- Para usar o frontend, o backend precisa estar rodando.
- O projeto foi construído com foco no desafio técnico.

---

## Autor

Rafael Fernando
