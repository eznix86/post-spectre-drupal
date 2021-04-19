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
                'opt_out' => FALSE,
            ] + parent::defaultFieldSettings();
    }

    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition) {
        return [
            'columns' => [
                'opt_out' => [
                    'type' => 'text',
                    'not null' => FALSE,
                ],
                'post_spectre_type' => [
                    'type' => 'text',
                    'not null' => FALSE,
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
        $properties = [];

        $properties['opt_out'] = DataDefinition::create('string')
            ->setLabel(t('Opt-out from post spectre'))
            ->setRequired(FALSE);

        $properties['post_spectre_type'] = DataDefinition::create('string')
            ->setLabel(t('Post spectre type secure'))
            ->setRequired(FALSE);

        return $properties;
    }
}
