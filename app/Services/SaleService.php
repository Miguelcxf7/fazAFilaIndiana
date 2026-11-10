<?php
/** Service: aplica as regras da venda e garante atomicidade com transação. */
final class SaleService {
    public function __construct(private PDO $db, private ProductRepository $products, private SaleRepository $sales) {}
    public function checkout(?int $customerId,array $cart): int {
        if (!$cart) throw new RuntimeException('O carrinho está vazio.');
        $this->db->beginTransaction();
        try {
            $items=[]; $total=0.0;
            foreach($cart as $id=>$qty) {
                $qty=(int)$qty; if($qty<1) throw new RuntimeException('Quantidade inválida.');
                $p=$this->products->lockForSale((int)$id); if(!$p) throw new RuntimeException('Produto não encontrado.');
                if((int)$p['quantidade']<$qty) throw new RuntimeException('Estoque insuficiente: '.$p['nome']);
                $price=(float)$p['preco_unitario']; $sub=$price*$qty;
                $this->products->reduceStock((int)$id,$qty);
                $items[]=['id'=>(int)$id,'nome'=>$p['nome'],'quantidade'=>$qty,'preco'=>$price,'subtotal'=>$sub]; $total+=$sub;
            }
            $saleId=$this->sales->create($customerId,$items,$total); $this->db->commit(); return $saleId;
        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
        
            error_log("ERRO ORIGINAL DA VENDA: " . $e->getMessage());
        
            throw $e;
        }
    }
    public function cancel(int $id): void {
        $this->db->beginTransaction(); try { $this->sales->cancel($id); $this->sales->restoreStock($id); $this->db->commit(); } catch(Throwable $e) { if($this->db->inTransaction())$this->db->rollBack(); throw $e; }
    }
}
