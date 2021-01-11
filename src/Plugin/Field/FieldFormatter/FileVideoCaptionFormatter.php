<?php

namespace Drupal\islandora_defaults\Plugin\Field\FieldFormatter;

use Drupal\file\Plugin\Field\FieldFormatter\FileVideoFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Cache\Cache;
use Drupal\islandora\IslandoraUtils;

/**
 * Plugin implementation of the 'file_video_caption' formatter.
 *
 * @FieldFormatter(
 *   id = "file_video_caption",
 *   label = @Translation("Video with Caption"),
 *   description = @Translation("Display the file using an HTML5 video tag and caption track."),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class FileVideoCaptionFormatter extends FileVideoFormatter {


  /**
   * The field definition.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface
   */
  protected $fieldDefinition;

  /**
   * The formatter settings.
   *
   * @var array
   */
  protected $settings;

  /**
   * The label display setting.
   *
   * @var string
   */
  protected $label;

  /**
   * The view mode.
   *
   * @var string
   */
  protected $viewMode;

  /**
   * Islandora utility functions.
   *
   * @var \Drupal\islandora\IslandoraUtils
   */
  protected $utils;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\islandora\IslandoraUtils $utils
   *   Islandora utils.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, IslandoraUtils $utils) {
    parent::__construct([], $plugin_id, $plugin_definition);

    $this->fieldDefinition = $field_definition;
    $this->settings = $settings;
    $this->label = $label;
    $this->viewMode = $view_mode;
    $this->thirdPartySettings = $third_party_settings;
    $this->utils = $utils;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('islandora.utils'));
  }

  /**
   * {@inheritdoc}
   */
  public static function getMediaType() {
    return 'video';
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $source_files = $this->getSourceFiles($items, $langcode);
    if (empty($source_files)) {
      return $elements;
    }

    $attributes = $this->prepareAttributes();
    foreach ($source_files as $delta => $files) {
      $file = $files[0]['file'];
      $medias = \Drupal::service('islandora.utils')->getReferencingMedia($file->id());
      $first_media = array_values($medias)[0];
      if ($first_media->get('field_captions')->entity != NULL) {
        $caption = $first_media->get('field_captions')->entity->createFileUrl();
      }
      $node = $this->utils->getParentNode($first_media);
      $thumbn_term = $this->utils->getTermForUri('http://pcdm.org/use#ThumbnailImage');
      $thumb_media = $this->utils->getMediaWithTerm($node, $thumbn_term);
      if ($thumb_media) {
        $poster = $thumb_media->get('field_media_image')->entity->createFileUrl();
      }

      $elements[$delta] = [
        '#theme' => 'file_video_with_caption',
        '#attributes' => $attributes,
        '#files' => $files,
        '#cache' => ['tags' => []],
      ];

      if (isset($caption)) {
        $elements[$delta]['#caption'] = $caption;
      }
      if (isset($poster)) {
        $elements[$delta]['#poster'] = $poster;
      }

      $cache_tags = [];
      foreach ($files as $file) {
        $cache_tags = Cache::mergeTags($cache_tags, $file['file']->getCacheTags());
      }
      $elements[$delta]['#cache']['tags'] = $cache_tags;
    }
    return $elements;
  }

}
