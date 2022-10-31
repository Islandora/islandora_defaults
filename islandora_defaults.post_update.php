<?php

/**
 * @file
 * Post-update hooks.
 */

/**
 * Remove "enforced" dependency on this module from installed config.
 */
function islandora_defaults_post_update_remove_enforced_dependency() {
  // XXX: Notably absent from this list is the migration defined by the
  // migrate_plus.migration.islandora_defaults_tags config entity; however,
  // given this migration depends on content that is explicitly part of this
  // module (the CSV from which it takes its data), it would leave it in an
  // inconsistent state if we were to leave the entity intact.
  $targets = [
    'context.context.binary',
    'context.context.collection',
    'context.context.repository_content',
    'context.context.taxonomy_terms',
    'core.entity_form_display.node.islandora_object.default',
    'core.entity_view_display.media.file.open_seadragon',
    'core.entity_view_display.media.image.open_seadragon',
    'core.entity_view_display.node.islandora_object.binary',
    'core.entity_view_display.node.islandora_object.default',
    'core.entity_view_display.node.islandora_object.open_seadragon',
    'core.entity_view_display.node.islandora_object.pdfjs',
    'core.entity_view_display.node.islandora_object.teaser',
    'core.entity_view_mode.media.open_seadragon',
    'core.entity_view_mode.node.binary',
    'core.entity_view_mode.node.open_seadragon',
    'field.field.node.islandora_object.field_description',
    'field.field.node.islandora_object.field_display_hints',
    'field.field.node.islandora_object.field_member_of',
    'field.field.node.islandora_object.field_model',
    'node.type.islandora_object',
    'rdf.mapping.node.islandora_object',
    'views.view.openseadragon_media_evas',
    'views.view.pdfjs_media_evas',
  ];

  $diff_set = ['islandora_defaults'];

  /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
  $config_factory = \Drupal::service('config.factory');
  foreach ($targets as $target) {
    $editable_target = $config_factory->getEditable($target);
    if ($editable_target->isNew()) {
      // We do not want to _create_ any of the configs, just change them if they
      // exist, so if it turns out that we would be creating the config, skip
      // it.
      continue;
    }

    $current_set = $editable_target->get('dependencies.enforced.module');
    $post_diff = array_diff($current_set, $diff_set);
    if ($post_diff !== $current_set) {
      $editable_target->set('dependencies.enforced.module', $post_diff);
      $editable_target->save();
    }
  }
}
