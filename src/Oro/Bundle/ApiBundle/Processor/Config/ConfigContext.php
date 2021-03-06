<?php

namespace Oro\Bundle\ApiBundle\Processor\Config;

use Oro\Bundle\ApiBundle\Config\ConfigExtraInterface;
use Oro\Bundle\ApiBundle\Config\FiltersConfigExtra;
use Oro\Bundle\ApiBundle\Config\SortersConfigExtra;
use Oro\Bundle\ApiBundle\Processor\ApiContext;

class ConfigContext extends ApiContext
{
    /** FQCN of an entity */
    const CLASS_NAME = 'class';

    /** the maximum number of related entities that can be retrieved */
    const MAX_RELATED_ENTITIES = 'maxRelatedEntities';

    /** a list of additional configuration data that should be retrieved */
    const EXTRA = 'extra';

    /** @var ConfigExtraInterface[] */
    protected $extras = [];

    public function __construct()
    {
        $this->set(self::EXTRA, []);
    }

    /**
     * Gets FQCN of an entity.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->get(self::CLASS_NAME);
    }

    /**
     * Sets FQCN of an entity.
     *
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->set(self::CLASS_NAME, $className);
    }

    /**
     * Gets the maximum number of related entities that can be retrieved
     *
     * @return int|null
     */
    public function getMaxRelatedEntities()
    {
        return $this->get(self::MAX_RELATED_ENTITIES);
    }

    /**
     * Sets the maximum number of related entities that can be retrieved
     *
     * @param int $limit
     */
    public function setMaxRelatedEntities($limit)
    {
        $this->set(self::MAX_RELATED_ENTITIES, $limit);
    }

    /**
     * Checks if the specified additional configuration data is requested.
     *
     * @param string $extraName
     *
     * @return bool
     */
    public function hasExtra($extraName)
    {
        return in_array($extraName, $this->get(self::EXTRA), true);
    }

    /**
     * Gets a list of requested additional configuration data.
     *
     * @return ConfigExtraInterface[]
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Sets additional configuration data that you need.
     *
     * @param ConfigExtraInterface[] $extras
     *
     * @throws \InvalidArgumentException if $extras has invalid elements
     */
    public function setExtras(array $extras)
    {
        $names = [];
        foreach ($extras as $extra) {
            if (!$extra instanceof ConfigExtraInterface) {
                throw new \InvalidArgumentException(
                    'Expected an array of "Oro\Bundle\ApiBundle\Config\ConfigExtraInterface".'
                );
            }
            $names[] = $extra->getName();
            $extra->configureContext($this);
        }

        $this->extras = $extras;
        $this->set(self::EXTRA, $names);
    }

    /**
     * Checks whether a definition of filters exists.
     *
     * @return bool
     */
    public function hasFilters()
    {
        return $this->has(FiltersConfigExtra::NAME);
    }

    /**
     * Gets a definition of filters.
     *
     * @return array|null
     */
    public function getFilters()
    {
        return $this->get(FiltersConfigExtra::NAME);
    }

    /**
     * Sets a definition of filters.
     *
     * @param array|null $filters
     */
    public function setFilters($filters)
    {
        $this->set(FiltersConfigExtra::NAME, $filters);
    }

    /**
     * Checks whether a definition of sorters exists.
     *
     * @return bool
     */
    public function hasSorters()
    {
        return $this->has(SortersConfigExtra::NAME);
    }

    /**
     * Gets a definition of sorters.
     *
     * @return array|null
     */
    public function getSorters()
    {
        return $this->get(SortersConfigExtra::NAME);
    }

    /**
     * Sets a definition of sorters.
     *
     * @param array|null $sorters
     */
    public function setSorters($sorters)
    {
        $this->set(SortersConfigExtra::NAME, $sorters);
    }
}
