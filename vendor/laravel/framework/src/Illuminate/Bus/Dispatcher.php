<?php namespace Illuminate\Bus;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Bus\HandlerResolver;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;

class Dispatcher implements DispatcherContract, QueueingDispatcher, HandlerResolver {

	/**
	 * The container implementation.
	 *
	 * @var \Illuminate\Contracts\Container\Container
	 */
	protected $container;

	/**
	 * The queue resolver callback.
	 *
	 * @var \Closure|null
	 */
	protected $queueResolver;

	/**
	 * All of the command to handler mappings.
	 *
	 * @var array
	 */
	protected $mappings = [];

	/**
	 * The fallback mapping Closure.
	 *
	 * @var \Closure
	 */
	protected $mapper;

	/**
	 * Create a new command dispatcher instance.
	 *
	 * @param  \Illuminate\Contracts\Container\Container  $container
	 * @param  \Closure|null $queueResolver
	 * @return void
	 */
	public function __construct(Container $container, Closure $queueResolver = null)
	{
		$this->container = $container;
		$this->queueResolver = $queueResolver;
	}

	/**
	 * Marshal a command and dispatch it to its appropriate handler.
	 *
	 * @param  mixed  $command
	 * @param  array  $array
	 * @return mixed
	 */
	public function dispatchFromArray($command, array $array)
	{
		return $this->dispatch($this->marshalFromArray($command, $array));
	}

	/**
	 * Marshal a command and dispatch it to its appropriate handler.
	 *
	 * @param  mixed  $command
	 * @param  array  $array
	 * @return mixed
	 */
	public function dispatchFrom($command, ArrayAccess $source, $extras = [])
	{
		return $this->dispatch($this->marshal($command, $source, $extras));
	}

	/**
	 * Marshal a command from the given array.
	 *
	 * @param  string  $command
	 * @param  array  $array
	 * @return mixed
	 */
	protected function marshalFromArray($command, array $array)
	{
		return $this->marshal($command, new Collection, $array);
	}

	/**
	 * Marshal a command from the given array accessible object.
	 *
	 * @param  string  $command
	 * @param  \ArrayAccess  $source
	 * @param  array  $extras
	 * @return mixed
	 */
	protected function marshal($command, ArrayAccess $source, $extras = [])
	{
		$injected = [];

		$reflection = new ReflectionClass($command);

		if ($constructor = $reflection->getConstructor())
		{
			$injected = array_map(function($parameter) use ($command, $source, $extras)
			{
				return $this->getParameterValueForCommand($command, $source, $parameter, $extras);

			}, $constructor->getParameters());
		}

		return $reflection->newInstanceArgs($injected);
	}

	/**
	 * Get a parameter value for a marshalled command.
	 *
	 * @param  string  $command
	 * @param  \ArrayAccess  $source
	 * @param  \ReflectionParameter  $parameter
	 * @param  array  $extras
	 * @return mixed
	 */
	protected function getParameterValueForCommand($command, ArrayAccess $source,
                                                   ReflectionParameter $parameter, array $extras = array())
	{
		$value = $this->extractValueFromExtras($parameter, $extras)
								?: $this->extractValueFromSource($source, $parameter);

		if (is_null($value) && $parameter->isDefaultValueAvailable())
		{
			$value = $parameter->getDefaultValue();
		}
		elseif (is_null($value))
		{
			MarshalException::whileMapping($command, $parameter);
		}

		return $value;
	}

	/**
	 * Attempt to extract the given parameter out of the given array.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @param  array  $extras
	 * @return mixed
	 */
	protected function extractValueFromExtras(ReflectionParameter $parameter, array $extras)
	{
		return array_get($extras, $parameter->name);
	}

	/**
	 * Attempt to extract the given parameter out of the source.
	 *
	 * @param  \ArrayAccess  $source
	 * @param  \ReflectionParameter  $parameter
	 * @return mixed
	 */
	protected function extractValueFromSource(ArrayAccess $source, ReflectionParameter $parameter)
	{
		return array_get($source, $parameter->name);
	}

	/**
	 * Dispatch a command to its appropriate handler.
	 *
	 * @param  mixed  $command
	 * @param  \Closure|null  $afterResolving
	 * @return mixed
	 */
	public function dispatch($command, Closure $afterResolving = null)
	{
		if ($this->queueResolver && $this->commandShouldBeQueued($command))
		{
			return $this->dispatchToQueue($command);
		}
		else
		{
			return $this->dispatchNow($command, $afterResolving);
		}
	}

