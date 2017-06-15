<?php

class Delayeds extends Bases {

	public function initialize() {
		parent::initialize();
	}

	public function raw_leads_download_procedure($purchase_id, $user_id) {
		return $this->getData("CALL raw_leads_download_procedure($purchase_id, $user_id)");
	}

	public function cpurchase_raw_delayed_procedure($seller_id, $user_id, $purchase_desc) {
		return $this->getData("CALL cpurchase_raw_delayed_procedure($seller_id,$user_id,'{$purchase_desc}')");
	}
}