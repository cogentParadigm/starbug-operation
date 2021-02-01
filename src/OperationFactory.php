<?php
namespace Starbug\Operation;

use Psr\Container\ContainerInterface;

class OperationFactory implements OperationFactoryInterface {
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function get($operation): OperationInterface {
    if (is_callable($operation)) {
      return $operation($this->container);
    }
    return $this->container->get($operation);
  }
}
