# 📚 Simulado para Concursos - README

![Banner](https://via.placeholder.com/1200x400/4361ee/ffffff?text=Simulado+para+Concursos) *(Adicione um banner real posteriormente)*

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
   git clone https://github.com/seu-usuario/simulado-concursos.git
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
├── public/               # Arquivos acessíveis publicamente
│   ├── css/              # Folhas de estilo
│   ├── js/               # Scripts JavaScript
│   ├── api.php           # Endpoint da API
│   └── index.php         # Página inicial
├── src/
│   ├── config/           # Configurações
│   ├── controllers/      # Controladores
│   ├── models/           # Modelos de dados
│   └── database/         # Migrações do banco
├── vendor/               # Dependências (se houver)
└── .htaccess             # Configurações do Apache
```

## 📋 Funcionalidades Principais

### Áreas de Conhecimento
- 📂 Criação e gerenciamento de áreas
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
- ⏱ Tempo gasto por questão
- 📋 Relatório de desempenho

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

## ✉️ Contato

Seu Nome - [@seu_twitter](https://twitter.com/seu_twitter) - seuemail@exemplo.com

Link do Projeto: [https://github.com/seu-usuario/simulado-concursos](https://github.com/seu-usuario/simulado-concursos)

---

<div align="center">
  <sub>Criado com ❤︎ por <a href="https://github.com/seu-usuario">seu nome</a></sub>
</div>

## 🎉 Screenshots

*(Adicione screenshots reais do seu projeto aqui)*

1. **Página Inicial**  
   ![Página Inicial](https://via.placeholder.com/600x400?text=P%C3%A1gina+Inicial)

2. **Adicionar Questão**  
   ![Adicionar Questão](https://via.placeholder.com/600x400?text=Adicionar+Quest%C3%A3o)

3. **Simulado em Andamento**  
   ![Simulado](https://via.placeholder.com/600x400?text=Simulado+em+Andamento)

## 🔧 Troubleshooting

### Problemas comuns e soluções:

1. **Erro de conexão com o banco de dados**  
   Verifique as credenciais no arquivo `src/config/database.php`

2. **Página não encontrada (404)**  
   Certifique-se de que o mod_rewrite está ativado no Apache

3. **Ícones não aparecendo**  
   Verifique se o caminho para os arquivos SVG está correto

---

Este README foi cuidadosamente elaborado para proporcionar uma visão completa do projeto. Atualize os links, imagens e informações de contato conforme necessário para refletir seu projeto real.