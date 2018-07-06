<?php

/*
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
use Omeka\Api\Representation\ValueRepresentation;

class ItemValueExtractorDelegator extends ItemValueExtractor
{
    /**
     * Extract the values of the given property of the given item.
     * If a value is a resource, then this method is called recursively with
     * the source part after the slash as $source.
     * @param AbstractResourceEntityRepresentation $representation Item
     * @param string $source Property (RDF term).
     * @return string[] Human-readable values.
     */
    protected function extractPropertyValue(AbstractResourceEntityRepresentation $representation, $source)
    {
        @list($property, $subProperty) = explode('/', $source, 2);
        $extractedValue = [];
        /* @var $values ValueRepresentation[] */
        $values = $representation->value($property, ['all' => true, 'default' => []]);
        foreach ($values as $i => $value) {
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
                    $this->extractPropertyValue($value->valueResource(), $property)
            );
        } else {
            $resourceTitle = $value->valueResource()->displayTitle('');
            if (!empty($resourceTitle)) {
                $extractedValues[] = $resourceTitle;
            }
        }
    }
}
