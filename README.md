<p align="center">
  <picture>
    <source
      media="(prefers-color-scheme: dark)"
      srcset="public/assets/images/FocoEscritaBranco.png"
    >
    <source
      media="(prefers-color-scheme: light)"
      srcset="public/assets/images/FocoEscritaPreto.png"
    >
    <img
      src="public/assets/images/FocoEscritaPreto.png"
      alt="Foco - Gerenciador de tarefas"
      width="550"
    >
  </picture>
</p>

> 🚧 **Projeto em desenvolvimento**

O **Foco** é uma aplicação web de gerenciamento de tarefas, desenvolvida como projeto acadêmico da disciplina de Desenvolvimento Web.

Seu objetivo é oferecer uma interface simples e intuitiva para organizar tarefas, acompanhar atividades e gerenciar prioridades e prazos.

## Funcionalidades

Funcionalidades previstas para o sistema:

- Cadastro e autenticação de usuários.
- Criação, visualização, edição e exclusão de tarefas.
- Organização de tarefas por prioridade e categoria.
- Acompanhamento do status das tarefas.
- Definição de prazos.

## Tecnologias utilizadas

- **Frontend:** HTML5, CSS3 e JavaScript.
- **Backend:** PHP.
- **Banco de dados:** MySQL/MariaDB.
- **Controle de versão:** Git e GitHub.

## Estrutura do projeto

```text
ProjetoDevWeb/
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── docs/
│   └── database-setup.md
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── images/
│   │   └── js/
│   │       └── app.js
│   ├── pages/
│   │   ├── login.php
│   │   └── register.php
│   └── index.php
├── .env.example
├── .gitignore
├── DESIGN.md
└── README.md
```

## Instalação e configuração

O projeto utiliza PHP com PDO para comunicação com o banco de dados MySQL/MariaDB.

Para configurar o ambiente local, importar as tabelas e testar a conexão, consulte o [guia de configuração do banco de dados](docs/database-setup.md).

## Documentação

- [Configuração do banco de dados](docs/database-setup.md)
- [Identidade visual e diretrizes de design](DESIGN.md)

## Status do projeto

O projeto está em desenvolvimento. A estrutura inicial do banco de dados e a conexão PHP com PDO já foram implementadas e testadas localmente.

As funcionalidades de gerenciamento de tarefas e autenticação ainda estão em desenvolvimento.