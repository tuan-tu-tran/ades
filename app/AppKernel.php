<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Security\Core\Util\SecureRandom;

class AppKernel extends Kernel
{
    private $cacheRoot;
    public function __construct($env, $debug, $cacheRoot=NULL)
    {
        $this->cacheRoot=$cacheRoot;
        parent::__construct($env, $debug);
    }

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            //new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            //new Symfony\Bundle\MonologBundle\MonologBundle(),
            //new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            //new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            //new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            //new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new EducAction\AdesBundle\EducActionAdesBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            //$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            //$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $localConfig=__DIR__."/../local/config.yml";
        if(!file_exists($localConfig)){
            $random=new SecureRandom();
            $secret=base64_encode($random->nextBytes(20));
            $config=array(
                "parameters"=>array(
                    "secret"=>$secret
                )
            );
            file_put_contents($localConfig, Yaml::Dump($config));
            chmod($localConfig,0666);
        }
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getCacheDir()
    {
        if($this->cacheRoot) {
            return $this->cacheRoot."/".$this->getEnvironment();
        }else{
            return parent::getCacheDir();
        }
    }
}
