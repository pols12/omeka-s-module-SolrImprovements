# SolrImprovements module for Omeka S

Extends [Solr module](https://github.com/biblibre/omeka-s-module-Solr) for
[Omeka-S](https://github.com/omeka/omeka-s) to let it index properties
recursively. In fact, when a property value is a resource, you can choose
what property of that resource to index.

Solr module ≥v0.4.0 is required. If you want to index HTML media content,
you need to install a version including [aaeb32e commit](https://github.com/biblibre/omeka-s-module-Solr/commit/aaeb32e9ac572d2a4bf2ff46ca85d5d819cfb6c3).
Last master snapshot is recommended.

## See also
These improvements are included in [an updated fork](https://github.com/Daniel-KM/omeka-s-module-Solr).

## Licence

Most of the code comes from Solr module, so it is owned and copyrighted by
[Biblibre](https://github.com/biblibre) and [Daniel Berthereau](https://github.com/Daniel-KM)
and [licensed under CeCILL FREE SOFTWARE LICENSE AGREEMENT v2.1](https://github.com/biblibre/omeka-s-module-Solr/blob/master/LICENSE).

My contributions to that code inherit from this copyleft license, so I
publish them under CeCILL license v2.1 too.
