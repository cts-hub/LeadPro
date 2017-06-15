<?php
use Phalcon\Flash;
use Phalcon\Session;

class ImportController extends ControllerBase {

	protected $leads_obj;

	public function initialize() {
		$this->tag->setTitle('Import');
		parent::initialize();
		$this->leads_obj = new Leads();
	}

	public function indexAction() {
		$this->view->setVar('sellers', $this->leads_obj->getSellersList());
		$this->view->setVar('arr_option_buyers', $this->leads_obj->getBuyersList());
		if ($this->request->isPost()) {
			$sales      = $this->request->getPost('addsales');
			$sales_desc = $this->request->getPost('salesdescr');
			$sales      = $this->leads_obj->addSeller($sales, $sales_desc);
		}
		$file_import_info = $this->leads_obj->getLastImport();
		$this->view->setVar('file_import_info', $file_import_info);

		if ($this->request->hasFiles() == true) {
			foreach ($this->request->getUploadedFiles() as $file) {
				$fileName           = $file->getName();
				$filename           = time() . $fileName;
				$result             = $file->moveTo('/var/lib/mysql-files/import/' . $filename);
				$sales_id           = $this->request->getPost('sales');
				$file_import_report = $this->leads_obj->loadFileData($filename);
				$this->leads_obj->purchProc($sales_id, $_SESSION['auth']['id']);
				if ($result) {
					$this->response->redirect("import/index");
				}
			}
		}
	}

	public function apiAction() {
		if ($this->request->isPost()) {
			$campaign_id = $this->request->getPost('campaign');
			settype($campaign_id, 'integer');

			$purchase_id = $this->request->getPost('saleslist');
			settype($purchase_id, 'integer');
// 			$purchase_id = 2;
			echo(" purchase_id: " . "$purchase_id");

// 			$bayer_id    = 8;//buyer only for transit devsharewallet.airsoftltd.com
// 			$bayer_id    = 12;//buyer only for transit trader.bnroptions.com
			$bayer_id = 13;//buyer only for transit trader.noblecapitalhouse.com
			//TODO 
			//$brand_name  = 8;
			$user_id = $this->session->get('auth')['id'];
			//echo $user_id;
			$buyer_info = $this->leads_obj->getBayerApi($bayer_id);
			$brand_name = $buyer_info[0]['brand_name'];
			$apikey     = $buyer_info[0]['apikey'];
// 			$campaign_id = $buyer_info[0]['campaign_id'];
			$brand_id        = 1;
			$api_sale_result = $this->leads_obj->getSalesLeadsApiRaw($purchase_id, $bayer_id, $brand_id, $campaign_id, $user_id);


			// fetch exported leads
			$res = $this->leads_obj->getLeadsToApi();
			$cnt = count($res);
			echo "total" . $cnt . " ";
			for ($i = 0; $i < $cnt; $i++) {
				$row['key']         = $apikey;
				$row['campaign_id'] = $campaign_id;
				$lead_id            = $res[$i]['lead_id'];
				$row['first_name']  = $res[$i]['first_name'];
// 				$row['last_name']   = $res[$i]['last_name'];
// 				$row['first_name']  = "Test";
				$row['last_name']  = "_";
				$row['countryISO'] = $res[$i]['countryiso'];
// 				$row['countryISO']    = "YTU";
				$row['email_address'] = $res[$i]['email_address'];
				$row['phone']         = $res[$i]['phone'];
				$row['currency']      = $res[$i]['currency'];
				//$row['campaign_keyword'] = $res[$i]['campaign_keyword'];
				$row['is_lead_only'] = $res[$i]['is_lead_only'];
				//$row['comment'] = $res[$i]['comment'];
				$row['send_register_email'] = $res[$i]['send_register_email'];
				echo("$i ");
				$data = $this->sendToApi($row, $brand_name);

				echo '<pre>';
				print_r($data);
				echo '</pre>';
				if (!empty($data['createLead']['client_id'])) {
					$client_id = $data['createLead']['client_id'];
					$code      = 0;
					$msg       = '0';
				} else {
					$client_id = 0;
					$code      = $data['error']['code'];
					$msg       = $data['error']['message'];
				}


				$in_api_tr_att_id = 1;
				$recordResult     = $this->leads_obj->apiResponseTransitRecord($in_api_tr_att_id, $lead_id, $client_id, $msg, $code);

			}
			$report = $this->leads_obj->getLastApiTransitReport();
			echo "Back Office responses summary";
			echo '<pre>';
			print_r($report);
			echo '</pre>';


		}
	}

	public function sendToApi($data, $brand) {

		$url = 'http://' . $brand . '/back.php/affiliate/externalSorce/api?method=createLead&' . http_build_query($data);

		echo "\n\nRequest:\n\n";
		echo $url;
		echo "\n\n";

		$ch      = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($page, true);

// 		echo "\n\nResponse inside sendToApi:\n\n";
// 		echo '<pre>';
// 		print_r($data);
// 		echo '</pre>';
		return $data;
	}

}