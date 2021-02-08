<?php
namespace Starbug\Operation\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Starbug\Bundle\BundleFactoryInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Operation\OperationInterface;
use Starbug\Operation\OperationFactoryInterface;

class OperationMiddleware implements MiddlewareInterface {
  /**
   * Factory for operations.
   *
   * @var OperationFactoryInterface
   */
  protected $operations;
  /**
   * Factory for state.
   *
   * @var BundleFactoryInterface
   */
  protected $bundles;
  /**
   * Factory for PSR-7 Response
   *
   * @var ResponseFactoryInterface
   */
  protected $response;
  public function __construct(OperationFactoryInterface $operations, BundleFactoryInterface $bundles, ResponseFactoryInterface $response) {
    $this->operations = $operations;
    $this->bundles = $bundles;
    $this->response = $response;
  }
  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    if ($operation = $this->getOperation($request)) {
      $this->configure($operation, $request);
      $state = $this->execute($operation, $request);
      $request = $request->withAttribute("state", $state);
      $route = $request->getAttribute("route");
      if ($operation->success() && $url = $route->getOption("successUrl")) {
        return $this->response->createResponse(302)->withHeader("Location", $url);
      }
    }
    return $handler->handle($request);
  }
  protected function isExecutable(ServerRequestInterface $request) {
    return in_array($request->getMethod(), ["POST", "PUT", "DELETE"]) &&
    $request->getAttribute("route")->hasOperation($request->getMethod());
  }
  protected function getOperation(ServerRequestInterface $request): ?OperationInterface {
    if ($this->isExecutable($request)) {
      return $this->operations->get($request->getAttribute("route")->getOperation($request->getMethod()));
    }
    return null;
  }
  protected function configure(OperationInterface $operation, ServerRequestInterface $request) {
    $arguments = $request->getAttribute("route")->getOptions();
    $operation->configure($arguments);
  }
  protected function execute(OperationInterface $operation, ServerRequestInterface $request): BundleInterface {
    $data = $this->bundles->create($request->getParsedBody());
    $state = $this->bundles->create();
    return $operation->execute($data, $state);
  }
}
