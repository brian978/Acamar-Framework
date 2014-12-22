<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace Acamar\Mvc\View;

/**
 * Class View
 *
 * @package Acamar\Mvc\View
 */
class View
{
    /**
     * Identifies the parent of this view
     *
     * @var View
     */
    protected $parent = null;

    /**
     * @var string
     */
    protected $templatesPath = '';

    /**
     * Identifies the template that will be used to render this view
     *
     * @var string
     */
    protected $template = '';

    /**
     * Variables that will be used when rendering the view
     *
     * @var array
     */
    protected $data = array();

    /**
     * @var ViewHelperManager
     */
    protected $viewHelperManager = null;

    /**
     * Magic get method (this is used by the view scripts)
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = array())
    {
        $helper = $this->getViewHelperManager()->getHelper($method);

        if (is_callable(array($helper, '__invoke'))) {
            return call_user_func_array(array($helper, '__invoke'), $arguments);
        }

        return $helper;
    }

    /**
     * Sets a new variable in the data array
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        if (is_string($name)) {
            $this->data[$name] = $value;
        }

        return $this;
    }

    /**
     * Returns a variable that is set on the view (if it exists)
     *
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return $default;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @param \Acamar\Mvc\View\View $parent
     * @return $this
     */
    public function setParent(View $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \Acamar\Mvc\View\View
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the path from where the templates will be rendered
     *
     * @param string $path
     * @return $this
     */
    public function setTemplatesPath($path)
    {
        if (is_string($path)) {
            $this->templatesPath = $this->convertPath(trim($path));
        }

        return $this;
    }

    /**
     * Returns the templates path
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->templatesPath;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $this->convertPath(strpos($template, '.phtml') > 0 ? $template : $template . '.phtml');

        return $this;
    }

    /**
     * @param string $template
     * @return string
     * @throws \RuntimeException
     */
    protected function resolveTemplatePath($template)
    {
        $templatePathname = $this->templatesPath . DIRECTORY_SEPARATOR . trim($template);
        if (!file_exists($templatePathname)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        return $templatePathname;
    }

    /**
     * Converts the slashes in the given string to the ones specific to the platform that the framework runs on
     *
     * @param string $string
     * @return string
     */
    protected function convertPath($string)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $string = str_replace('/', DIRECTORY_SEPARATOR, $string);
        } else {
            $string = str_replace('\\', DIRECTORY_SEPARATOR, $string);
        }

        return $string;
    }

    /**
     * Using only a require in this method prevents the view from accessing anything we do not want from the render
     * method
     *
     * @throws \Exception
     * @return string
     */
    public function getContents()
    {
        ob_start();

        try {
            require $this->resolveTemplatePath($this->template);

            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * @param \Acamar\Mvc\View\ViewHelperManager $viewHelperManager
     * @return $this
     */
    public function setViewHelperManager(ViewHelperManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;

        return $this;
    }

    /**
     *
     * @return ViewHelperManager
     */
    public function getViewHelperManager()
    {
        if ($this->viewHelperManager === null) {
            $this->viewHelperManager = new ViewHelperManager();
        }

        return $this->viewHelperManager;
    }
}
