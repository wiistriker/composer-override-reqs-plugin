<?php

namespace Wiistriker;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackage;
use Composer\Package\Link;
use Composer\Package\Version\VersionParser;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PrePoolCreateEvent;
use ReflectionObject;

class OverridePlugin implements PluginInterface, EventSubscriberInterface
{
    private VersionParser $versionParser;
    private array $overrides = [];

    public function __construct()
    {
        $this->versionParser = new VersionParser();
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $extra = $composer->getPackage()->getExtra();
        if (isset($extra['reqs-override'])) {
            $this->overrides = $extra['reqs-override']['overrides'];
        }
    }

    public function deactivate(Composer $composer, IOInterface $io) {}
    public function uninstall(Composer $composer, IOInterface $io) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'pre-pool-create' => 'onPrePoolCreate',
        ];
    }

    public function onPrePoolCreate(PrePoolCreateEvent $event): void
    {
        /** @var CompletePackage[] $packages */
        $packages = $event->getPackages();

        foreach ($packages as $package) {
            if (!isset($this->overrides[$package->getName()])) {
                continue;
            }

            $current_package_override = $this->overrides[$package->getName()];
            foreach ($current_package_override as $current_package_override_package_version => $current_package_overrides) {
                if (!str_starts_with($package->getVersion(), $current_package_override_package_version)) {
                    continue;
                }

                $requires = $package->getRequires();

                foreach ($current_package_overrides as $override_package_name => $override_package_version) {
                    /** @var Link $existedLink */
                    $existedLink = $requires[$override_package_name];

                    $existedLinkReflection = new ReflectionObject($existedLink);
                    $constraintProperty = $existedLinkReflection->getProperty('constraint');
                    $constraintProperty->setAccessible(true);
                    $constraintProperty->setValue($existedLink, $this->versionParser->parseConstraints($override_package_version));

                    $prettyConstraintProperty = $existedLinkReflection->getProperty('prettyConstraint');
                    $prettyConstraintProperty->setAccessible(true);
                    $prettyConstraintProperty->setValue($existedLink, $override_package_version);
                }
            }
        }
    }
}
