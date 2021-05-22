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

        $element[PostSpectreType::OPT_OUT] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Opt-out'),
            '#default_value' => isset($items[$delta]->opt_out) ? $items[$delta]->opt_out : 0,
            '#return_value' => 1,
            '#description' => $this->t('Disable post spectre security for this content'),
        ];

        $element[PostSpectreType::POST_SPECTRE_TYPE] = array(
            '#title' => $this->t('Cross Origin'),
            '#description' => $this->t('Manage your level of isolation/cross-origin sharing'),
            '#type' => 'radios',
            '#default_value' => isset($items[$delta]->post_spectre_type) ? $items[$delta]->post_spectre_type :  PostSpectreType::DEFAULT,
            '#options' => array(
                PostSpectreType::DEFAULT => t('A. Same-Origin Only'),
                PostSpectreType::FULL_ISOLATION => t('B. Same-Origin Only with full isolation (No assets with cross origin will be allowed)'),
                PostSpectreType::OPEN_CROSS_ORIGIN_WINDOW => t('C. Open Cross-Origin Windows (Allow same-site window only)'),
                PostSpectreType::CROSS_ORIGIN_OPENERS => t('D. Cross-Origin Openers (Allow cross-site window/tab sharing. Ex: Federated sign-in forms involving payments or sign-in (SSO))'),
                PostSpectreType::CROSS_ORIGIN_OPENERS_IFRAME => t('E. Cross-Origin Openers (Allow cross-site content to be framed only. Ex: iframe)'),
                PostSpectreType::CROSS_ORIGIN_OPENERS_POPUP => t('F. Cross-Origin Openers (Allow cross-site content to be framed and cross-site window/pop-up sharing)')
            ),
        );

        return $element;
    }

}
