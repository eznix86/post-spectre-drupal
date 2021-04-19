<?php

namespace Drupal\post_spectre\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\post_spectre\Constant\PostSpectreType;

/**
 * Implements a field widget for PostSpectre.
 *
 * @FieldWidget(
 *   id = "post_spectre_widget",
 *   label = @Translation("Post Spectre Widget"),
 *   field_types = {
 *     "post_spectre_fieldtype",
 *   }
 * )
 */
class PostSpectreFieldWidget extends WidgetBase implements WidgetInterface {

    /**
     * {@inheritdoc}
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

        // Save the custom field machine name to allow other functions to access it for DB queries.
        \Drupal::configFactory()->getEditable('post_spectre.settings')->set('post_spectre.custom_field_name', $this->fieldDefinition->getName())->save();

        $element['#uid'] = Html::getUniqueId('post_spectre-' . $this->fieldDefinition->getName());

        $element['opt_out'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Opt-out'),
            '#default_value' => isset($items[$delta]->opt_out) ? $items[$delta]->opt_out : 0,
            '#return_value' => 1,
            '#description' => $this->t('Disable post spectre security for this content'),
        ];

        $element['post_spectre_type'] = array(
            '#title' => $this->t('Cross Origin'),
            '#type' => 'radios',
            '#default_value' => isset($items[$delta]->post_spectre_type) ? $items[$delta]->post_spectre_type :  PostSpectreType::OPEN_CROSS_ORIGIN_WINDOW,
            '#options' => array(
                PostSpectreType::OPEN_CROSS_ORIGIN_WINDOW => t('A. Open Cross-Origin Windows (Allow content to be opened in a new window)'),
                PostSpectreType::CROSS_ORIGIN_OPENERS => t('B. Cross-Origin Openers (Allow Federated sign-in forms involving payments or sign-in)'),
                PostSpectreType::CROSS_ORIGIN_OPENERS_IFRAME => t('B (i). Cross-Origin Openers (Allow content to be framed - iframe)'),
                PostSpectreType::CROSS_ORIGIN_OPENERS_POPUP => t('B (ii). Cross-Origin Openers (Allow content to be opened in a pop-up)')
            ),
        );

        return $element;
    }

}
