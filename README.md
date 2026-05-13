<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About AirportControl

**AirportControl** é uma aplicação web de gerenciamento de operações aeroportuárias desenvolvida com Laravel. O sistema oferece funcionalidades completas para o controle de voos, companhias aéreas, aeronaves, aeroportos e toda a infraestrutura necessária para o gerenciamento eficiente de um aeroporto.

A aplicação é construída com:
- **Framework**: Laravel 11
- **Frontend**: Blade Templates com Bootstrap
- **Banco de Dados**: MySQL
- **Build Tools**: Vite para assets
- **Relatórios**: Suporte a exportação em CSV e PDF

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## 📋 Funcionalidades Principais

### 🔐 Autenticação e Segurança
- Sistema de login e registro de usuários
- Recuperação de senha por email
- Dois tipos de usuários: **Administrador** e **Usuário Comum**
- Controle de acesso baseado em permissões (Gates)
- Validação de formulários com mensagens de erro personalizadas

### 📊 Dashboard Interativo
- Dashboard geral com estatísticas completas
- Visualização de gráficos em tempo real
- Métricas de voos, passageiros e companhias
- Dados segmentados por horário, tipo de voo e aeronave

### ✈️ Gerenciamento de Voos (CRUD)
- Criar, editar, visualizar e deletar voos
- Registros detalhados com companhia aérea, aeronave e aeroporto
- Controle de passageiros e capacidade
- Classificação por horário (EAM, AM, AN, PM, ALL)
- Tipos de voo (Regular, Charter)
- Sistema de notas (Objetivo, Pontualidade, Serviços, Pátio)
- Exportação em CSV e PDF
- Validação de duplicidade de voos

### 🏢 Gerenciamento de Companhias Aéreas
- Cadastro, edição e exclusão de companhias aéreas
- Associação de aeronaves à companhia
- Controle de disponibilidade de aeronaves
- Dashboard específico com estatísticas por companhia
- Listagem de voos por companhia
- Exportação de voos em PDF
- Verificação de código e nome únicos (validação AJAX)

### ✈ Gerenciamento de Aeronaves
- Cadastro de aeronaves com fabricante, modelo e capacidade
- Classificação por porte (Pequeno, Médio, Grande)
- Associação com companhias aéreas
- Dashboard individual com estatísticas de uso
- Sistema de ranking de aeronaves
- Informações gerais com filtros por companhia
- Métricas de performance (voos, passageiros, notas)

### 🛫 Gerenciamento de Aeroportos (Wizard Multi-etapas)
- **Passo 1**: Criação básica do aeroporto com associação de companhias
- **Passo 2**: Cadastro de depósitos e estrutura
- **Passo 3**: Adição de veículos por depósito
- Dashboard específico com estatísticas detalhadas
- Informações gerais com evolução mensal
- Análise de companhias e passageiros por período

### 📦 Gerenciamento de Depósitos e Veículos
- Cadastro de depósitos por aeroporto
- Gerenciamento de veículos por depósito (carrinho de compras)
- Tipos de veículos suportados
- Sistema de finalização em lote
- Validação de códigos únicos

### 📈 Relatórios Avançados
- Sistema de relatórios customizáveis
- Relatório de Companhias por Aeroporto
- Filtros por aeroporto e companhia específica
- Visualização em tabela (Admin) e cards (Usuários)
- API REST para dados de relatórios
- Controle de visibilidade por tipo de usuário

### 📊 Análises e Rankings
- **Ranking de Aeronaves**: Ordenação por nota geral, objetivo, pontualidade, serviços, pátio, voos e passageiros
- **Informações de Companhias**: Estatísticas consolidadas
- **Informações de Aeroportos**: Dados por período
- Destaques e comparações
- Estatísticas de fabricantes e portes de aeronave

### 📁 Exportação de Dados
- Exportação de voos em CSV
- Exportação de voos em PDF
- Exportação de voos por companhia em PDF
- Formatação automática de dados

### 🔧 Validações e Verificações AJAX
- Verificação de código de companhia (tempo real)
- Verificação de nome de companhia (tempo real)
- Verificação de nome de aeroporto (tempo real)
- Verificação de código de veiculo/deposito
- Verificação de modelo de aeronave

