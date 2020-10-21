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
class TwoColumnLayout extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    return $configuration + [
      'column_widths' => $this->getDefaultWidth(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['column_widths'] = [
      '#type' => 'select',
      '#title' => $this->t('Column widths'),
      '#default_value' => $this->configuration['column_widths'],
      '#options' => $this->getWidthOptions(),
      '#description' => $this->t('Choose the column widths for this layout.'),
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['column_widths'] = $form_state->getValue('column_widths');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);
    $column_widths = $this->configuration['column_widths'];
    $first_column_width = $this->getFirstFromWidths($column_widths);
    $second_column_width = $this->getSecondFromWidths($column_widths);
    $template_name = $this->getPluginDefinition()->getTemplate();

    $build['#attributes']['class'] = [
      'layout',
      $template_name,
      "{$template_name}--{$column_widths}",
    ];

    if (!empty($build['first'])) {
      $build['first'] = array_merge_recursive($build['first'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$first_column_width}",
          ],
        ],
      ]);
    }

    if (!empty($build['second'])) {
      $build['second'] = array_merge_recursive($build['second'], [
        '#attributes' => [
          'class' => [
            "layout__region--col-{$second_column_width}",
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
      // TODO: Change 66 to 67 for better consistency.
      '33-66' => '33%/67%',
      // TODO: Change 66 to 67 for better consistency.
      '66-33' => '67%/33%',
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
