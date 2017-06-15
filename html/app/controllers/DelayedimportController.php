<?php


class DelayedimportController extends ControllerBase {

	protected $leads_model;
	protected $delayeds_model;

	public function initialize() {
		$this->tag->setTitle('Экспорт');
		parent::initialize();
		$this->leads_model    = new Leads();
		$this->delayeds_model = new Delayeds();
	}

	public function indexAction() {
		$this->view->setVar('sellers', $this->leads_model->getSellersList());
		$this->view->setVar('purchases', $this->leads_model->raw_purchases_list());
	}

	public function downloadAction() {
		if ($this->request->isPost()) {
			$purchase_id = $this->request->getPost('saleslist');
			$user_id     = $_SESSION['auth']['id'];
			$file_name   = $this->delayeds_model->raw_leads_download_procedure($purchase_id, $user_id);
			$fname       = "/var/lib/mysql-files/download/" . $file_name [0] ['result'] . '.csv';
			if (file_exists($fname)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename=' . basename($fname));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($fname));
				ob_clean();
				flush();
				readfile($fname);
				exit ();
			}
		}
	}

	public function importAction() {
		if ($this->request->isPost()) {
			$seller_id     = $this->request->getPost('seller');
			$purchase_desc = $this->request->getPost('purchases_desc');
			if ($this->request->hasFiles() == true) {
				foreach ($this->request->getUploadedFiles() as $file) {
					$fileName           = $file->getName();
					$filename           = time() . $fileName;
					$result             = $file->moveTo('/var/lib/mysql-files/import/' . $filename);
					$seller_id          = $this->request->getPost('seller');
					$file_import_report = $this->leads_model->loadFileData($filename);
					$result_raw         = $this->delayeds_model->cpurchase_raw_delayed_procedure($seller_id, $_SESSION['auth']['id'], $purchase_desc);
					if ($result_raw) {
						$this->response->redirect("delayedimport/index");
					}
				}
			}
		}
	}
}