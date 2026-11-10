<?php
final class ReportController extends Controller {
    private SaleRepository $repo; private SaleService $service;
    public function __construct(){ $db=Database::connection();$p=new ProductRepository($db);$this->repo=new SaleRepository($db);$this->service=new SaleService($db,$p,$this->repo); }
    public function index(): void { $this->render('reports/index',['sales'=>$this->repo->all($_GET['de']??'',$_GET['ate']??''),'from'=>$_GET['de']??'','to'=>$_GET['ate']??'']); }
    public function detail(): void { $sale=$this->repo->find((int)($_GET['id']??0));if(!$sale){$this->flash('danger','Venda não encontrada.');$this->redirect('index.php?route=reports');}$this->render('reports/detail',['sale'=>$sale]); }
    public function cancel(): void { try{$this->service->cancel((int)($_POST['id']??0));$this->flash('success','Venda cancelada e estoque restaurado.');}catch(Throwable $e){$this->flash('danger',$e->getMessage());}$this->redirect('index.php?route=reports'); }
}
