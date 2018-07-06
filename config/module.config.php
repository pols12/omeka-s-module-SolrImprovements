<?php
namespace SolrImprovements;

return [
	'solr_value_extractors' => [
        'delegators' => [
			//'items'
            \Solr\ValueExtractor\ItemValueExtractor::class => [ItemValueExtractorDelegatorFactory::class],
            'items' => [ItemValueExtractorDelegatorFactory::class],
        ],
    ],
    'controllers' => [
        'delegators' => [
			//'Solr\Controller\Admin\Mapping'
			\Solr\Controller\Admin\MappingController::class => [MappingControllerDelegatorFactory::class],
			'Solr\Controller\Admin\Mapping' => [MappingControllerDelegatorFactory::class],
        ],
    ],
    'form_elements' => [
        'delegators' => [
			//'Solr\Form\Admin\SolrMappingForm'
			\Solr\Form\Admin\SolrMappingForm::class => [SolrMappingFormDelegatorFactory::class]
        ],
    ],
    'view_manager' => [
		'controller_map' => [
//			'SolrImprovements' => 'solr/admin',
			'SolrImprovements\MappingControllerDelegator' => 'solr/admin/mapping',
        ],
        'template_path_stack' => [
//            __DIR__ . '/../../Solr/view',
            __DIR__ . '/../view',
        ],
		'template_map' => [
			'solr/admin/mapping/form' => __DIR__ . '/../view/solr/admin/mapping/form.phtml',
		],
    ],
//	'translator' => [
//        'translation_file_patterns' => [
//            [
//                'type' => 'gettext',
//                'base_dir' => __DIR__ . '/../language',
//                'pattern' => '%s.mo',
//                'text_domain' => null,
//            ],
//        ],
//    ],
];
