<?php

namespace SolrImprovements;

use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class SourceFieldset extends Fieldset implements InputFilterProviderInterface
{
	public function __construct($name = null, $options = array()) {
		parent::__construct($name, $options);
		$this->init();
	}
	
	public function init()
	{
		$this->add([
			'name' => 'source',
			'type' => Element\Select::class,
			'options' => [
				'value_options' => $this->getOption('options'),
				'empty_option' => 'MaSource',
			],
		]);
	}

	public function getInputFilterSpecification() {
		return [
			'source' => [
				'required' => true,
			]
		];
	}

}
