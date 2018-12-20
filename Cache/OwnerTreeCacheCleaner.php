<?php

namespace Pintushi\Bundle\SecurityBundle\Cache;

use Pintushi\Bundle\SecurityBundle\Owner\OwnerTreeProviderInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

class OwnerTreeCacheCleaner implements CacheClearerInterface
{
    /**
     * @var OwnerTreeProviderInterface
     */
    protected $treeProvider;

    /**
     * @param OwnerTreeProviderInterface $treeProvider
     */
    public function __construct(OwnerTreeProviderInterface $treeProvider)
    {
        $this->treeProvider = $treeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($cacheDir)
    {
        $this->treeProvider->clear();
    }
}
