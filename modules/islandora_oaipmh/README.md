# Islandora OAI-PMH Endpoint

This submodule leverages the [REST OAI-PMH module](https://www.drupal.org/project/rest_oai_pmh)
to provide an OAI-PMH endpoint for harvesting repository content.

## Sets available

By default the OAI-PMH endpoint is located at 'yoursite.example/oai/request'.
It contains (when queried with `?verb=ListSets`) one Set representing each
Collection in the repository containing all the children of that collection,
and a Set containing all Repository Items that are not members of collections
(and are not Collections themselves). Included, but disabled by default, is
a set identified as 'oai\_pmh:all\_repository\_items'. It includes
all items using the 'Repository item' content type that _don't_ use 
the 'Collection' model. Additional sets can be created by making Views with the
"Entity Reference" View display mode and enabling them on the rest\_oai\_pmh
configuration page: `/admin/config/services/rest/oai-pmh`. The module can use
any view using an Entity Reference view display mode, they do not need to be
part of the provided OAI-PMH views, they are simply available as a convenience.

The rest\_oai\_pmh module indexes set membership, so repository items may not appear
immediately in their respective sets. Indexing will happen automatically during
cron runs but can be triggered manually at `/admin/config/services/rest/oai-pmh/queue`.

## Mappings

The REST OAI PMH module expects Dublin Core metadata to be made available
either through the RDF module or the Metatag module. By default, this module
uses the [Repository Item content model's RDF mapping](http://islandora.github.io/documentation/user-documentation/content_types/#update-create-an-rdf-mapping).
However, the default RDF mapping for the Linked Agent field is not Dublin 
Core so is not supported by REST OAI PMH. 

Including agent links in the OAI-PMH metadata
currently requires updating the RDF mapping to include a Dublin Core predicate
for that field or any other additional Typed Relation fields. Alternatively, the `rest_oai_pmh` module 
also supports defining mappings with the
[metatag module](https://www.drupal.org/project/metatag) or creating a custom
OaiMetadataMap plugin.

## Configuration

1. Install rest\_oai\_pmh (e.g. `composer require drupal/rest\_oai\_pmh`).
1. Enable this module (e.g. `drush en -y islandora\_oaipmh`).
1. Trigger the OAI-PMH indexer: Click the button found on the page at `http://localhost:8000/admin/config/services/rest/oai-pmh/queue` (or wait for cron)
1. Give anonymous users the "Access GET on OAI-PMH resource" permission.

Your OAI-PMH Endpoint should now be ready. e.g. `http://localhost:8000/oai/request?verb=ListRecords&metadataPrefix=oai_dc`
