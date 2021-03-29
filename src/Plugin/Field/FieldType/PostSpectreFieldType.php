<?php

namespace Drupal\post_spectre\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Implements a field type for PostSpectre.
 *
 * @FieldType(
 *   id = "post_spectre_fieldtype",
 *   label = @Translation("Post Spectre Field Type"),
 *   description = @Translation("Enable post spectre security"),
 *   category = @Translation("Security"),
 *   default_widget = "post_spectre_widget",
 *   default_formatter = "post_spectre_formatter",
 * )
 */
class PostSpectreFieldType extends FieldItemBase {

    /**
     * {@inheritdoc}
     */
    public static function defaultFieldSettings() {
        return [
                'status' => FALSE,
            ] + parent::defaultFieldSettings();
    }

    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition) {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
        $properties = [];

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty() {
        $status = $this->get('status')->getValue();
        $enabled = FALSE;

        if (isset($status)) {
            $enabled = TRUE;
        }

        return !$enabled;
    }

}
