<?php
final class CartController extends Controller {
    private PDO $db; private ProductRepository $products; private CustomerRepository $customers;
    public function __construct(){ $this->db=Database::connection();$this->products=new ProductRepository($this->db);$this->customers=new CustomerRepository($this->db); }
    public function index(): void { $term=trim($_GET['q']??'');$products=$this->products->search($term);$cart=$_SESSION['cart']??[];$rows=[];$total=0;
        foreach($cart as $id=>$qty){foreach($this->products->search('') as $p){if((int)$p['id']===(int)$id){$sub=(float)$p['preco_unitario']*(int)$qty;$rows[]=$p+['cart_qty'=>$qty,'subtotal'=>$sub];$total+=$sub;break;}}}
        $this->render('cart/index',['products'=>$products,'cartRows'=>$rows,'total'=>$total,'customers'=>$this->customers->all()]); }
    public function add(): void { $id=(int)($_POST['produto_id']??0);$qty=max(1,(int)($_POST['quantidade']??1));$p=$this->products->search('');$found=null;foreach($p as $x)if((int)$x['id']===$id)$found=$x;
        if(!$found||$qty>(int)$found['quantidade'])$this->flash('danger','Produto inválido ou quantidade maior que o estoque.');else{$_SESSION['cart'][$id]=($_SESSION['cart'][$id]??0)+$qty;$this->flash('success','Produto adicionado ao carrinho.');}$this->redirect('index.php?route=cart'); }
    public function update(): void { $id=(int)($_POST['produto_id']??0);$qty=(int)($_POST['quantidade']??0);if($qty<=0)unset($_SESSION['cart'][$id]);else $_SESSION['cart'][$id]=$qty;$this->redirect('index.php?route=cart'); }
    public function remove(): void { unset($_SESSION['cart'][(int)($_POST['produto_id']??0)]);$this->redirect('index.php?route=cart'); }
    public function checkout(): void { try{$service=new SaleService($this->db,$this->products,new SaleRepository($this->db));$customer=($_POST['cliente_id']??'')!==''?(int)$_POST['cliente_id']:null;$id=$service->checkout($customer,$_SESSION['cart']??[]);unset($_SESSION['cart']);$this->flash('success','Venda #'.$id.' finalizada com sucesso.');}catch(Throwable $e){$this->flash('danger',$e->getMessage());}$this->redirect('index.php?route=cart'); }
}
