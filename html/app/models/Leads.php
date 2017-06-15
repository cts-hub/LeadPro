<?php

class Leads extends Bases {

	public function initialize() {
		parent::initialize();
	}

	public function getLeadsToApi() {
		$sql      = "SELECT * FROM exporttable";
		$allLeads = $this->db->query($sql);
		return $allLeads->fetchAll(PDO::FETCH_ASSOC);
	}

	public function exportLeads($bayer_id, $region, $count, $type_region) {
		return $this->getData("CALL sale_procedure($bayer_id,'{$region}', $count, $type_region)");
	}

	public function getLeadsByParams($bayer_id, $region, $count, $type_region, $seller, $purchases) {
		return $this->getData("CALL sale_preview_procedure($bayer_id,'{$region}',$count,$type_region,$seller,$purchases)");
	}

	public function getLastImport() {
		//return $this->getData("CALL import_by_user_procedure()");
		return $this->getData("CALL last_purchase_report()");
	}

	public function getLastApiTransitReport() {
		return $this->getData("CALL last_api_transit_report()");
	}

	public function getCountrysList() {
		return $this->getData("CALL countries_list()");
	}

	public function getCountryCodeById($country) {
		return $this->getData("CALL get_country_A2('{$country}')");
	}

	public function getRegionsList() {
		return $this->getData("CALL regions_list()");
	}

	public function getCampaignsList() {
		return $this->getData("CALL campaigns_list();");
	}

	public function getBuyersList() {
		return $this->getData("CALL buyers_list()");
	}

	public function getSellersList() {
		return $this->getData("Call sellers_list()");
	}

	public function getPurchasesList() {
		return $this->getData("CALL purchases_list()");
	}

	public function raw_purchases_list() {
		return $this->getData("CALL raw_purchases_list()");
	}

	public function getRawPurchasesList($sales_id, $user_id, $purchase_desc) {
		return $this->db->query("CALL cpurchase_raw_procedure($sales_id,$user_id,'{$purchase_desc}')");
	}

	public function getBayerApi($bayer_id) {
		return $this->getData("CALL buyer_api_info($bayer_id)");
	}

	public function getSalesLeadsApi($bayer_id, $region, $count, $type_region) {
		return $this->getData("CALL c_api_sale_procedure($bayer_id,'{$region}', $count, $type_region)");
	}

	public function getSalesLeadsApiRaw($purchase_id, $bayer_id, $brand_id, $campaign_id, $user_id) {
		return $this->getData("CALL c_api_transit_procedure($purchase_id, $bayer_id, $brand_id, $campaign_id, $user_id)");
	}

	public function exportData() {
		return $this->getData("CALL last_sale_attempt_report()");
	}

	public function getBuyersPreview($id) {
		return $this->getData("CALL buyerpreview($id)");
	}

	public function addBuyer($bayer_name, $bayer_desc, $sales_apikey, $campaign_id, $brand) {
		return $this->getData("CALL add_buyer('{$bayer_name}','{$bayer_desc}','{$sales_apikey}',$campaign_id,'{$brand}')");
	}

	public function addSeller($seller_name, $seller_desc) {
		return $this->getData("CALL add_seller('{$seller_name}','{$seller_desc}')");
	}

	public function processResponses() {
		return $this->getData("CALL api_process_responses()");
	}

	public function apiResponseRecord($lead_id, $return_client_id, $msg, $err_code) {
		return $this->getData("call api_record_responce($lead_id, $return_client_id, '{$msg}', $err_code)");
	}

	public function apiResponseTransitRecord($in_api_tr_att_id, $lead_id, $return_client_id, $msg, $err_code) {
		return $this->getData("call api_record_transit_responce($in_api_tr_att_id,$lead_id, $return_client_id, '{$msg}', $err_code)");
	}

	public function purchProc($seller, $user) {
		return $this->getData("CALL cpurchase_procedure($seller,$user)");
	}

	public function addCampaign($campaign_id, $campaign_name, $campaign_desc) {
		return $this->getData("CALL add_campaign('{$campaign_name}',$campaign_id,'{$campaign_desc}')");
	}

	public function loadFileData($name) {
		$query = <<<eof
    LOAD DATA INFILE "/var/lib/mysql-files/import/$name"
     INTO TABLE importtable
     FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '"'
     LINES TERMINATED BY "\n"
     IGNORE 1 LINES
    (c1,c2,c3,c4,c5)
eof;
		$this->db->query($query);
	}
}