### 🎯 Filtros e Buscas Avançadas
- Filtros por período (semanal, mensal, anual)
- Filtros por companhia aérea
- Filtros por ano e mês
- Búsca de companhia por código de voo
- Seleção dinâmica de aeronaves por companhia

### 👥 Controle de Usuários (Admin)
- Listagem de usuários
- Gerenciamento de tipos de usuários
- Definição de permissões

### 🏭 Gerenciamento de Fabricantes (Admin)
- Cadastro de fabricantes de aeronaves
- Associação com modelos de aeronaves

## 🚀 Setup e Instalação para AirportControl

Siga os passos abaixo para configurar e executar a aplicação AirportControl em sua máquina local.

### 📋 Pré-requisitos
- PHP 8.1 ou superior
- Composer (gerenciador de dependências PHP)
- Node.js 16+ e npm (para assets frontend)
- MySQL 5.7 ou superior
- XAMPP (ou similar com Apache e MySQL) para desenvolvimento local
- Git (opcional, para clonar o repositório)

### 📥 Passos de Instalação

#### 1️⃣ Clonar ou Baixar o Projeto
```bash
# Coloque a pasta do projeto em C:\xampp\htdocs\AirportControl
# ou altere o caminho conforme sua instalação do XAMPP
```

#### 2️⃣ Instalar Dependências PHP
Abra o terminal na raiz do projeto e execute:
```bash
composer install
```

#### 3️⃣ Instalar Dependências Node.js
```bash
npm install
```

#### 4️⃣ Configuração do Ambiente
Copie o arquivo `.env.example` para `.env`:
```bash
cp .env.example .env
```

**Edite o arquivo `.env` com suas configurações:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airportcontrol
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://localhost/AirportControl/public
APP_ENV=local
APP_DEBUG=true
```

#### 5️⃣ Gerar Chave da Aplicação
```bash
php artisan key:generate
```

#### 6️⃣ Configuração do Banco de Dados
1. **Inicie o MySQL** via XAMPP Control Panel
2. **Acesse phpMyAdmin** em `http://localhost/phpmyadmin`
3. **Crie um novo banco de dados** chamado `airportcontrol`
4. **Execute as migrações** (volta ao terminal):
```bash
php artisan migrate
```

5. **[Opcional] Popule com dados de exemplo:**
```bash
php artisan db:seed
```

#### 7️⃣ Compilar Assets Frontend

**Para desenvolvimento (com hot reload):**
```bash
npm run dev
```

**Para produção:**
```bash
npm run build
```

#### 8️⃣ Executar a Aplicação

**Opção A: Via XAMPP (Recomendado para desenvolvimento)**
1. Inicie Apache e MySQL no XAMPP Control Panel
2. Acesse `http://localhost/AirportControl/public`

**Opção B: Servidor PHP Integrado**
```bash
php artisan serve
```
Acesse em `http://localhost:8000`

### 🔒 Credenciais Padrão (Após Seed)
- **Email Admin**: admin@example.com
- **Senha**: password
- **Email Usuário**: user@example.com
- **Senha**: password

### 🛠️ Comandos Úteis

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Resetar banco de dados
php artisan migrate:reset
php artisan migrate

# Verificar rotas
php artisan route:list

