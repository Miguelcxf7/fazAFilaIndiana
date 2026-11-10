
<?php

final class ProductRepository {

    public function __construct(private PDO $db) {}

    public function search(string $term = ''): array {

        $s = $this->db->prepare(
            "SELECT id, nome, quantidade, preco_unitario
             FROM produtos
             WHERE (:q = '' OR nome ILIKE :like1 OR cod ILIKE :like2)
             ORDER BY nome
             LIMIT 100"
        );

        $s->execute([
            'q' => $term,
            'like1' => '%' . $term . '%',
            'like2' => '%' . $term . '%'
        ]);

        return $s->fetchAll();
    }

    public function lockForSale(int $id): ?array {

        $s = $this->db->prepare(
            'SELECT id, nome, quantidade, preco_unitario
             FROM produtos
             WHERE id = :id
             FOR UPDATE'
        );

        $s->execute(['id' => $id]);

        return $s->fetch() ?: null;
    }

    public function reduceStock(int $id, int $qty): void {

        $s = $this->db->prepare(
            'UPDATE produtos
             SET quantidade = quantidade - :quantidade
             WHERE id = :id
             AND quantidade >= :limite'
        );

        $s->execute([
            'quantidade' => $qty,
            'id' => $id,
            'limite' => $qty
        ]);

        if ($s->rowCount() !== 1) {
            throw new RuntimeException(
                'Estoque insuficiente para um dos produtos.'
            );
        }
    }
}
