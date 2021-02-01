<?php
namespace Starbug\Operation;

interface OperationFactoryInterface {
  public function get($operation): OperationInterface;
}
