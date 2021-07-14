<?php

namespace Pipa\Dispatch;
use Exception;
use Pipa\Event\EventSource;
use Pipa\Matcher\HasComparableState;

class Dispatch implements HasComparableState {

	public $context;
	public $router;
	public $request;
	public $response;
	public $view;
	public $session;

	public $action;
	public $locale;
	public $result;
	public $security;

	public $events;
	public $exception;

	function __construct(
							Context $context,
							Router $router,
							Request $request,
							Response $response,
							View $view = null,
							Session $session = null
						) {
		$this->context = $context;
		$this->router = $router;
		$this->request = $request;
		$this->response = $response;
		$this->view = $view;
		$this->session = $session ? $session : new PHPSession();

		$this->events = new EventSource($this);
	}

	function getComparableState() {
		$state = array();
		foreach($this as $property=>$value) {
			if ($value instanceof HasComparableState) {
				foreach($value->getComparableState() as $k=>$v) {
					$state["$property:$k"] = $v;
				}
			}
		}
		return $state;
	}

	function run() {
		try {
			$step = "init";
			$this->events->trigger("init", null, true);

			$step = "routing";
			$this->events->trigger("pre-routing");
			$this->action = $this->router->resolve($this);
			$this->events->trigger("post-routing");

			$step = "processing";
			$this->events->trigger("pre-processing");
			$result = $this->action->run($this);

			if ($result instanceof Dispatch) {
				$this->events->trigger("subdispatch");
				$result->run();
			} else {
				$this->result = Result::from($result, $this->action->getOptions());
				$this->events->trigger("post-processing");

				$step = "rendering";
				$this->events->trigger("pre-rendering");
				$this->response->render($this);
				$this->events->trigger("post-rendering");
			}

			$step = "finish";
			$this->events->trigger("finish");

			if ($this->result)
				return $this->result->data;

		} catch(Exception $e) {
			$this->exception = $e;
			$this->events->trigger("{$step}-error");
			$this->events->trigger("error");
		}
	}

	function sub($action, array $data = null) {
		$request = clone $this->request;
		$request->data = $data;

		return new self(
			$this->context,
			new ActionRouter($action),
			$request,
			$this->response,
			$this->view,
			$this->session
		);
	}
}
