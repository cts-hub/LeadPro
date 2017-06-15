<?php
use Phalcon\Flash;
use Phalcon\Session;

class ExportController extends ControllerBase {

	protected $leads_obj;

	public function initialize() {
		$this->tag->setTitle('Экспорт');
		parent::initialize();
		$this->leads_obj = new Leads();
	}

	public function indexAction() {
		$this->view->setVar('arr_option_country', $this->leads_obj->getCountrysList());
		$this->view->setVar('arr_option_regions', $this->leads_obj->getRegionsList());
		$this->view->setVar('arr_option_buyers', $this->leads_obj->getBuyersList());
		$this->view->setVar('arr_export', $this->leads_obj->exportData());

		if ($this->request->isPost()) {
			if ($this->request->getPost('addbuyer')) {
				$bayer      = $this->request->getPost('addbuyer');
				$bayer_desc = $this->request->getPost('buyerdescr');
				$sales_apikey = $this->request->getPost('sales_apikey');
				$campaign_id = $this->request->getPost('campaign_id');
				$brand = $this->request->getPost('brand');
				$bayer      = $this->leads_obj->addBuyer($bayer, $bayer_desc,$sales_apikey,$campaign_id,$brand);

				if ($bayer) {
					$this->response->redirect("invoices/index");
				}
			} elseif ($this->request->getPost('buyer')) {
				$this->persistent->bayer_id   = $this->request->getPost('buyer');

				if(!$this->request->getPost('country')){
					$this->persistent->region     = $this->request->getPost('region', array('string', 'striptags'));
					$this->persistent->region_type     = 1;
				}else{
					$this->persistent->region     = $this->request->getPost('country');
					$this->persistent->region_type     = 2;
				}
				$this->persistent->cnt_number = $this->request->getPost('cnt_number');


				$this->view->setVar('bayer_id', $this->persistent->bayer_id);
				$this->view->setVar('cnt_number', $this->persistent->cnt_number);
				$this->view->setVar('region', $this->persistent->region);

				$this->view->setVar('arr_buyers_preview', $this->leads_obj->getBuyersPreview($this->persistent->bayer_id));

				$leads = $this->leads_obj->getLeadsByParams($this->persistent->bayer_id, $this->persistent->region, $this->persistent->cnt_number);
				$this->view->setVar('leads', $leads);
			} elseif ($this->request->getPost('export')) {
/*				$this->persistent->bayer_id   = $this->request->getPost('buyer');
				$this->persistent->cnt_number = $this->request->getPost('cnt_number');
				$this->persistent->country    = $this->request->getPost('country');
				$this->persistent->region     = $this->request->getPost('region', array('string', 'striptags'));
				$this->leads_obj->exportLeads($this->persistent->bayer_id, '<a>' . $this->persistent->region . '</a>', $this->persistent->cnt_number, 1);*/
			} else {
				//$this->request->getPost('SHOW')
			}

		}
	}



	/*public function companiesAction() {
	if ($this->request->isPost()) {
		$sales      = $this->request->getPost('addsales');
		$sales_desc = $this->request->getPost('salesdescr');
		$sales      = $this->leads_obj->addSeller($sales, $sales_desc);
		if ($sales) {
			$this->response->redirect("invoices/companies");
		}
	}
	$this->view->setVar('sellers', $this->leads_obj->getSellersList());
	$file_import_info = $this->leads_obj->getLastImport();
	$this->view->setVar('file_import_info', $file_import_info);

	if ($this->request->hasFiles() == true) {
		foreach ($this->request->getUploadedFiles() as $file) {
			$fileName           = $file->getName();
			$filename           = time() . $fileName;
			$result             = $file->moveTo('/var/lib/mysql-files/import/' . $filename);
			$sales_id           = $this->request->getPost('sales');
			$file_import_report = $this->leads_obj->loadFileData($filename, $sales_id, $this->session->get(['auth']['id']));
			if ($result) {
				$this->response->redirect("invoices/companies");
			}
		}
	}
}*/

	public function exportAction() {
		$file_name = $this->leads_obj->exportLeads($this->persistent->bayer_id, '<a>' . $this->persistent->region . '</a>', $this->persistent->cnt_number, $this->persistent->region_type);
		$fname     = "/var/lib/mysql-files/export/" . $file_name[0]['result'] . '.csv';
		
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
			exit;
		}
	}

	public function apiAction() {
		$to_api = $this->leads_obj->getBayerApi(14);
		print_r($to_api);
	}
}