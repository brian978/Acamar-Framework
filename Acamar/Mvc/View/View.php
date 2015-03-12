<?php
/**
 * Acamar-Framework
 *
 * @link https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2014
 * @license https://github.com/brian978/Acamar-Framework/blob/master/LICENSE New BSD License
 */

namespace Acamar\Mvc\View;

/**
 * Class View
 *
 * @method url \Acamar\Mvc\View\Helper\Url
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
     * Identifies the template that will be used to render a layout that will "surround" this view
     *
     * @var string
     */
    protected $layoutTemplate = '';

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
    protected $data = [];

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
    public function __call($method, array $arguments = [])
    {
        $helper = $this->getViewHelperManager()->getHelper($method);

        if (is_callable([$helper, '__invoke'])) {
            return call_user_func_array([$helper, '__invoke'], $arguments);
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
            $this->templatesPath = $this->convertPath($path);

            // We need to make sure that the path does not end with a DIRECTORY_SEPARATOR so it won't
            // cause the getTemplatePath() to fail at finding the template
            $this->templatesPath = trim($this->templatesPath, DIRECTORY_SEPARATOR);
        }

        return $this;
    }

    /**
     * Returns the templates path (this is the path were this object will search for a $this->template)
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->templatesPath;
    }

    /**
     * @param string $template
     * @return View
     */
    public function setLayoutTemplate($template)
    {
        if (null !== $template && !is_string($template)) {
            return $this;
        }

        if (is_string($template)) {
            $template = $this->convertPath(strpos($template, '.phtml') > 0 ? $template : $template . '.phtml');
        }

        $this->layoutTemplate = $template;

        return $this;
    }

    /**
     * Returns the layout template that will be used to render the template
     *
     * @return string
     */
    public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
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
     * Returns the relative path of the template file that will be used by the view
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Concatenates the templates path with the provided template script name, checks if the file exits and then
     * it returns the full template path
     *
     * @throws \RuntimeException
     * @return string
     */
    public function getTemplatePath()
    {
        $templatePath = $this->templatesPath . DIRECTORY_SEPARATOR . $this->template;
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View cannot render `{$this->template}` because the template does not exist");
        }

        return $templatePath;
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

        return trim($string);
    }

    /**
     * The method creates a sandbox for the view script file to run in
     *
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
            require $this->getTemplatePath();

            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Sets a ViewHelperManager object
     *
     * @param \Acamar\Mvc\View\ViewHelperManager $viewHelperManager
     * @return $this
     */
    public function setViewHelperManager(ViewHelperManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;

        return $this;
    }

    /**
     * Returns the ViewHelperManager object that the view uses (if none exists, it will create one)
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
