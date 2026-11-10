<?php
final class SaleRepository {
    public function __construct(private PDO $db) {}
    public function all(string $from='',string $to=''): array {
        $sql="SELECT v.*, c.nome AS cliente FROM vendas v LEFT JOIN clientes c ON c.id=v.cliente_id WHERE (:de='' OR v.criada_em::date >= CAST(:de AS date)) AND (:ate='' OR v.criada_em::date <= CAST(:ate AS date)) ORDER BY v.criada_em DESC";
        $s=$this->db->prepare($sql); $s->execute(['de'=>$from,'ate'=>$to]); return $s->fetchAll();
    }
    public function find(int $id): ?array { $s=$this->db->prepare('SELECT v.*,c.nome AS cliente FROM vendas v LEFT JOIN clientes c ON c.id=v.cliente_id WHERE v.id=:id');$s->execute(['id'=>$id]);$sale=$s->fetch(); if(!$sale)return null; $i=$this->db->prepare('SELECT * FROM itens_venda WHERE venda_id=:id ORDER BY id');$i->execute(['id'=>$id]);$sale['itens']=$i->fetchAll();return $sale; }
    public function create(?int $customerId,array $items,float $total): int {
        $s=$this->db->prepare("INSERT INTO vendas(cliente_id,total,status) VALUES(:cliente,:total,'finalizada') RETURNING id");$s->execute(['cliente'=>$customerId,'total'=>$total]);$id=(int)$s->fetchColumn();
        $i=$this->db->prepare('INSERT INTO itens_venda(venda_id,produto_id,produto_nome,quantidade,preco_unitario,subtotal) VALUES(:v,:p,:n,:q,:preco,:sub)');
        foreach($items as $x)$i->execute(['v'=>$id,'p'=>$x['id'],'n'=>$x['nome'],'q'=>$x['quantidade'],'preco'=>$x['preco'],'sub'=>$x['subtotal']]); return $id;
    }
    public function cancel(int $id): void { $s=$this->db->prepare("UPDATE vendas SET status='cancelada' WHERE id=:id AND status='finalizada'");$s->execute(['id'=>$id]); if(!$s->rowCount()) throw new RuntimeException('Venda não encontrada ou já cancelada.'); }
    public function restoreStock(int $id): void { $s=$this->db->prepare('SELECT produto_id,quantidade FROM itens_venda WHERE venda_id=:id');$s->execute(['id'=>$id]);$u=$this->db->prepare('UPDATE produtos SET quantidade=quantidade+:q WHERE id=:p');foreach($s as $r)$u->execute(['q'=>$r['quantidade'],'p'=>$r['produto_id']]); }
}
