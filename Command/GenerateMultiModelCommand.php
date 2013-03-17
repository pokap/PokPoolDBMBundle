<?php

namespace Pok\Bundle\DoctrineMultiBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class GenerateMultiModelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pok:doctrine:multi:generate')
            ->setDescription('Generate multi-model')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filesystem = new Filesystem();
        $manager  = $this->getModelManager();
        $metadata = $manager->getMetadataDriverImpl();

        foreach ($metadata->getAllClassNames() as $className) {
            $data       = $this->getElement($metadata->getDrivers(), $className);
            $parameters = $this->buildParameters($data);

            $filename = sprintf('%s%s%s.php', $data['dir'], DIRECTORY_SEPARATOR, $parameters['model_name']);

            if ($filesystem->exists($filename)) {
                // todo implement ask helper confirm
            }

            if (!$filesystem->exists($data['dir'])) {
                $filesystem->mkdir($data['dir']);
            }

            $made = $this->getTimedTwigEngine()->render($this->getTemplate(), $parameters);

            //file_put_contents($filename, $made);
        }
    }

    /**
     * Build parameters with data recovered by the driver.
     *
     * @param array $data
     *
     * @return array
     */
    protected function buildParameters(array $data)
    {
        $occ = strrpos($data['class'], '\\');
        
        return array(
            'model_namespace' => substr($data['class'], 0, $occ),
            'model_name'      => substr($data['class'], $occ + 1),
            'managers'        => array(
                'entity' => array(
                'namespace' => 'test\\test',
                'methods'   => array(
                        array(
                            'type'      => 'setter',
                            'name'      => 'setName',
                            'arguments' => array('$name')
                        )
                    )
                )
            )
        );
    }

    /**
     * @return \Pok\Bundle\DoctrineMultiBundle\ModelManager
     */
    protected function getModelManager()
    {
        return $this->getContainer()->get('pok.doctrine_multi.manager');
    }

    /**
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    protected function getTimedTwigEngine()
    {
        return $this->getContainer()->get('templating');
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getContainer()->getParameter('pok.doctrine_multi.command.view');
    }

    private function getElement(array $drivers, $className)
    {
        foreach ($drivers as $namespace => $driver) {
            if (strpos($className, $namespace) === 0) {
                $result = $this->getMetadata($driver->getElement($className));

                $result['dir'] = $this->getDirectory($driver->getLocator()->getNamespacePrefixes());

                return $result;
            }
        }

        throw new \RuntimeException(sprintf('Invalid drivers with "%s".', $className));
    }

    private function getMetadata(\SimpleXMLElement $xml)
    {
        $result = array();

        $result['class'] = (string) $xml['model'];

        $result['identifier'] = array(
            'manager' => (string) $xml->{'model-reference'}['manager'],
            'field'   => (string) $xml->{'model-reference'}['field']
        );

        foreach ($xml->model as $model) {
            $result['models'][] = (string) $model['class'];
        }

        return $result;
    }

    private function getDirectory(array $prefixes)
    {
        foreach ($prefixes as $dir => $namespace) {
            $dir = substr($dir, 0, strrpos($dir, 'Bundle' . DIRECTORY_SEPARATOR) + 7);

            $namespace = substr($namespace, strrpos($namespace, 'Bundle\\') + 7);

            return $dir . $namespace;
        }

        throw new \RuntimeException('Unknown dir class.');
    }
}
