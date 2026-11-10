<?php
final class CustomerController extends Controller {
    private CustomerRepository $repo;
    public function __construct() { $this->repo=new CustomerRepository(Database::connection()); }
    public function index(): void { $this->render('customers/index',['customers'=>$this->repo->all()]); }
    public function form(): void { $id=isset($_GET['id'])?(int)$_GET['id']:null; $customer=$id?$this->repo->find($id):null; $this->render('customers/form',['customer'=>$customer]); }
    public function save(): void {
        $id=isset($_POST['id'])?(int)$_POST['id']:null; $name=trim($_POST['nome']??'');
        if($name===''){ $this->flash('danger','Informe o nome do cliente.');$this->redirect('index.php?route=customers'); }
        try{$this->repo->save(['nome'=>$name,'email'=>trim($_POST['email']??''),'telefone'=>trim($_POST['telefone']??''),'documento'=>trim($_POST['documento']??'')],$id);$this->flash('success','Cliente salvo com sucesso.');}catch (Throwable $e) {
            error_log('ERRO AO SALVAR CLIENTE: ' . $e->getMessage());
        
            $this->flash('danger', 'Erro: ' . $e->getMessage());
        }
    }
    public function delete(): void { try{$this->repo->delete((int)($_POST['id']??0));$this->flash('success','Cliente excluído.');}catch(Throwable $e){$this->flash('danger','Cliente vinculado a uma venda não pode ser excluído.');}$this->redirect('index.php?route=customers'); }
}
