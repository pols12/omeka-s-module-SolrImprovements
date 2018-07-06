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
class ItemValueExtractorDelegatorFactory implements DelegatorFactoryInterface
{
	public function __invoke(ContainerInterface $services, $name, callable $callback, array $options = null)
	{
		$api = $services->get('Omeka\ApiManager');
//        $config = $services->get('Config'); // @dkm
//        $baseFilepath = $config['file_store']['local']['base_path'] ?: (OMEKA_PATH . '/files'); // @dkm

        $itemValueExtractor = new ItemValueExtractorDelegator;
        $itemValueExtractor->setApiManager($api);
//        $itemValueExtractor->setBaseFilepath($baseFilepath); // @dkm

        return $itemValueExtractor;
	}
}