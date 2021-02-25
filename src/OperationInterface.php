<?php
namespace Starbug\Operation;

use Starbug\Bundle\BundleInterface;

interface OperationInterface {

  public function configure($options = []);

  /**
   * Execute the operation. Call this from consumers.
   *
   * @param array $data The input data for the operation. Typically from a form.
   * @param BundleInterface|array|null $state Initial state, usually carries validation errors.
   *
   * @return void
   */
  public function execute(array $data, $state = null): BundleInterface;

  /**
   * Check if operation has been executed.
   *
   * @return boolean
   */
  public function isExecuted();

  /**
   * Check if operation has been executed successfully.
   * Returns false if operation has not executed.
   *
   * @return boolean
   */
  public function success();

  /**
   * Check if operation has failed.
   * Returns false if operation has not executed.
   *
   * @return boolean
   */
  public function failure();
}
