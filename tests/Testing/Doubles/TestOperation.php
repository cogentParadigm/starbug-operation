<?php
namespace Starbug\Operation\Tests\Testing\Doubles;

use Starbug\Bundle\Bundle;
use Starbug\Bundle\BundleInterface;
use Starbug\Operation\OperationInterface;

/**
 * An `OperationInterface` fixture for trait tests.
 *
 * Records the `data` and `config` it was called with so tests can
 * assert on them. `success()` and `failure()` are configurable via
 * constructor flags.
 */
class TestOperation implements OperationInterface {

  private bool $willSucceed;
  private bool $willFail;
  public array $executedWith = [];
  public array $configuredWith = [];

  public function __construct(bool $willSucceed = true, bool $willFail = false) {
    $this->willSucceed = $willSucceed;
    $this->willFail = $willFail;
  }

  public function configure($options = []) {
    $this->configuredWith = $options;
  }

  public function execute(array $data, $state = null): BundleInterface {
    $this->executedWith = $data;
    return $state ?? new Bundle();
  }

  public function isExecuted() {
    return !empty($this->executedWith);
  }

  public function success() {
    return $this->willSucceed;
  }

  public function failure() {
    return $this->willFail;
  }
}
