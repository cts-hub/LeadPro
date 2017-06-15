<?php
use Phalcon\Flash;
use Phalcon\Session;

class ExportController extends ControllerBase {
	protected $leads_obj;

	public function initialize() {
		$this->tag->setTitle('Экспорт');
		parent::initialize();
		$this->view->setVar('user_name', $_SESSION ['auth'] ['name']);
		$this->leads_obj = new Leads ();
	}

	public function indexAction() {
		$this->persistent = null;
		$this->view->setVar('arr_option_country', $this->leads_obj->getCountrysList());
		$this->view->setVar('arr_option_regions', $this->leads_obj->getRegionsList());
		$this->view->setVar('arr_option_buyers', $this->leads_obj->getBuyersList());
		$this->view->setVar('sellers', $this->leads_obj->getSellersList());
		$this->view->setVar('purchases', $this->leads_obj->getPurchasesList());
		$arr_option_export = $this->leads_obj->exportData();
		$this->view->setVar('arr_option_export', $arr_option_export);
		if ($this->request->isPost()) {
			$this->persistent->bayer_id   = $this->request->getPost('buyer');
			$this->persistent->cnt_number = $this->request->getPost('cnt_number');
			$this->persistent->country    = $this->request->getPost('country');

			$seller    = $this->request->getPost('sales');
			$purchases = $this->request->getPost('saleslist');

			$this->switch_region();
			$this->view->setVar('bayer_id', $this->persistent->bayer_id);
			$this->view->setVar('cnt_number', $this->persistent->cnt_number);
			$this->view->setVar('region', $this->persistent->region);
			$this->view->setVar('country', $this->persistent->string_country);
			$this->view->setVar('arr_buyers_preview', $this->leads_obj->getBuyersPreview($this->persistent->bayer_id));
			$leads = $this->leads_obj->getLeadsByParams($this->persistent->bayer_id, $this->persistent->string_country, $this->persistent->cnt_number, $this->persistent->region_type, $seller, $purchases);
			$this->view->setVar('leads', $leads);
		}
		$arr_option_export  = $this->leads_obj->exportData();
		$json_option_export = json_encode($arr_option_export);
		$this->view->setVar('json_option_export', $json_option_export);
	}

	public function exportAction() {
		if ($this->request->isPost()) {
			$file_name = $this->leads_obj->exportLeads($this->request->getPost('bayer_id'), $this->request->getPost('country'), $this->request->getPost('cnt_number'), $this->request->getPost('region_type'));
			$fname     = "/var/lib/mysql-files/export/" . $file_name [0] ['result'] . '.csv';
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

	public function addAction() {
		if ($this->request->isPost()) {
			$bayer        = $this->request->getPost('addbuyer');
			$bayer_desc   = $this->request->getPost('buyerdescr');
			$sales_apikey = $this->request->getPost('sales_apikey');
			$campaign_id  = $this->request->getPost('campaign_id');
			$brand        = $this->request->getPost('brand');
			$bayer        = $this->leads_obj->addBuyer($bayer, $bayer_desc, $sales_apikey, $campaign_id, $brand);
			if ($bayer) {
				$this->response->redirect("export/index");
			}
		}
	}

	public function apiAction() {
		if ($this->request->isPost()) {
			$bayer_id        = $this->request->getPost('bayer_id');
			$cnt_number      = $this->request->getPost('cnt_number');
			$country         = $this->request->getPost('country');
			$region_type     = $this->request->getPost('region_type');
			$api_sale_result = $this->leads_obj->getSalesLeadsApi($bayer_id, $country, $cnt_number, $region_type);
			$buyer_info      = $this->leads_obj->getBayerApi($bayer_id);

			$apikey      = $buyer_info[0]['apikey'];
			$campaign_id = $buyer_info[0]['campaign_id'];
			$brand_name  = $buyer_info[0]['brand_name'];

			// fetch exported leads
			$res = $this->leads_obj->getLeadsToApi();
			$cnt = count($res);
			for ($i = 0; $i < $cnt; $i++) {
				$row['key'] = $apikey;
				//$row ['campaign_id'] = $campaign_id;
				$row['campaign_id'] = 1175;
				$lead_id             = $res[$i]['lead_id'];
				$row['first_name']  = $res[$i]['first_name'];
				$row['last_name']   = $res[$i]['last_name'];
				$row['countryISO'] = $res[$i]['countryiso'];
// 				$row['countryISO']    = 'YTF';
				$row['email_address'] = $res[$i]['email_address'];
				$row['phone']         = $res[$i]['phone'];
				$row['currency']      = $res[$i]['currency'];
				// $row['campaign_keyword'] = $res[$i]['campaign_keyword'];
				$row['is_lead_only'] = $res[$i]['is_lead_only'];
				// $row['comment'] = $res[$i]['comment'];
				$row['send_register_email'] = $res[$i]['send_register_email'];
				$row['custom_refer'] = "user_defined";

				$data = $this->sendToApi($row, $brand_name);

				list($client_id, $code, $msg) = $this->chkResponseCode($data);
				$recordResult = $this->leads_obj->apiResponseRecord($lead_id, $client_id, $msg, $code);
			}
		}
	}

	protected function switch_region() {
		if ($this->request->getPost('country')) {
			$this->persistent->country = $this->request->getPost('country');
			foreach ($this->persistent->country as $item => $value) {
				$this->persistent->string_country .= '<a>' . $value . '</a>';
			}
			$this->persistent->region_type = 2;
			$this->view->setVar('region_type', 2);
		} else {
			$this->persistent->string_country = null;
			$this->persistent->region         = $this->request->getPost('region');
			foreach ($this->persistent->region as $item => $value) {
				$this->persistent->string_country .= '<a>' . $value . '</a>';
			}
			$this->persistent->region_type = 1;
			$this->view->setVar('region_type', 1);
		}
	}


}