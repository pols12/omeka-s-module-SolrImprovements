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

use Solr\Form\Admin\SolrMappingForm;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

class SolrMappingFormDelegator extends SolrMappingForm
{
    public function init()
    {
        $translator = $this->getTranslator();

        $this->add([
            'type' => Collection::class,
            'name' => 'o:source',
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => true,
                'label' => $translator->translate('Source'),
                'target_element' => new SourceFieldset(null, [
							'options' => $this->getSourceOptions()
						]),
            ],
        ]);

        $this->add([
            'name' => 'o:field_name',
            'type' => Text::class,
            'options' => [
                'label' => 'Solr field', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $settingsFieldset = new Fieldset('o:settings');
        $settingsFieldset->add([
            'name' => 'formatter',
            'type' => Select::class,
            'options' => [
                'label' => $translator->translate('Formatter'),
                'value_options' => $this->getFormatterOptions(),
                'empty_option' => 'None', // @translate
            ],
        ]);
        $settingsFieldset->add([
            'name' => 'label',
            'type' => Text::class,
            'options' => [
                'label' => $translator->translate('Default label'),
                'info' => $translator->translate('The label is automatically translated if it exists in Omeka.'),
            ],
        ]);
        $this->add($settingsFieldset);

        $inputFilter = $this->getInputFilter();
        $settingsFilter = $inputFilter->get('o:settings');
        $settingsFilter->add([
            'name' => 'formatter',
            'required' => false,
        ]);
    }
	
	public function getInputFilter() {
		$filters = parent::getInputFilter();
		$filters->remove('csrf');

		return $filters;
	}
}
