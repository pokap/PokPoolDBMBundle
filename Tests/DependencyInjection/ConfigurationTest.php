<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

use Pok\Bundle\PoolDBMBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $processor = new Processor();
        $configuration = new Configuration(false);
        $options = $processor->processConfiguration($configuration, array());

        $defaults = array(
            'managers'     => array(),
            'auto_mapping' => false,
            'mappings'     => array(),
        );

        foreach ($defaults as $key => $default) {
            $this->assertTrue(array_key_exists($key, $options), sprintf('The default "%s" exists', $key));
            $this->assertEquals($default, $options[$key]);

            unset($options[$key]);
        }

        if (count($options)) {
            $this->fail('Extra defaults were returned: '. print_r($options, true));
        }
    }

    /**
     * Tests a full configuration.
     *
     * @dataProvider fullConfigurationProvider
     */
    public function testFullConfiguration($config)
    {
        $processor = new Processor();
        $configuration = new Configuration(false);
        $options = $processor->processConfiguration($configuration, array($config));

        $expected = array(
            'managers'     => array(
                'foo' => array('id' => 'bar.service')
            ),
            'auto_mapping' => true,
            'mappings' => array(
                'FooBundle' => array(
                    'type'    => 'xml',
                    'mapping' => true,
                ),
                'BarBundle' => array(
                    'type'      => 'xml',
                    'dir'       => '%kernel.cache_dir%',
                    'prefix'    => 'prefix_val',
                    'alias'     => 'alias_val',
                    'is_bundle' => false,
                    'mapping'   => true,
                )
            )
        );

        $this->assertEquals($expected, $options);
    }

    public function fullConfigurationProvider()
    {
       $yaml = Yaml::parse(__DIR__.'/Fixtures/config/yaml/full.yml');
       $yaml = $yaml['pok_pool_dbm'];

       return array(
           array($yaml),
       );
    }
}
