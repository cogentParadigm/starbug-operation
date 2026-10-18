<?php
namespace Starbug\Operation\Tests\Testing\Doubles;

use Starbug\Operation\OperationFactoryInterface;
use Starbug\Operation\OperationInterface;

/**
 * An `OperationFactoryInterface` fixture that returns a configured
 * `TestOperation` regardless of the requested class name.
 */
class TestOperationFactory implements OperationFactoryInterface {

  private OperationInterface $operation;

  public function __construct(OperationInterface $operation) {
    $this->operation = $operation;
  }

  public function get($operation): OperationInterface {
    return $this->operation;
  }
}
