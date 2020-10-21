<?php

namespace Drupal\ids_layout\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Base class of layouts with configurable widths.
 *
 * @internal
 *   Plugin classes are internal.
 */
class TwoColumnBricksLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    return $configuration + [
      'above_widths' => $this->getDefaultWidth(),
      'below_widths' => $this->getDefaultWidth(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['above_widths'] = [
      '#type' => 'select',
      '#title' => $this->t('Column widths above'),
      '#default_value' => $this->configuration['above_widths'],
      '#options' => $this->getWidthOptions(),
      '#description' => $this->t('Choose the column widths for the upper columns.'),
    ];
    $form['below_widths'] = [
      '#type' => 'select',
      '#title' => $this->t('Column widths below'),
      '#default_value' => $this->configuration['below_widths'],
      '#options' => $this->getWidthOptions(),
      '#description' => $this->t('Choose the column widths for the lower columns.'),
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['above_widths'] = $form_state->getValue('above_widths');
    $this->configuration['below_widths'] = $form_state->getValue('below_widths');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);
    $above_widths = $this->configuration['above_widths'];
    $below_widths = $this->configuration['below_widths'];
    $first_above_width = $this->getFirstFromWidths($above_widths);
    $second_above_width = $this->getSecondFromWidths($above_widths);
    $first_below_width = $this->getFirstFromWidths($below_widths);
    $second_below_width = $this->getSecondFromWidths($below_widths);
    $template_name = $this->getPluginDefinition()->getTemplate();

    $build['#attributes']['class'] = [
      'layout',
      $template_name,
      "{$template_name}--above--{$above_widths}",
      "{$template_name}--below--{$below_widths}",
    ];

    if (!empty($build['first_above'])) {
      $build['first_above'] = array_merge_recursive($build['first_above'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$first_above_width}",
          ],
        ],
      ]);
    }

    if (!empty($build['second_above'])) {
      $build['second_above'] = array_merge_recursive($build['second_above'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$second_above_width}",
          ],
        ],
      ]);
    }

    if (!empty($build['first_below'])) {
      $build['first_below'] = array_merge_recursive($build['first_below'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$first_below_width}",
          ],
        ],
      ]);
    }

    if (!empty($build['second_below'])) {
      $build['second_below'] = array_merge_recursive($build['second_below'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$second_below_width}",
          ],
        ],
      ]);
    }

    return $build;
  }

  /**
   * Gets the width options for the configuration form.
   *
   * The first option will be used as the default 'column_widths' configuration
   * value.
   *
   * @return string[]
   *   The width options array where the keys are strings that will be added to
   *   the CSS classes and the values are the human readable labels.
   */
  protected function getWidthOptions() {
    return [
      '50-50' => '50%/50%',
      '33-67' => '33%/67%',
      '67-33' => '67%/33%',
      '25-75' => '25%/75%',
      '75-25' => '75%/25%',
    ];
  }

  /**
   * Gets the first width from a widths value.
   *
   * @param string $widths
   *   A widths value, formatted as stored in the configuration for this layout.
   *
   * @return string
   *   The first width.
   */
  protected function getFirstFromWidths($widths) {
    return substr($widths, 0, 2);
  }

  /**
   * Gets the second width from a widths value.
   *
   * @param string $widths
   *   A widths value, formatted as stored in the configuration for this layout.
   *
   * @return string
   *   The second width.
   */
  protected function getSecondFromWidths($widths) {
    return substr($widths, 3, 2);
  }

  /**
   * Provides a default value for the width options.
   *
   * @return string
   *   A key from the array returned by ::getWidthOptions().
   */
  protected function getDefaultWidth() {
    // Return the first available key from the list of options.
    $width_classes = array_keys($this->getWidthOptions());
    return array_shift($width_classes);
  }

}
