<?php
/**
 * Created by PhpStorm.
 * User: imosiev
 * Date: 25.11.13
 * Time: 18:26
 */

namespace Verber\Console;


use Symfony\Component\Console\Application;

class SilexAwareApplication extends Application
{
    /**
     * @var \Silex\Application $silexApp;
     */
    protected $silexApp;

    public function setSilex(\Silex\Application $app)
    {
        $this->silexApp = $app;
        return $this;
    }

    public function getSilex()
    {
        return $this->silexApp;
    }
} 