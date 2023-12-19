<?php

namespace Zaber04\LumenApiResources\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Arr;

class RouteListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all registered routes.';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Verb', 'Path', 'NamedRoute', 'Controller', 'Action', 'Middleware'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var array
     */
    protected $compactColumns = ['verb', 'path', 'controller', 'action'];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->displayRoutes($this->getRoutes());
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routeCollection = app()->router->getRoutes();
        $rows = [];

        foreach ($routeCollection as $route) {
            $controller = $this->getController($route['action']);

            // Show class name without namesapce
            if ($this->option('compact') && $controller !== 'None') {
                $controller = substr($controller, strrpos($controller, '\\') + 1);
            }

            $rows[] = [
                'verb'       => $route['method'],
                'path'       => $route['uri'],
                'namedRoute' => $this->getNamedRoute($route['action']),
                'controller' => $controller,
                'action'     => $this->getAction($route['action']),
                'middleware' => $this->getMiddleware($route['action']),
            ];
        }

        return $this->pluckColumns($rows);
    }

    /**
     * Get the named route from the action array.
     *
     * @param array $action
     * @return string
     */
    protected function getNamedRoute(array $action)
    {
        return $action['as'] ?? '';
    }

    /**
     * Get the controller from the action array.
     *
     * @param array $action
     * @return string
     */
    protected function getController(array $action)
    {
        return $action['uses'] ? current(explode("@", $action['uses'])) : 'None';
    }

    /**
     * Get the action from the action array.
     *
     * @param array $action
     * @return string
     */
    protected function getAction(array $action)
    {
        if (!empty($action['uses'])) {
            $parts = explode('@', $action['uses']);
            return end($parts) ?: 'METHOD NOT FOUND';
        }

        return 'Closure';
    }

    /**
     * Get the middleware from the action array.
     *
     * @param array $action
     * @return string
     */
    protected function getMiddleware(array $action)
    {
        return isset($action['middleware'])
            ? (is_array($action['middleware']) ? implode(", ", $action['middleware']) : $action['middleware']) : '';
    }

    /**
     * Remove unnecessary columns from the routes.
     *
     * @param  array  $routes
     * @return array
     */
    protected function pluckColumns(array $routes)
    {
        return array_map(function ($route) {
            return Arr::only($route, $this->getColumns());
        }, $routes);
    }

    /**
     * Display the route information on the console.
     *
     * @param  array  $routes
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        if (empty($routes)) {
            return $this->error("Your application doesn't have any routes.");
        }

        $this->table($this->getHeaders(), $routes);
    }

    /**
     * Get the table headers for the visible columns.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return Arr::only($this->headers, array_keys($this->getColumns()));
    }

    /**
     * Get the column names to show (lowercase table headers).
     *
     * @return array
     */
    protected function getColumns()
    {
        $availableColumns = array_map('lcfirst', $this->headers);

        if ($this->option('compact')) {
            return array_intersect($availableColumns, $this->compactColumns);
        }

        if ($columns = $this->option('columns')) {
            return array_intersect($availableColumns, array_map('lcfirst', $columns));
        }

        return $availableColumns;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'columns',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Columns to include in the route table (' . implode(', ', $this->headers) . ')',
            ],
            [
                'compact',
                'c',
                InputOption::VALUE_NONE,
                'Only show verb, path, controller and action columns',
            ],
        ];
    }
}