# Executar testes
php artisan test
```

### ⚠️ Solução de Problemas

#### Erro de Permissão
Se encontrar erros de permissão:
1. Execute XAMPP como Administrador
2. Verifique se as pastas `storage/` e `bootstrap/cache/` têm permissões de escrita

#### Erro de Extensão PHP
Verifique se o arquivo `php.ini` tem as seguintes extensões ativadas:
```
extension=pdo_mysql
extension=openssl
extension=fileinfo
```

#### Porta 8000 em Uso
Se usar `php artisan serve`, mude a porta:
```bash
php artisan serve --port=8001
```

## 🏗️ Arquitetura do Projeto

### Estrutura de Diretórios
```
AirportControl/
├── app/
│   ├── Http/Controllers/        # Controllers da aplicação
│   ├── Models/                   # Modelos Eloquent (Aeronave, Aeroporto, etc)
│   ├── Repositories/             # Padrão Repository (dados e lógica de acesso)
│   ├── Services/                 # Serviços de negócio (RankingService, etc)
│   ├── Providers/                # Service Providers e configurações
│   └── Helpers/                  # Funções auxiliares
├── routes/
│   ├── web.php                   # Rotas web autenticadas
│   └── api.php                   # Rotas API
├── database/
│   ├── migrations/               # Migrações do banco de dados
│   ├── seeders/                  # Seeders para dados iniciais
│   └── factories/                # Factories para testes
├── resources/
│   ├── views/                    # Templates Blade
│   ├── css/                      # Estilos CSS
│   └── js/                       # Scripts JavaScript
├── config/                       # Arquivos de configuração
├── tests/                        # Testes automáticos
└── storage/                      # Logs e arquivos temporários
```

### Padrões de Design Utilizados

1. **MVC (Model-View-Controller)**
   - Controllers gerenciam requisições
   - Models representam dados
   - Views renderizam a apresentação

2. **Repository Pattern**
   - `AeronaveRepository`: Lógica de acesso a dados de aeronaves
   - `AeroportoRepository`: Lógica de acesso a dados de aeroportos
   - Abstração do banco de dados

3. **Service Pattern**
   - `RankingService`: Gera rankings de aeronaves
   - `AeroportoService`: Dados e estatísticas de aeroportos
   - Lógica de negócio complexa

### Modelos Principais

- **User**: Usuários da aplicação (Admin/Usuário Comum)
- **Aeronave**: Modelos de aeronaves
- **CompanhiaAerea**: Companhias aéreas
- **Aeroporto**: Aeroportos
- **Voo**: Registros de voos
- **Deposito**: Depósitos de armazenagem
- **Veiculo**: Veículos por depósito
- **Fabricante**: Fabricantes de aeronaves
- **Relatorio**: Configuração de relatórios

### Relacionamentos Principais

- Companhia Aérea ↔ Aeronaves (Many-to-Many)
- Aeroporto ↔ Companhias Aéreas (Many-to-Many)
- Aeroporto → Depósitos (One-to-Many)
- Depósito → Veículos (One-to-Many)
- Voo → Aeronave (Many-to-One)
- Voo → CompanhiaAerea (Many-to-One)
- Voo → Aeroporto (Many-to-One)
- Aeronave → Fabricante (Many-to-One)

## 🔌 Endpoints da API

### Endpoints Públicos (Sem Autenticação)
- `POST /login` - Login de usuários
- `GET /register` - Formulário de registro
- `POST /register` - Registro de usuários
- `GET /esqueci-senha` - Recuperação de senha
- `POST /esqueci-senha` - Enviar link de reset
- `GET /resetar-senha/{token}` - Formulário de reset
- `POST /resetar-senha` - Atualizar senha

### Endpoints Autenticados

#### Dashboard e Relatórios
- `GET /home` - Página inicial
- `GET /dashboard` - Dashboard geral
- `GET /dashboard/graficos` - Gráficos
- `GET /relatorios` - Listagem de relatórios
- `GET /relatorios/companhias-por-aeroporto` - Relatório customizado

#### Informações (Usuários Comuns)
- `GET /aeronaves/informacoes` - Informações de aeronaves
- `GET /aeronaves/ranking` - Ranking de aeronaves
- `GET /aeronaves/{id}/dashboard` - Dashboard de aeronave
- `GET /companhias/informacoes` - Informações de companhias
- `GET /companhias/{id}/dashboard` - Dashboard de companhia
- `GET /aeroportos/informacoes` - Informações de aeroportos
- `GET /aeroportos/{id}/dashboard` - Dashboard de aeroporto

#### CRUD de Voos
- `GET /voos` - Listar voos
- `GET /voos/create` - Formulário criar voo
- `POST /voos` - Criar voo
- `GET /voos/{id}` - Ver voo
- `GET /voos/{id}/edit` - Editar voo
- `PUT /voos/{id}` - Atualizar voo
- `DELETE /voos/{id}` - Deletar voo
- `GET /voos/export/csv` - Exportar CSV
- `GET /voos/export/pdf` - Exportar PDF

#### CRUD de Companhias Aéreas
- `GET /companhias` - Listar companhias
- `POST /companhias` - Criar companhia
- `GET /companhias/{id}` - Ver companhia
- `PUT /companhias/{id}` - Atualizar companhia
- `DELETE /companhias/{id}` - Deletar companhia
- `GET /companhias/{id}/voos-pdf` - Voos em PDF

#### CRUD de Aeronaves
- `GET /aeronaves` - Listar aeronaves
- `POST /aeronaves` - Criar aeronave
- `GET /aeronaves/{id}` - Ver aeronave
- `PUT /aeronaves/{id}` - Atualizar aeronave
- `DELETE /aeronaves/{id}` - Deletar aeronave

#### CRUD de Aeroportos
- `GET /aeroportos` - Listar aeroportos
- `GET /aeroportos/create-step1` - Wizard Passo 1
- `POST /aeroportos/store-step1` - Salvar Passo 1
- `GET /aeroportos/create-step2/{id}` - Wizard Passo 2
- `POST /aeroportos/store-step2/{id}` - Salvar Passo 2
- `GET /aeroportos/create-step3/{id}` - Wizard Passo 3
- `POST /aeroportos/store-step3/{id}` - Salvar Passo 3
- `GET /aeroportos/{id}` - Ver aeroporto
- `PUT /aeroportos/{id}` - Atualizar aeroporto
- `DELETE /aeroportos/{id}` - Deletar aeroporto

#### Gerenciamento de Depósitos e Veículos
- `GET /aeroportos/{id}/depositos` - Listar depósitos
- `POST /aeroportos/{id}/depositos` - Criar depósito
- `GET /aeroportos/{id}/depositos/{dep}/veiculos` - Listar veículos
- `POST /aeroportos/{id}/depositos/{dep}/veiculos` - Criar veículo
- `POST /aeroportos/{id}/depositos/{dep}/veiculos/finalizar` - Finalizar veículos

#### Endpoints AJAX (Validação)
- `POST /companhias/check-code` - Verificar código companhia
- `POST /companhias/check-name` - Verificar nome companhia
- `POST /aeroportos/check-name` - Verificar nome aeroporto
- `POST /verificar-id-voo` - Verificar ID voo
- `GET /api/companhias/{id}/aeronaves` - Aeronaves por companhia

#### Endpoints Admin
- `GET /admin/relatorios` - Gerenciar relatórios
- `GET /admin/users` - Gerenciar usuários
- `GET /fabricantes` - Gerenciar fabricantes

## 🔐 Sistema de Autenticação

A aplicação usa o sistema de autenticação padrão do Laravel com dois tipos de usuários:

### Tipos de Usuário
- **Administrador (tipo = 0)**
  - Acesso completo a CRUD de todas as entidades
  - Acesso ao painel de controle
  - Gerenciamento de usuários
  - Configuração de relatórios

- **Usuário Comum (tipo = 1)**
  - Visualização de informações (aeronaves, companhias, aeroportos)
  - Acesso a relatórios visíveis
  - Dashboard geral
  - Sem acesso a criação/edição/deleção

### Middleware
- `auth` - Verificar autenticação
- `admin` - Verificar se é administrador
- `verified` - Verificar email verificado

## 📦 Dependências Principais

```json
{
  "laravel/framework": "^11.0",
  "barryvdh/laravel-dompdf": "^2.0",
  "laravel/tinker": "^2.8"
}
```

## 🧪 Testes

Para executar os testes automáticos:
```bash
php artisan test
```

## 📄 Licença

O framework Laravel é software de código aberto licenciado sob a [licença MIT](https://opensource.org/licenses/MIT).

## 💬 Suporte e Documentação

Para mais informações sobre Laravel:
- [Documentação oficial do Laravel](https://laravel.com/docs)
- [Guia de Contribuição](https://laravel.com/docs/contributions)
- [Código de Conduta](https://laravel.com/docs/contributions#code-of-conduct)

## 🐛 Reportar Problemas

Se encontrar problemas ou bugs:
1. Verifique se o problema já foi reportado
2. Forneça detalhes da versão do PHP e Laravel
3. Inclua passos para reproduzir o problema
4. Attach screenshots ou logs se relevante

## 📈 Roadmap

- [ ] Integração com APIs de terceiros
- [ ] Notificações em tempo real
- [ ] Modo escuro (Dark Mode)
- [ ] Aplicação mobile
- [ ] Analytics avançados
- [ ] Integração com sistemas de pagamento

## 🙏 Agradecimentos

- Desenvolvido com Laravel Framework
- UI baseada em Bootstrap 5
- Gráficos com Chart.js
- Relatórios com DomPDF
