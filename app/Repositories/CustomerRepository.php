
<?php
/** Repository: concentra o acesso SQL aos clientes. */
final class CustomerRepository {

    public function __construct(private PDO $db) {}

    public function all(): array {
        return $this->db
            ->query('SELECT * FROM clientes ORDER BY nome')
            ->fetchAll();
    }

    public function find(int $id): ?array {
        $s = $this->db->prepare(
            'SELECT * FROM clientes WHERE id = :id'
        );

        $s->execute(['id' => $id]);

        return $s->fetch() ?: null;
    }

    public function save(array $d, ?int $id = null): void {

        if ($id) {
            $sql = 'UPDATE clientes
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone,
                        cpf = :documento
                    WHERE id = :id';

            $d['id'] = $id;

        } else {
            $sql = 'INSERT INTO clientes
            (nome, email, telefone, cpf)
            VALUES
            (:nome, :email, :telefone, :documento)';
        }

        $s = $this->db->prepare($sql);

        $s->execute([
            'nome' => $d['nome'],
            'email' => $d['email'] ?: null,
            'telefone' => $d['telefone'] ?: null,
            'documento' => $d['documento'] ?: null
        ] + ($id ? ['id' => $id] : []));
    }

    public function delete(int $id): void {
        $s = $this->db->prepare(
            'DELETE FROM clientes WHERE id = :id'
        );

        $s->execute(['id' => $id]);
    }
}
