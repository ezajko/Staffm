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

namespace Pmwebdesign\Staffm\ViewHelpers;

/**
 * Check Deputy Costcenters of employee
 */
class DeputyCostCenterEmployeeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Render
     *   
     * @param \Pmwebdesign\Staffm\Domain\Model\Representation $representation
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $costcenter
     * 
     * @return int 
     */
    public function render(\Pmwebdesign\Staffm\Domain\Model\Representation $representation = NULL, 
            \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $costcenter = NULL)
    {
        $pruefe = 0;
        if ($representation != NULL) {           
            foreach ($representation->getEmployee()->getRepresentations() as $r) {  
                /* @var $r \Pmwebdesign\Staffm\Domain\Model\Representation */
                if ($r === $representation) {
                    foreach($r->getCostcenters() as $costcenterDeputy) {
                        if($costcenterDeputy == $costcenter) {
                            $pruefe = 1;
                        }
                    }                    
                }
            }
        }        
        return $pruefe;
    }
}
