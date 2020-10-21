<?php

namespace Drupal\ids_layout\Plugin\Layout;

use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Form\FormStateInterface;

/**
 * Layout which floats right sidebar, which allows content to flow around it.
 */
class FloatColumnLayout extends LayoutDefault {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'column_width' => '33',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['column_width'] = [
      '#type' => 'select',
      '#title' => $this->t('Column width'),
      '#default_value' => $this->configuration['column_width'],
      '#options' => [
        '25' => '75%/25%',
        '33' => '67%/33%',
        '50' => '50%/50%',
      ],
      '#description' => $this->t('Choose the column widths for this layout.'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['column_width'] = $form_state->getValue('column_width');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    $build['#attributes']['class'] = [
      'layout',
      $this->getPluginDefinition()->getTemplate(),
      'clearfix',
    ];

    // Apply the width to right sidebar if it has content.
    if (!empty($build['first'])) {
      $colWidth = $this->configuration['column_width'] ?? '33';
      $build['first']['#attributes']['class'][] = "layout-region--col-{$colWidth}";
    }

    return $build;
  }

}
