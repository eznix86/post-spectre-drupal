<?php

namespace Drupal\post_spectre\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Implements a field formatter for PostSpectre.
 *
 * @FieldFormatter(
 *   id = "post_spectre_formatter",
 *   label = @Translation("Post Spectre Formatter"),
 *   field_types = {
 *     "post_spectre_fieldtype",
 *   }
 * )
 */
class PostSpectreFieldFormatter extends FormatterBase {

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
        $summary = [];
        $summary[] = $this->t('Formatter for Post Spectre Widget');
        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = [];

        return $elements;
    }

}