	/**
	 * Dispatch a command to its appropriate handler in the current process.
	 *
	 * @param  mixed  $command
	 * @param  \Closure|null  $afterResolving
	 * @return mixed
	 */
	public function dispatchNow($command, Closure $afterResolving = null)
	{
		if ($command instanceof SelfHandling)
			return $this->container->call([$command, 'handle']);

		$handler = $this->resolveHandler($command);

		if ($afterResolving)
			call_user_func($afterResolving, $handler);

		return call_user_func(
			[$handler, $this->getHandlerMethod($command)], $command
		);
	}

	/**
	 * Determine if the given command should be queued.
	 *
	 * @param  mixed  $command
	 * @return bool
	 */
	protected function commandShouldBeQueued($command)
	{
		if ($command instanceof ShouldBeQueued) return true;

		return (new ReflectionClass($this->getHandlerClass($command)))->implementsInterface(
			'Illuminate\Contracts\Queue\ShouldBeQueued'
		);
	}

	/**
	 * Dispatch a command to its appropriate handler behind a queue.
	 *
	 * @param  mixed  $command
	 * @return mixed
	 *
	 * @throws \RuntimeException
	 */
	public function dispatchToQueue($command)
	{
		$queue = call_user_func($this->queueResolver);

		if ( ! $queue instanceof Queue)
		{
			throw new \RuntimeException("Queue resolver did not return a Queue implementation.");
		}

		$queue->push($command);
	}

	/**
	 * Get the handler instance for the given command.
	 *
	 * @param  mixed  $command
	 * @return mixed
	 */
	public function resolveHandler($command)
	{
		if ($command instanceof SelfHandling) return $command;

		return $this->container->make($this->getHandlerClass($command));
	}

	/**
	 * Get the handler class for the given command.
	 *
	 * @param  mixed  $command
	 * @return string
	 */
	public function getHandlerClass($command)
	{
		if ($command instanceof SelfHandling) return get_class($command);

		return $this->inflectSegment($command, 0);
	}

	/**
	 * Get the handler method for the given command.
	 *
	 * @param  mixed  $command
	 * @return string
	 */
	public function getHandlerMethod($command)
	{
		if ($command instanceof SelfHandling) return 'handle';

		return $this->inflectSegment($command, 1);
	}

	/**
	 * Get the given handler segment for the given command.
	 *
	 * @param  mixed  $command
	 * @param  int  $segment
	 * @return string
	 */
	protected function inflectSegment($command, $segment)
	{
		$className = get_class($command);

		if (isset($this->mappings[$className]))
		{
			return $this->getMappingSegment($className, $segment);
		}
		elseif ($this->mapper)
		{
			return $this->getMapperSegment($command, $segment);
		}

		throw new \InvalidArgumentException("No handler registered for command [{$className}]");
	}

	/**
	 * Get the given segment from a given class handler.
	 *
	 * @param  string  $className
	 * @param  int  $segment
	 * @return string
	 */
	protected function getMappingSegment($className, $segment)
	{
		return explode('@', $this->mappings[$className])[$segment];
	}

	/**
	 * Get the given segment from a given class handler using the custom mapper.
	 *
	 * @param  mixed  $command
	 * @param  int  $segment
	 * @return string
	 */
	protected function getMapperSegment($command, $segment)
	{
		return explode('@', call_user_func($this->mapper, $command))[$segment];
	}

	/**
	 * Register command to handler mappings.
	 *
	 * @param  array  $commands
	 * @return void
	 */
	public function maps(array $commands)
	{
		$this->mappings = array_merge($this->mappings, $commands);
	}

	/**
	 * Register a fallback mapper callback.
	 *
	 * @param  \Closure  $mapper
	 * @return void
	 */
	public function mapUsing(Closure $mapper)
	{
		$this->mapper = $mapper;
	}

	/**
	 * Map the command to a handler within a given root namespace.
	 *
	 * @param  mixed  $command
	 * @param  string  $commandNamespace
	 * @param  string  $handlerNamespace
	 * @return string
	 */
	public static function simpleMapping($command, $commandNamespace, $handlerNamespace)
	{
		$command = str_replace($commandNamespace, '', get_class($command));

		return $handlerNamespace.'\\'.trim($command, '\\').'Handler@handle';
	}

}
