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
class MappingControllerDelegatorFactory implements DelegatorFactoryInterface
{
	public function __invoke(ContainerInterface $services, $name, callable $callback, array $options = null)
	{
		$valueExtractorManager = $services->get('Solr\ValueExtractorManager');

        $controller = new MappingControllerDelegator;
        $controller->setValueExtractorManager($valueExtractorManager);

        return $controller;
	}
}