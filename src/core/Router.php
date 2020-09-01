<?php

namespace app\core;

/**
 * Class Router
 */
class Router
{
    public Request $request;
    public Response $response;
    protected array $routers = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param $path
     * @param $callback
     */
    public function get($path, $callback)
    {
        $this->routers['get'][$path] = $callback;
    }

    /**
     * @param $path
     * @param $callback
     */
    public function post($path, $callback)
    {
        $this->routers['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routers[$method][$path] ?? null;

        if ($callback === null)
        {
            $this->response->setStatusCode(404);
            return $this->renderView('not-found');
        }

        if (is_string($callback))
        {
            return $this->renderView($callback);
        }

        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }

        return call_user_func($callback, $this->request);
    }

    public function renderView($view, $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderContent($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        ob_start();
        include_once Application::$ROOT_DIR.'/views/layouts/main.php';
        return ob_get_clean();
    }

    protected function renderContent($viewContent, $params)
    {
        if ($params)
        {
            foreach ($params as $key => $value)
            {
                $$key = $value;
            }
        }
        ob_start();
        include_once Application::$ROOT_DIR."/views/$viewContent.php";
        return ob_get_clean();
    }
}