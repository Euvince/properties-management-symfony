<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;


class PropertySearch
{
    private ?float $maxPrice = null;

    private ?float $minSurface = null;

    private ?ArrayCollection $specificities = null;

    function __construct()
    {
        $this->specificities = new ArrayCollection();
    }

    /**
     * Get the value of maxPrice
     */ 
    public function getMaxPrice() : ?float
    {
        return $this->maxPrice;
    }

    /**
     * Set the value of maxPrice
     *
     * @return  self
     */ 
    public function setMaxPrice(?float $maxPrice) : ?static
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    /**
     * Get the value of minSurface
     */ 
    public function getMinSurface() : ?float
    {
        return $this->minSurface;
    }

    /**
     * Set the value of minSurface
     *
     * @return  self
     */ 
    public function setMinSurface(?float $minSurface) : ?static
    {
        $this->minSurface = $minSurface;

        return $this;
    }

    /**
     * Get the value of specificities
     */ 
    public function getSpecificities() : ?ArrayCollection
    {
        return $this->specificities;
    }

    /**
     * Set the value of specificities
     *
     * @return  self
     */ 
    public function setSpecificities(?ArrayCollection $specificities) : ?static
    {
        $this->specificities = $specificities;

        return $this;
    }
}