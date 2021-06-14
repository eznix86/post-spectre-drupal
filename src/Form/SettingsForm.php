<?php

namespace Drupal\post_spectre\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\post_spectre\Constant\PostSpectreType;

/**
 * Configure post_spectre settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'post_spectre_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['post_spectre.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $default = $this->config('post_spectre.settings')->get(PostSpectreType::OPT_OUT);

    $form[PostSpectreType::OPT_OUT] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable globally'),
      '#default_value' => isset($default) ? $default : 0,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('post_spectre.settings')
      ->set(PostSpectreType::OPT_OUT, $form_state->getValue(PostSpectreType::OPT_OUT))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
