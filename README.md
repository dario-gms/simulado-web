# 📚 Simulado para Concursos

## ✨ Visão Geral

O **Simulado para Concursos** é uma aplicação web desenvolvida para ajudar candidatos a concursos públicos a praticar e testar seus conhecimentos. O sistema permite:

- Criar e gerenciar áreas de conhecimento
- Adicionar questões com múltiplas opções
- Realizar simulados personalizados
- Visualizar resultados detalhados

## 🛠 Tecnologias Utilizadas

### Backend
- **PHP 8.2+** - Lógica de negócios e API
- **MySQL** - Banco de dados relacional
- **PDO** - Conexão com o banco de dados

### Frontend
- **HTML5** - Estrutura semântica
- **CSS3** - Estilização moderna
- **JavaScript** - Interatividade
- **SVG** - Ícones e elementos gráficos

### Infraestrutura
- **XAMPP/Apache** - Servidor web local
- **phpMyAdmin** - Gerenciamento do banco de dados

## 🚀 Como Executar o Projeto

### Pré-requisitos
- XAMPP ou servidor Apache com PHP 8.2+
- MySQL 5.7+ ou MariaDB 10.3+
- Git (opcional)

### Instalação
1. Clone o repositório:
   ```bash
   git clone https://github.com/dario-gms/simulado-web
   ```
2. Configure o banco de dados:
   - Importe o arquivo `database/migrations.sql` no phpMyAdmin
   - Atualize as credenciais em `src/config/database.php`

3. Coloque o projeto na pasta `htdocs` do XAMPP ou no diretório raiz do seu servidor web

4. Acesse no navegador:
   ```
   http://localhost/simulado/public/
   ```

## 🏗 Estrutura do Projeto

```
simulado/
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js
│   │   ├── simulado.js
│   │   └── add_question.js
│   ├── index.php
│   ├── api.php
│   ├── simulado.php
│   ├── resultado.php
│   ├── manage_areas.php
│   └── add_question.php
├── src/
│   ├── config/
│   │   └── database.php
│   ├── controllers/
│   │   ├── QuestionController.php
│   │   ├── AreaController.php
│   │   └── SimuladoController.php
│   ├── models/
│   │   ├── Question.php
│   │   ├── Option.php
│   │   └── Area.php
│   └── database/
│       └── migrations.sql
├── vendor/
└── .htaccess
```

## 📋 Funcionalidades Principais

### Áreas de Conhecimento
- 📂 Criação e gerenciamento de áreas de estudo
- 🗂 Organização hierárquica
- 🔄 Atualização em tempo real

### Questões
- ✏️ Adição de questões com 5 alternativas
- ✔️ Marcação da resposta correta
- 🏷 Associação a áreas específicas

### Simulados
- 🎯 Seleção por áreas de conhecimento
- ⏱ Cronômetro integrado
- 📊 Progresso em tempo real

### Resultados
- 📈 Pontuação detalhada
- ⏱ Tempo gasto

## 🎨 Design System

### Cores Principais
| Cor               | Hexadecimal |
|-------------------|-------------|
| Azul Primário     | `#4361ee`   |
| Azul Secundário   | `#3f37c9`   |
| Sucesso           | `#4cc9f0`   |
| Perigo            | `#f72585`   |

### Tipografia
- **Família Principal**: Roboto
- **Tamanhos**:
  - Títulos: 2.5rem
  - Texto normal: 1rem
  - Pequeno: 0.875rem

## 🤝 Como Contribuir

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

---

<div align="center">
  <sub>Criado com ❤︎ por <a href="https://github.com/dario-gms">Dário Gomes</a></sub>
</div>

## 🎉 Screenshots

1. **Página Inicial**  
 ![image](https://github.com/user-attachments/assets/c84f8b62-ae26-4782-8e3d-a17bdf418c75)

2. **Adicionar Questão**  
  ![image](https://github.com/user-attachments/assets/df025ee2-0820-40c5-8ece-dc5e2098032f)

3. **Simulado em Andamento**  
   ![image](https://github.com/user-attachments/assets/4d731018-47a1-470b-a16e-b44a14b5b5fe)

## 🔧 Troubleshooting

### Problemas comuns e soluções:

1. **Erro de conexão com o banco de dados**  
   Verifique as credenciais no arquivo `src/config/database.php`

2. **Página não encontrada (404)**  
   Certifique-se de que o mod_rewrite está ativado no Apache

3. **Ícones não aparecendo**  
   Verifique se o caminho para os arquivos SVG está correto
