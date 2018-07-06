<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolrImprovements;

use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Description of ItemValueExtractorDelegatorFactory
 *
 * @author pols12
 */
class SolrMappingFormDelegatorFactory implements DelegatorFactoryInterface
{
	public function __invoke(ContainerInterface $services, $name, callable $callback, array $options = null)
	{
		$valueExtractorManager = $services->get('Solr\ValueExtractorManager');
        $valueFormatterManager = $services->get('Solr\ValueFormatterManager');
        $api = $services->get('Omeka\ApiManager');
        $translator = $services->get('MvcTranslator');

        $form = new SolrMappingFormDelegator(null, $options);
        $form->setTranslator($translator);
        $form->setValueExtractorManager($valueExtractorManager);
        $form->setValueFormatterManager($valueFormatterManager);
        $form->setApiManager($api);

        return $form;
	}
}