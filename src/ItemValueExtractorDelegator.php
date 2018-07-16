<?php

/*
 * Edited by Pols12. Original version licence:
 * Copyright BibLibre, 2016-2017
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

use Solr\ValueExtractor\ItemValueExtractor;
use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\AbstractResourceRepresentation;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\Api\Representation\ItemRepresentation;

class ItemValueExtractorDelegator extends ItemValueExtractor
{
	/**
	 * @var Zend\Log\LoggerInterface Logger
	 */
	protected $logger;
	
	public function setLogger($logger) {
		$this->logger=$logger;
	}
	
	/**
	 * Overriding to manage item sets and media in the same way as subproperties.
	 */
	public function getAvailableFields() {
		$fields = parent::getAvailableFields();
		$fields['o:id'] = [ 'label' => 'Internal identifier' ];
		unset($fields['item_set']['children']);
		unset($fields['media']['children']);
		$fields['content'] = ['label' => 'HTML Content (for media)'];
		return $fields;
	}
	
	/**
	 * Overriding to handle the cases where $source is item_set or media without
	 * sub-property and the case where item_set or media are sub-property of
	 * non-item resource.
	 * @param AbstractResourceRepresentation $resource
	 * @param string $source
	 * @return array
	 */
	public function extractValue(AbstractResourceRepresentation $resource, $source) {
		if (preg_match('/^media\/(.*)|^media$/', $source, $matches)
				|| preg_match('/^item_set\/(.*)|^item_set$/', $source, $matches))
		{
			// Don’t try to index item set or media if $resource is not an item
			if(! $resource instanceof ItemRepresentation) {
				$this->logger->warn('Tried to get '.$matches[0].' of non item resource.');
				return [];
			}

			// Media or item_set indexing without sub-property set:
			elseif (empty($matches[1])) { 
				if('media' === $matches[0])
					return $this->extractMediaValue($resource, '');
				else //item_set
					return $this->extractItemSetValue($resource, '');
			}
        }
		return parent::extractValue($resource, $source);
	}
	
    /**
     * Extract the values of the given property of the given item.
     * If a value is a resource, then this method is called recursively with
     * the source part after the slash as $source.
     * @param AbstractResourceEntityRepresentation $resource Item
     * @param string|null $source Property (RDF term).
     * @return string[] Human-readable values.
     */
    protected function extractPropertyValue(AbstractResourceEntityRepresentation $resource, $source)
    {
        @list($property, $subProperty) = explode('/', $source, 2); //$subProperty may be NULL
		
		
		switch($property) {
			case '': // If item_set or media have been used without sub-property
				return [$resource->displayTitle()];
			case 'o:id':
				return [$resource->id()];
			case 'media':
				if(! $resource instanceof ItemRepresentation) {
					$this->logger->warn('Tried to get media of non item resource.');
					return [];
				}
				return $this->extractMediaValue($resource, $subProperty);
			case 'item_set':
				if(! $resource instanceof ItemRepresentation) {
					$this->logger->warn('Tried to get item_set of non item resource.');
					return [];
				}
				return $this->extractItemSetValue($resource, $subProperty);
		}
		
        $extractedValue = [];
        /* @var $values ValueRepresentation[] */
        $values = $resource->value($property, ['all' => true, 'default' => []]);
        foreach ($values as $value) {
            $type = $value->type();
            if ($type === 'literal' || $type == 'uri') {
                $extractedValue[] = (string) $value;
            } elseif ('resource' === explode(':', $type)[0]) {
                $this->extractPropertyResourceValue($extractedValue, $value, $subProperty);
            }
        }

        return $extractedValue;
    }

    /**
     * Extracts value(s) from resource-type value and adds them to already
     * extracted values (passed by reference).
     * @param array $extractedValues Already extracted values.
     * @param ValueRepresentation $value Resource-type value from which to
     * extract searched values.
     * @param null|string $property RDF term representing the property to
     * extract. If null, get the displayTitle() value.
     */
    protected function extractPropertyResourceValue(array &$extractedValues,
            ValueRepresentation $value, $property)
    {
        if (isset($property)) {
            $extractedValues = array_merge(
                    $extractedValues,
                    $this->extractValue($value->valueResource(), $property)
            );
        } else {
            $resourceTitle = $value->valueResource()->displayTitle('');
            if (!empty($resourceTitle)) {
                $extractedValues[] = $resourceTitle;
            }
        }
    }
}
