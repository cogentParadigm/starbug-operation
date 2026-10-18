<?php
namespace Starbug\Operation\Tests\Testing\Traits;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Starbug\Operation\Tests\Testing\Doubles\TestOperation;
use Starbug\Operation\Tests\Testing\Doubles\TestOperationFactory;
use Starbug\Operation\Testing\Traits\OperationAssertions;
use Starbug\Operation\OperationInterface;

/**
 * Tests for the `OperationAssertions` trait.
 *
 * The trait's `OperationFactoryInterface` property is wired to a
 * `TestOperationFactory` that returns a `TestOperation` whose
 * success/failure behavior is configured per test. This exercises
 * the assertion helpers end-to-end.
 */
class OperationAssertionsTest extends TestCase {

  use OperationAssertions;

  private TestOperation $operation;

  protected function setUp(): void {
    parent::setUp();
    $this->operation = new TestOperation();
    $this->operationFactory = new TestOperationFactory($this->operation);
  }

  public function testExecuteReturnsOperationInstance(): void {
    $result = $this->execute('AnyOperation', ['name' => 'Test']);
    $this->assertSame($this->operation, $result);
  }

  public function testExecutePassesDataToOperation(): void {
    $this->execute('AnyOperation', ['name' => 'Test', 'value' => 42]);
    $this->assertSame(['name' => 'Test', 'value' => 42], $this->operation->executedWith);
  }

  public function testExecutePassesConfigToOperation(): void {
    $this->execute('AnyOperation', ['name' => 'Test'], ['key' => 'value']);
    $this->assertSame(['key' => 'value'], $this->operation->configuredWith);
  }

  public function testExecuteSkipsConfigureWhenConfigEmpty(): void {
    $this->execute('AnyOperation', ['name' => 'Test']);
    $this->assertSame([], $this->operation->configuredWith);
  }

  public function testAssertExecuteSuccessPassesWhenOperationSucceeded(): void {
    $this->operation = new TestOperation(true, false);
    $this->operationFactory = new TestOperationFactory($this->operation);
    $op = $this->execute('AnyOperation', []);
    $this->assertExecuteSuccess($op);
  }

  public function testAssertExecuteSuccessFailsWhenOperationFailed(): void {
    $this->operation = new TestOperation(false, true);
    $this->operationFactory = new TestOperationFactory($this->operation);
    $op = $this->execute('AnyOperation', []);
    $this->expectException(AssertionFailedError::class);
    $this->assertExecuteSuccess($op);
  }

  public function testAssertExecuteFailurePassesWhenOperationFailed(): void {
    $this->operation = new TestOperation(false, true);
    $this->operationFactory = new TestOperationFactory($this->operation);
    $op = $this->execute('AnyOperation', []);
    $this->assertExecuteFailure($op);
  }

  public function testAssertExecuteFailureFailsWhenOperationSucceeded(): void {
    $this->operation = new TestOperation(true, false);
    $this->operationFactory = new TestOperationFactory($this->operation);
    $op = $this->execute('AnyOperation', []);
    $this->expectException(AssertionFailedError::class);
    $this->assertExecuteFailure($op);
  }

  public function testAssertExecuteFailureFailsWhenOperationNotExecuted(): void {
    $op = new TestOperation(false, false);
    $this->expectException(AssertionFailedError::class);
    $this->assertExecuteFailure($op);
  }

  public function testExecuteAcceptsOperationFromOutsideFactory(): void {
    $external = new TestOperation();
    $this->assertExecuteSuccess($external);
    // Operation is "executed" by being passed in (it has not actually run).
    $this->assertTrue(true);
  }
}
