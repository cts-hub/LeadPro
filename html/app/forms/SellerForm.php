<?php

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\PresenceOf;

class SellerForm extends Form {

	public function initialize($entity = null, $options = null) {
		// Name
		$name = new Text('addsales');
		$name->setLabel('addsales');
		$name->setFilters(array('striptags', 'string'));
		$name->addValidators(array(
			new PresenceOf(array(
				'message' => 'Name is required'
			))
		));
		$this->add($name);

		// Email
		$desc = new Text('salesdescr');
		$desc->setLabel('E-salesdescr');
		$desc->setFilters(array('striptags', 'string'));
		$desc->addValidators(array(
			new PresenceOf(array(
				'message' => 'Name is required'
			))
		));
		$this->add($desc);
	}
}