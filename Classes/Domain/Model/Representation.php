<?php

/*
 * Copyright (C) 2018 pm-webdesign.eu 
 * Markus Puffer <m.puffer@pm-webdesign.eu>
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace Pmwebdesign\Staffm\Domain\Model;

/**
 * Representation for employees
 *
 * @author Markus Puffer <m.puffer@pm-webdesign.eu>
 */
class Representation extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Person to be represented
     *
     * @var \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter 
     */
    protected $employee = NULL;
    
    /**
     * Employee who represents
     *
     * @var \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter 
     */
    protected $deputy = NULL;


    /**
     * Excluded cost centers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pmwebdesign\Staffm\Domain\Model\Kostenstelle> 
     */
    protected $costcenters = NULL;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }
    
    /**
     * Initialize Object Storages
     * 
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->costcenters = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }
    
    /**
     * Get employee
     * 
     * @return \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter
     */
    public function getEmployee(): \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter
    {
        return $this->employee;
    }

    /**
     * Set employee
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $employee
     */
    public function setEmployee(\Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $employee)
    {
        $this->employee = $employee;
    }
    
    /**
     * Get employee
     * 
     * @return \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter
     */
    public function getDeputy(): \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter
    {
        return $this->deputy;
    }

    /**
     * Set employee
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $employee
     */
    public function setDeputy(\Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $deputy)
    {
        $this->deputy = $deputy;
    }

    /**
     * Get cost centers
     * 
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pmwebdesign\Staffm\Domain\Model\Kostenstelle>
     */
    public function getCostcenters()
    {
        return $this->costcenters;
    }
    
    /**
     * Set cost centers
     * 
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pmwebdesign\Staffm\Domain\Model\Kostenstelle> $costcenters
     */
    public function setCostcenters(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $costcenters)
    {
        $this->costcenters = $costcenters;
    }
}
