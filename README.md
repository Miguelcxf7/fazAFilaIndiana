# Sistema PDV - MVC e Design Patterns

Projeto desenvolvido a partir do PDV fornecido em aula. A versão-base selecionada foi a de **PHP + PostgreSQL + PDO**. Foram organizados os módulos de clientes, carrinho/finalização de vendas e relatórios.

## Requisitos
- PHP 8.1 ou superior;
- PostgreSQL;
- Extensões PHP `pdo` e `pdo_pgsql`;
- Navegador atualizado.

## Arquitetura MVC
- **Model:** `app/Models/` representa as entidades do domínio (Cliente, Produto e Venda).
- **View:** `views/` contém somente a apresentação HTML/PHP.
- **Controller:** `app/Controllers/` recebe as requisições, chama os repositórios/serviços e seleciona as telas.
- **Core:** `app/Core/` contém infraestrutura compartilhada e inicialização.
- **Repository:** `app/Repositories/` concentra consultas e comandos SQL.
- **Service:** `app/Services/` implementa regras de negócio e transações de venda.

Fluxo: Navegador → `public/index.php` (roteador) → Controller → Service/Repository → PostgreSQL → View.

## Design Patterns adotados
1. **Singleton (`app/Core/Database.php`):** oferece uma única instância PDO por requisição, centralizando a conexão e suas configurações.
2. **Repository (`app/Repositories/`):** separa o acesso ao banco das regras de negócio e dos controllers. Exemplos: `CustomerRepository`, `ProductRepository` e `SaleRepository`.
3. **Service Layer (`app/Services/SaleService.php`):** encapsula a regra de finalização/cancelamento da venda. Garante transação: se qualquer etapa falhar, o banco executa rollback.

## Funcionalidades
### Clientes
- Create: cadastrar cliente;
- Read: listar clientes;
- Update: editar cadastro;
- Delete: excluir cliente (o banco impede exclusão quando há venda vinculada).

### Carrinho e vendas
- Buscar produtos;
- Adicionar, alterar quantidade e remover itens;
- Conferir estoque no servidor;
- Finalizar venda com baixa de estoque e gravação dos itens em uma transação.

### Relatórios
- Listar vendas;
- Filtrar por período;
- Consultar detalhes e itens de uma venda;
- Cancelar venda finalizada e restaurar o estoque.

> Observação: relatório é naturalmente uma funcionalidade de consulta (Read). As operações de criação e cancelamento da venda são realizadas no fluxo do PDV; não é recomendado editar manualmente o histórico de uma venda já concluída.

## POP - Procedimento Operacional Padrão de implantação
1. Instale PHP 8.1+ com `pdo_pgsql` habilitado e tenha acesso a um banco PostgreSQL.
2. Copie a pasta do projeto para o diretório do servidor web (por exemplo, `htdocs` do XAMPP).
3. Faça uma cópia de `.env.example` com o nome `.env` na raiz do projeto.
4. Edite `.env`: informe em `SRVDB` a URL PostgreSQL/Neon no formato mostrado no exemplo. Se preferir, também pode informar o DSN PDO e preencher `DB_USER` e `DB_PASS`. **Não publique o `.env` no GitHub.**
5. Confirme que a tabela `produtos` já existe e possui as colunas `id`, `cod`, `nome`, `quantidade` e `preco_unitario`, conforme o PDV original.
6. Execute `database/schema.sql` no banco PostgreSQL para criar `clientes`, `vendas` e `itens_venda`.
7. Habilite no `php.ini` as extensões `extension=pdo_pgsql` e `extension=pgsql` (remova `;` no início, se estiver comentado) e reinicie o Apache.
8. Acesse `http://localhost/PDV_MVC/public/` (ajuste o nome da pasta conforme a instalação).
9. Faça um teste: cadastre um cliente, adicione produto ao carrinho, finalize uma venda e confira o relatório.
10. Para publicar no GitHub, envie o projeto sem `.env`, senhas, logs ou dados reais. Inclua este README e o `.env.example`.

## Segurança e observações
- Consultas utilizam PDO com parâmetros preparados.
- Saídas exibidas nas telas passam por `htmlspecialchars`.
- A baixa de estoque é feita dentro de transação e com bloqueio da linha do produto (`FOR UPDATE`).
- Configure HTTPS e autenticação/autorização antes de usar em produção.
- O schema pressupõe que a tabela `produtos` já existe no banco do projeto original. Revise nomes/tipos caso o banco da aula seja diferente.
- A aplicação não contém credenciais. Use suas próprias variáveis locais no `.env`.

## Publicação no GitHub
Crie um repositório vazio e execute na raiz do projeto:
```bash
git init
git add .
git commit -m "Implementa MVC, patterns e módulos de vendas"
git branch -M main
git remote add origin https://github.com/SEU-USUARIO/SEU-REPOSITORIO.git
git push -u origin main
```
