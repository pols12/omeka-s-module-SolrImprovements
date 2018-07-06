<?php
namespace SolrImprovements;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Omeka\Module\AbstractModule;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractController;
use Zend\View\Renderer\PhpRenderer;

class Module extends AbstractModule implements
		AutoloaderProviderInterface {

	/**
	 * Return an array for passing to Zend\Loader\AutoloaderFactory.
	 *
	 * @return array
	 */
	 public function getAutoloaderConfig()
	 {
		return [
			\Zend\Loader\StandardAutoloader::class => [
				'namespaces' => [
					// Autoload all classes of this namespace from '/module/DateHandler/src/'
					__NAMESPACE__ => __DIR__ . '/src',
				]
			]
		];
	 }

	/**
	 * Returns configuration to merge with application configuration
	 *
	 * @return array
	 */
	public function getConfig(){
		return include __DIR__ . '/config/module.config.php';
	}
	
}

