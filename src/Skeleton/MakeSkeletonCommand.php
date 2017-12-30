<?php

namespace CleaniqueCoders\Console\Skeleton;

use CleaniqueCoders\Console\Commander;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSkeletonCommand extends Commander
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('skeleton')
            ->setDescription('Create a new Laravel Package')
            ->addArgument('vendor', InputArgument::REQUIRED)
            ->addArgument('package', InputArgument::REQUIRED)
            ->addArgument('path', InputArgument::OPTIONAL);
    }
    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $vendor    = $input->getArgument('vendor');
        $package   = $input->getArgument('package');
        $path      = $input->getArgument('path') ? $input->getArgument('path') : getcwd();
        $path      = ($path == '.') ? getcwd() : $path;
        $directory = $path . DIRECTORY_SEPARATOR . $this->getDirectoryName($vendor, $package);

        $this->verifyPackageDoesntExist($directory);

        $output->writeln('<info>Creating your Laravel Package Skeleton...</info>');

        /**
         * 1. Copy Stub
         * 2. Update Package Name and Autoload
         * 3. Update Service Provider - File Name, Namespace, Class Name
         */

        /** 1. Copy Stub */
        $stubsDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'stubs';
        $this->filesystem->mirror($stubsDir, $directory);

        /** 2. Update Package Name and Autoload */
        $composerJson = $directory . DIRECTORY_SEPARATOR . 'composer.json';
        file_put_contents($composerJson, str_replace(
            [
                "DummyPackageName",
                "DummyAutoLoad",
            ],
            [
                $this->getQualifiedPackageName($vendor, $package),
                $this->getAutoLoadName($vendor, $package),
            ],
            file_get_contents($composerJson)
        ));

        /** 3. Update Service Provider - File Name, Namespace, Class Name,  */
        $dummyProvider   = $directory . DIRECTORY_SEPARATOR . 'src/PackagerDummyServiceProvider.php';
        $packageProvider = $directory . DIRECTORY_SEPARATOR . 'src/' . $this->getQualifiedClassName($package) . 'ServiceProvider.php';
        $this->filesystem->rename($dummyProvider, $packageProvider, true);

        file_put_contents($packageProvider, str_replace(
            [
                "DummyNamespace",
                "DummyClassName",
            ],
            [
                $this->getQualifiedNamespace($vendor, $package),
                $this->getQualifiedClassName($package),
            ],
            file_get_contents($packageProvider)
        ));

        /** 4. Update TestCase.php */
        $testCase = $directory . DIRECTORY_SEPARATOR . 'tests/TestCase.php';
        file_put_contents($testCase, str_replace(
            [
                "DummyNamespace",
                "DummyClassName",
            ],
            [
                $this->getQualifiedNamespace($vendor, $package),
                $this->getQualifiedClassName($package),
            ],
            file_get_contents($testCase)
        ));

        $output->writeln('<info>Your package directory name: ' . $directory . '</info>');

        /**
         * @todo to make sure the following is automated
         */
        // exec('cd ' . $directory);
        // exec('git init');
        // exec('git add .');
        // exec('git commit -m "initial commits"');
        // exec($this->findComposer() . ' update');

        $output->writeln('<comment>Your Laravel Package Skeleton is ready!</comment>');
    }
}
