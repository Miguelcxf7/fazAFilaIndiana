-- Execute este script no PostgreSQL depois de confirmar a tabela produtos existente.
-- A tabela produtos deve possuir: id, cod, nome, quantidade, preco_unitario.
CREATE TABLE IF NOT EXISTS clientes (
 id BIGSERIAL PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 email VARCHAR(180) UNIQUE,
 telefone VARCHAR(30),
 documento VARCHAR(30) UNIQUE,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS vendas (
 id BIGSERIAL PRIMARY KEY,
 cliente_id BIGINT NULL REFERENCES clientes(id) ON DELETE RESTRICT,
 total NUMERIC(12,2) NOT NULL CHECK(total >= 0),
 status VARCHAR(20) NOT NULL DEFAULT 'finalizada' CHECK(status IN ('finalizada','cancelada')),
 criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS itens_venda (
 id BIGSERIAL PRIMARY KEY,
 venda_id BIGINT NOT NULL REFERENCES vendas(id) ON DELETE CASCADE,
 produto_id BIGINT NOT NULL,
 produto_nome VARCHAR(180) NOT NULL,
 quantidade INTEGER NOT NULL CHECK(quantidade > 0),
 preco_unitario NUMERIC(12,2) NOT NULL CHECK(preco_unitario >= 0),
 subtotal NUMERIC(12,2) NOT NULL CHECK(subtotal >= 0)
);
CREATE INDEX IF NOT EXISTS idx_vendas_criada_em ON vendas(criada_em);
CREATE INDEX IF NOT EXISTS idx_vendas_cliente ON vendas(cliente_id);
CREATE INDEX IF NOT EXISTS idx_itens_venda_venda ON itens_venda(venda_id);
