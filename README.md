# Haut Forms
forms.haut.world

---

## Estrutura de Diretórios

/
├── admin/
│   ├── index.php          // Dashboard do Admin
│   ├── usuarios.php       // Gerenciar usuários (Adicionar, Editar, Excluir)
│   ├── departamentos.php  // Gerenciar departamentos (Adicionar, Editar, Excluir)
│   ├── cargos.php         // Gerenciar cargos (Adicionar, Editar, Excluir)
│   └── snippet.php        // Página para gerar o snippet PHP
├── includes/
│   ├── header.php         // Cabeçalho da página (Bulma, meta tags, etc.)
│   ├── footer.php         // Rodapé da página
│   ├── db.php             // Conexão com o banco de dados
│   └── auth.php           // Lógica de autenticação e verificação de permissões
├── member/
│   ├── index.php          // Dashboard do Membro (links para formulários)
│   └── profile.php        // Perfil do usuário (Editar nome, cargo, etc.)
├── forms/
│   └── financeiro.php     // Exemplo de formulário
├── index.php              // Página de Login
├── logout.php             // Deslogar
└── .htaccess              // Arquivo para segurança e redirecionamento de URL