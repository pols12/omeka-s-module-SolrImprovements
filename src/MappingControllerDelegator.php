<?php

/*
 * Copyright BibLibre, 2017
 * Copyright Daniel Berthereau, 2017-2018
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace SolrImprovements;

use Solr\Controller\Admin\MappingController;
use Solr\Form\Admin\SolrMappingForm;
use Zend\View\Model\ViewModel;

class MappingControllerDelegator extends MappingController
{
    public function addAction()
    {
        $solrNodeId = $this->params('nodeId');
        $resourceName = $this->params('resourceName');

        $solrNode = $this->api()->read('solr_nodes', $solrNodeId)->getContent();

        $form = $this->getForm(SolrMappingForm::class, [
            'solr_node_id' => $solrNodeId,
            'resource_name' => $resourceName,
        ]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
//			var_dump($this->params()->fromPost());exit;
            if ($form->isValid()) {
                $data = $form->getData();
				var_dump($data);
				
				$this->sourceArrayToStr($data['o:source']);
//				var_dump($data['o:source']); exit;
                $data['o:solr_node']['o:id'] = $solrNodeId;
                $data['o:resource_name'] = $resourceName;
                $this->api()->create('solr_mappings', $data);

                $this->messenger()->addSuccess('Solr mapping created.'); // @translate

                return $this->redirect()->toRoute('admin/solr/node-id-mapping-resource', [
                    'nodeId' => $solrNodeId,
                    'resourceName' => $resourceName,
                ]);
            } else {
				var_dump($form->getMessages());
                $this->messenger()->addError('There was an error during validation'); // @translate
            }
        }

        $view = new ViewModel;
        $view->setVariable('solrNode', $solrNode);
        $view->setVariable('form', $form);
        $view->setVariable('schema', $this->getSolrSchema($solrNodeId));
//        $view->setVariable('sourceLabels', $this->getSourceLabels()); // @dkm
        return $view;
    }

    public function editAction()
    {
        $solrNodeId = $this->params('nodeId');
        $resourceName = $this->params('resourceName');
        $id = $this->params('id');

        $mapping = $this->api()->read('solr_mappings', $id)->getContent();

        $form = $this->getForm(SolrMappingForm::class, [
            'solr_node_id' => $solrNodeId,
            'resource_name' => $resourceName,
        ]);
        $mappingData = $mapping->jsonSerialize();
        $this->sourceStrToArray($mappingData['o:source']);
        $form->setData($mappingData);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()
					|| (count($errors = $form->getMessages()) == 1
							&& array_key_exists('csrf', $errors)) )
			{
                $data = $form->getData();
				$this->sourceArrayToStr($data['o:source']);
                $data['o:solr_node']['o:id'] = $solrNodeId;
                $data['o:resource_name'] = $resourceName;
                $this->api()->update('solr_mappings', $id, $data);

                $this->messenger()->addSuccess('Solr mapping modified.'); // @translate
                $this->messenger()->addWarning('Don’t forget to check search pages that use this mapping.'); // @translate

                return $this->redirect()->toRoute('admin/solr/node-id-mapping-resource', [
                    'nodeId' => $solrNodeId,
                    'resourceName' => $resourceName,
                ]);
            } else {
                $this->messenger()->addError('There was an error during validation'); // @translate
            }
        }

        $view = new ViewModel;
        $view->setVariable('mapping', $mapping);
        $view->setVariable('form', $form);
        $view->setVariable('schema', $this->getSolrSchema($solrNodeId));
//        $view->setVariable('sourceLabels', $this->getSourceLabels()); // @dkm
		$view->setTemplate('solr/admin/mapping/edit'); // @delegator
        return $view;
    }

	/**
	 * Turns
	 * [
	 *	0 => ['source' => "foo"]
	 *	1 => ['source' => "bar"]
	 * ]
	 * into "foo/bar".
	 * @param array $source
	 */
	protected function sourceArrayToStr(&$source) {
		$source = implode(
				'/',
				array_map(
					function($v) { return $v['source']; },
					$source
				)
		);
	}
	
	protected function sourceStrToArray(&$source) {
		$source = explode('/', $source);
		$source = array_map(
			function($v) { return ['source' => $v]; },
			$source
		);
	}

}
