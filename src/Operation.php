<?php
namespace Starbug\Operation;

use Starbug\Bundle\Bundle;
use Starbug\Bundle\BundleInterface;

abstract class Operation implements OperationInterface {
  /**
   * Track result.
   *
   * @var BundleInterface
   */
  protected $errors;

  public function configure($options = []) {
    // No default implementation.
  }

  /**
   * Handle the operation. Implement this method in child classes.
   *
   * @see OperationInterface::execute
   */
  abstract public function handle(array $data, BundleInterface $state): BundleInterface;

  /**
   * {@inheritdoc}
   */
  public function execute(array $data, $state = null): BundleInterface {
    $state = $state ?? new Bundle();
    if (is_array($state)) {
      $state = new Bundle($state);
    }
    return $this->errors = $this->handle($data, $state);
  }

  public function isExecuted() {
    return $this->errors instanceof BundleInterface;
  }

  public function success() {
    return ($this->isExecuted() && $this->errors->isEmpty());
  }

  public function failure() {
    return ($this->isExecuted() && !$this->errors->isEmpty());
  }
}
