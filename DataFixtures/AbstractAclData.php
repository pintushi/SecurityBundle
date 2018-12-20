<?php

namespace Pintushi\Bundle\SecurityBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class provides functional for loading default Role permissions
 */
abstract class AbstractAclData extends Fixture implements
    DependentFixtureInterface
{
    private $aclConfigurator;
    private $kernel;

    public function __construct(AclConfigurator $aclConfigurator, KernelInterface $kernel)
    {
        $this->aclConfigurator = $aclConfigurator;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Pintushi\Bundle\UserBundle\DataFixtures\RoleFixture',
        ];
    }

    /**
     * Load roles default acls
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $aclData = $this->getAclData();

        $this->aclConfigurator->process($aclData);
    }

    /**
     * Returns ACL data as array
     *
     * Yaml File Example:
     *
     *     ROLE_NAME:
     *         role: role NAME
     *         label: Role Label
     *         permissions:
     *             entity|Some\Bundle\Entity\Name: [VIEW_SYSTEM, CREATE_SYSTEM, ...]
     *             action|some_acl_capability: [EXECUTE]
     *
     *
     * @return array
     */
    protected function getAclData()
    {
        $fileName = $this->kernel
            ->locateResource($this->getDataPath());
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);

        return Yaml::parse(file_get_contents($fileName));
    }

    /**
     * Gets path to load data from.
     *
     * @return string
     */
    abstract protected function getDataPath();
}
