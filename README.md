# 🧪 Simulado Web

## ✨ Visão Geral

**Simulado Web** é uma plataforma de simulados flexível e poderosa, criada para auxiliar estudantes e profissionais em diversas áreas a praticar seus conhecimentos por meio de questões objetivas. Ideal para concursos, ENEM, certificações ou treinamentos personalizados.

### Funcionalidades Principais

* Gerenciamento de áreas de conhecimento
* Cadastro de questões com alternativas, imagens e explicações
* Simulados personalizados (normal ou com respostas imediatas)
* Controle de tempo e limite de questões
* Histórico detalhado de desempenho

## 🛠 Tecnologias Utilizadas

### Backend

* **PHP 8.2+** — Lógica de aplicação e API
* **MySQL** — Banco de dados relacional
* **PDO** — Comunicação com o banco de forma segura

### Frontend

* **HTML5** — Estrutura semântica
* **CSS3** — Estilização moderna e responsiva
* **JavaScript** — Interatividade e controle de UI
* **SVG** — Ícones leves e vetoriais

### Infraestrutura

* **XAMPP/Apache** — Servidor local
* **phpMyAdmin** — Administração do banco de dados

## 🚀 Como Executar o Projeto

### Pré-requisitos

* XAMPP ou Apache com PHP 8.2+
* MySQL 5.7+ ou MariaDB 10.3+
* Git (opcional)

### Instalação

1. Clone o repositório:

   ```bash
   git clone https://github.com/dario-gms/simulado-web
   ```

2. Configure o banco de dados:

   * Importe `src/database/migrations.sql` no phpMyAdmin
   * Edite `src/config/database.php` com suas credenciais

3. Mova o projeto para `htdocs` do XAMPP ou a raiz do seu servidor

4. Acesse pelo navegador:

   ```
   http://localhost/simulado/public/
   ```

## 🏗 Estrutura do Projeto

```
simulado/
├── public/
│   ├── admin/
│   │   ├── stats.php           # Histórico detalhado dos usuários
│   │   └── users.php           # Gerenciamento de contas
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── add_question.js     # Scripts para cadastro de questões
│   │   ├── admin.js
│   │   ├── auth.js
│   │   ├── main.js
│   │   ├── profile.js
│   │   └── simulado.js
│   ├── partials/
│   │   └── menu.php            # Menu rápido para ações administrativas
│   ├── add_question.php
│   ├── api.php
│   ├── config.php
│   ├── get_image.php
│   ├── index.php
│   ├── login.php
│   ├── manage_areas.php
│   ├── profile.php             # Perfil e desempenho do usuário
│   ├── register.php
│   ├── resultado.php
│   ├── simulado.php
│   └── simulado_imediato.php  # Simulado com resposta imediata
├── src/
│   ├── config/
│   │   └── database.php
│   ├── controllers/
│   │   ├── AreaController.php
│   │   ├── AuthController.php
│   │   ├── ImageController.php
│   │   ├── QuestionController.php
│   │   ├── SimuladoController.php
│   │   ├── SimuladoResultsController.php
│   │   └── UserStatsController.php
│   ├── database/
│   │   └── migrations.sql
│   ├── models/
│   │   ├── Area.php
│   │   ├── Option.php
│   │   ├── Question.php
│   │   ├── QuestionImage.php
│   │   ├── User.php
│   │   └── UserStats.php
├── vendor/
└── .htaccess
```

## 🧩 Novidades e Recursos Recentes

* 📊 **stats.php**: histórico completo por usuário (área, questões, acertos, percentual, data)
* 👥 **users.php**: gerenciamento completo dos usuários e seus dados
* 🧑 **profile.php**: painel com desempenho individual, últimos simulados, notas e erros
* 📂 **menu.php**: menu contextual rápido para facilitar ações do administrador
* 🖼 **Imagens nas questões**: upload via gerenciador de arquivos ou arraste
* 🧠 **Explicações opcionais**: cada questão pode conter justificativa exibida após resposta
* ⚡ **Simulado Imediato**: usuário recebe feedback imediato da resposta
* 📌 **Simulado Customizado**:

  * Limite de até 100 questões
  * Cronômetro configurável — se o tempo expira, simulado é encerrado automaticamente
  * Questões não respondidas são contabilizadas como erros

## 🎨 Design System

### Paleta de Cores

| Elemento        | Cor       |
| --------------- | --------- |
| Azul Primário   | `#4361ee` |
| Azul Secundário | `#3f37c9` |
| Sucesso         | `#4cc9f0` |
| Erro/Perigo     | `#f72585` |

### Tipografia

* **Fonte**: Roboto
* **Tamanhos**:

  * Títulos: `2.5rem`
  * Texto comum: `1rem`
  * Pequeno: `0.875rem`

## 📈 Funcionalidades

### Áreas de Conhecimento

* Criação, edição e exclusão de áreas
* Associação direta com questões

### Questões

* Múltiplas alternativas (5)
* Imagem opcional (JPG, PNG, GIF, SVG etc.)
* Explicação opcional
* Associação com área

### Simulados

* Escolha de áreas
* Simulado normal ou com respostas imediatas
* Limite de até 100 questões
* Cronômetro (opcional)

### Resultados

* Correção automática
* Percentual de acerto
* Feedback por questão
* Histórico de desempenho

## 🤝 Como Contribuir

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/NomeDaFeature`)
3. Commit suas mudanças (`git commit -m "feat: adiciona nova feature"`)
4. Push para o repositório (`git push origin feature/NomeDaFeature`)
5. Abra um Pull Request

## 📄 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

---

<div align="center">
  <sub>Criado com ❤︎ por <a href="https://github.com/dario-gms">Dário Gomes</a></sub>
</div>

## 📷 Screenshots

1. **Página Inicial**
   ![image](https://github.com/user-attachments/assets/e45cfa79-c889-4aba-88bb-aec9375fd06c)


2. **Adicionar Questão**
   ![image](https://github.com/user-attachments/assets/6fe5266d-f602-4b69-9bbe-be54eb4b4f71)

3. **Simulado em Andamento**
   ![image](https://github.com/user-attachments/assets/ae338613-fdcb-49cb-afbb-50fc11dabcda)


## ❗ Troubleshooting

### Problemas comuns:

* **Erro de conexão com o banco de dados**
  Verifique o arquivo `src/config/database.php`

* **Erro 404**
  Certifique-se de que o mod\_rewrite está ativado no Apache

* **Imagens não carregam**
  Verifique os caminhos em `get_image.php` e permissões de pastas
