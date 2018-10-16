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

namespace Pmwebdesign\Staffm\Controller;

use PHPOffice\PhpSpreadsheet\Spreadsheet;
use PHPOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPOffice\PhpSpreadsheet\Writer\Xlsx;
use Pmwebdesign\Staffm\Property\TypeConverter\UploadedFileReferenceConverter;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * KostenstelleController
 */
class KostenstelleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     *
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cache;

    /**
     * kostenstelleRepository
     * 
     * @var \Pmwebdesign\Staffm\Domain\Repository\KostenstelleRepository    
     */
    protected $kostenstelleRepository = NULL;

    /**
     * 
     * @param \Pmwebdesign\Staffm\Domain\Repository\KostenstelleRepository $kostenstelleRepository
     */
    public function injectKostenstelleRepository(\Pmwebdesign\Staffm\Domain\Repository\KostenstelleRepository $kostenstelleRepository)
    {
        $this->kostenstelleRepository = $kostenstelleRepository;
    }
    
    /**
     * Initialize Action
     * Call before other actions
     */
    protected function initializeAction()
    {
        parent::initializeAction();
        
        /* Caching Framework */
        $this->cache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('staffm_mycache');
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $this->view->assign('menuname', 'Kostenstelle');
    }
    
    /**
     * Set TypeConverter option for image upload
     */
    public function initializeCreateAction()
    {
        $this->setTypeConverterConfigurationForImageUpload('newKostenstelle');
    }
    
    /**
     * Set TypeConverter option for image upload
     */
    public function initializeUpdateAction()
    {
        $this->setTypeConverterConfigurationForImageUpload('kostenstelle');
    }
    
    /**
     *
     */
    protected function setTypeConverterConfigurationForImageUpload($argumentName)
    {
        $uploadConfiguration = [
            UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
            UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/kostenstellen/',
        ];
        /** @var PropertyMappingConfiguration $newKostenstelleConfiguration */
        $newKostenstelleConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
//        $newMitarbeiterConfiguration->forProperty('image')
//            ->setTypeConverterOptions(
//                'Pmwebdesign\\Staffm\\Property\\TypeConverter\\UploadedFileReferenceConverter',
//                $uploadConfiguration
//            );
        
        //$newKostenstelleConfiguration->allowAllProperties('images');
        
        // TODO: Does not work, just with 'images.0' -> Problem just the first Picture is set right
//        $newKostenstelleConfiguration->forProperty('images.*')
//                ->setTypeConverterOptions(
//                    'Pmwebdesign\\Staffm\\Property\\TypeConverter\\UploadedFileReferenceConverter',
//                    $uploadConfiguration
//                );        
        for($i= 0; $i < 99; $i++) {
            $newKostenstelleConfiguration->forProperty('images.'.$i)
                ->setTypeConverterOptions(
                    'Pmwebdesign\\Staffm\\Property\\TypeConverter\\UploadedFileReferenceConverter',
                    $uploadConfiguration
                );
        }

    }

    /**
     * action export 
     * Daten in Excel exportieren           
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle 
     * @return void
     */
    public function exportAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle = NULL)
    {
        if ($this->request->hasArgument('searching')) {
            $search = $this->request->getArgument('searching');
        }

        $aktpfad = $_SERVER['DOCUMENT_ROOT'];
        $filePath = $aktpfad . "/uploads/tx_staffm/export.xlsx";

        $limit = 0;

        $_oPHPExcel = new Spreadsheet();
        $_oExcelWriter = new Xlsx($_oPHPExcel);

        // Prüfen ob Kostenstellen, oder Mitarbeiter ausgegeben werden sollen
        if ($kostenstelle == NULL) {
            $kostenstellen = $this->kostenstelleRepository->findSearchForm($search, $limit);

            // Create new Worksheet
            $myWorkSheet = new Worksheet($_oPHPExcel, 'Kostenstellen');
            $_oPHPExcel->addSheet($myWorkSheet, 0);
            $sheetIndex = $_oPHPExcel->getIndex(
                    $_oPHPExcel->getSheetByName('Worksheet')
            );
            // Delete standard worksheet
            $_oPHPExcel->removeSheetByIndex($sheetIndex);

            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, 'Kostenstelle');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 1, 'Bezeichnung');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 1, 'Verantwortlicher');
            //$_oPHPExcel->getSheetByName("Test")->setCellValue('A1', 'Titel');
            for ($i = 0; $i < count($kostenstellen); $i++) {
                $kostenstelle = new \Pmwebdesign\Staffm\Domain\Model\Kostenstelle();
                $kostenstelle = $kostenstellen[$i];
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i + 2, $kostenstelle->getNummer());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i + 2, $kostenstelle->getBezeichnung());
                if ($kostenstelle->getVerantwortlicher() != null) {
                    $vera = $kostenstelle->getVerantwortlicher()->getLastName() . " " . $kostenstelle->getVerantwortlicher()->getFirstName();
                    $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i + 2, $vera);
                }
            }
        } else {
            // Mitarbeiter sollen ausgegeben werden
            $mitarbeiters = $kostenstelle->getMitarbeiters();

            // Create new Worksheet
            $myWorkSheet = new Worksheet($_oPHPExcel, 'Mitarbeiter');
            $_oPHPExcel->addSheet($myWorkSheet, 0);
            $sheetIndex = $_oPHPExcel->getIndex(
                    $_oPHPExcel->getSheetByName('Worksheet')
            );
            // Delete standard worksheet
            $_oPHPExcel->removeSheetByIndex($sheetIndex);

            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Mitarbeiterliste für die Kostenstelle " . $kostenstelle->getNummer() . " " . $kostenstelle->getBezeichnung());

            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 2, "(Kst-Verantwortlicher: " . $kostenstelle->getVerantwortlicher()->getLastName() . " " . $kostenstelle->getVerantwortlicher()->getFirstName() . ")");
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 4, 'Nachname');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 4, 'Vorname');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'PersNr');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 4, 'AD-Name');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 4, 'Titel');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, 4, 'Telefon');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, 4, 'Handy');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, 4, 'Fax');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, 4, 'Email');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, 4, 'Kostenstelle');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, 4, 'KST_Bezeichnung');
            $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, 4, 'Firma');
            //$_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, 4, 'Qualifikation');
            //for ($i = 0; $i < count($mitarbeiters); $i++) { // funktioniert nicht!
            $i = 0;
            foreach ($mitarbeiters as $mitarbeiter) {
                //$mitarbeiter = new \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter();
                //$mitarbeiter = $mitarbeiters[$i];    

                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $i + 5, $mitarbeiter->getLastName());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $i + 5, $mitarbeiter->getFirstName());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $i + 5, (string) $mitarbeiter->getPersonalnummer());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $i + 5, $mitarbeiter->getUsername());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $i + 5, $mitarbeiter->getTitle());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $i + 5, $mitarbeiter->getTelephone());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $i + 5, $mitarbeiter->getHandy());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $i + 5, $mitarbeiter->getFax());
                $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $i + 5, $mitarbeiter->getEmail());
                if ($mitarbeiter->getKostenstelle() != null) {
                    $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $i + 5, $mitarbeiter->getKostenstelle()->getNummer());
                    $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $i + 5, $mitarbeiter->getKostenstelle()->getBezeichnung());
                }
                if ($mitarbeiter->getFirma() != null) {
                    $_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $i + 5, $mitarbeiter->getFirma()->getBezeichnung());
                }
                //$_oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $i + 5, $qualifikation->getBezeichnung());
                $i++;
            }
        }

        // Excel-Datei auf Server sichern		
        $_oExcelWriter->save($filePath);
        unset($_oExcelWriter);
        unset($_oPHPExcel);

        // Speichern unter verfügbar machen für User
        $size = filesize($filePath);
        //header("Content-type: application/octet-stream"); 
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-disposition: attachment; filename=\"export.xlsx\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $size");
        /* header("Pragma: no-cache"); 
          header("Expires: 0"); */
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean(); // Sehr wichtig sonst Fehler bei Excel-Datei
        flush(); // Sehr wichtig sonst Fehler bei Excel-Datei
        readfile($filePath);

        // Excel-Datei auf Server löschen
        unlink($filePath);
    }

    /**
     * action list
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter
     * @return void
     */
    public function listAction(\Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter = NULL)
    {
        // Search word?
        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
            $this->view->assign('search', $search);  
        } else {
            $search = NULL;
        }
        
        // Überprüfen ob Argument gesetzt wurde
        if ($this->request->hasArgument('key')) {
            $key = $this->request->getArgument('key');
            $this->view->assign('key', $key);            
        }
        
        // Auswahl Kst von Kst-Mitarbeiter-Edit
        if ($this->request->hasArgument('kst')) {
            $kst = $this->request->getArgument('kst');
            if ($kst == "kst") {
                $this->view->assign('kst', $kst);
            }
        }
        // No caching?
        if ($this->request->hasArgument('cache')) {
            $cache = $this->request->getArgument('cache');            
        } 
        
        if($key == "auswahl") {
            $limit = 0;
            $kostenstelles = $this->kostenstelleRepository->findSearchForm($search, $limit);
            $this->view->assign('kostenstelles', $kostenstelles);
            $this->view->assign('mitarbeiter', $mitarbeiter);
        } else {
        
            // Clicked char?
            if ($this->request->hasArgument('@widget_0')) {
                $widget = $this->request->getArgument('@widget_0');
                //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($widget);
                $char = $widget["char"];
            }
            $maid = "";
            if ($this->request->hasArgument('maid')) {
                $maid = $this->request->getArgument('maid');
            }
            
            // No cache flag? (Example cost center was deleted and the info message is shown in the list view)
            if ($cache != "notcache") {
                /* Caching Framework */    
                $speak = $GLOBALS['TSFE']->sys_language_uid; // Language Index
                $cachename = $speak."listKstIdentifier";
                $keyforcache = array('list', 'normal');
                // Char or employee id?
                if ($char != "" || $maid == "maid") {
                    // All clicked?
                    if($char == '%') {
                        $search = "";
                        $char = "All";
                        $key = "all";      
                    // Char clicked?
                    } elseif ($char <> '') {
                        $search = "";
                        // No, a other char is clicked
                        $char = $char;                                
                    // Employee id?
                    } elseif ($maid == "maid") {
                        // A employee id was send
                        $char = "All";
                        $key = "all";                
                    }

                    $cachename = $cachename.$char;
                    $keyforcache = array('list', 'buchstabe', $char);
                }

                // Groups of User
                $groups = $this->settings["admingroups"];        
                if($groups == NULL) {
                    $admin = FALSE;
                } else {
                    $userService = GeneralUtility::makeInstance(\Pmwebdesign\Staffm\Domain\Service\UserService::class);
                    // User is admin?
                    $admin = $userService->isAdmin($groups);        
                }

                // Cache of logged in user with admin authorization available?
                if ((($output = $this->cache->get($cachename."Adm")) !== false) && $search == "" && $admin == TRUE) {   
                    // Yes, return Cache
                    return $output;
                }

                // Cache for normal user available?        
                if ((($output = $this->cache->get($cachename)) !== false) && $search == "" && $admin == FALSE) {   
                    // Yes, return Cache
                    return $output;
                }
            }

            $limit = 0;
            $kostenstelles = $this->kostenstelleRepository->findSearchForm($search, $limit);

            

            // Ursprüngliche Suche vorhanden?
            if ($this->request->hasArgument('standardsearch')) {
                $standardsearch = $this->request->getArgument('standardsearch');
            }

            $this->view->assign('standardsearch', $standardsearch);
            $this->view->assign('kostenstelles', $kostenstelles);
            $this->view->assign('mitarbeiter', $mitarbeiter);           
            if ($maid != "") {
                $this->view->assign('maid', $maid);
            }

            // Search exist?
            if ($search <> "" || $cache == "notcache") {
                // Yes, no Cache is needed
                $this->view->assign('cache', '');
            } else {            
                // No, set Cache
                $output = $this->view->render();
                if($admin == TRUE) {
                    $this->cache->set($cachename."Adm", $output, $keyforcache);
                } else {
                    $this->cache->set($cachename, $output, $keyforcache);
                }
                return $output;
            }
        
        }
    }

    /**
     * action show
     * 
     * @param integer $kostenstelle
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter
     * @return void
     */
    public function showAction($kostenstelle, \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter = NULL)
    {
        if ($mitarbeiter != NULL) {
            $key = $this->request->getArgument('key');
            $this->view->assign('key', $key);
            //$kostenstelle = $mitarbeiter->getKostenstelle()->getUid();            
            $this->view->assign('mitarbeiter', $mitarbeiter);
        }
        
        // Get logged in user
        $aktuser = new \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter();
        $aktuser = $this->objectManager->
                get('Pmwebdesign\\Staffm\\Domain\\Repository\\MitarbeiterRepository')->
                findOneByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        // If user is logged in, get cost centers who user is responsible
        if ($aktuser != NULL) {
            $this->view->assign('aktuser', $aktuser);
            // Kostenstellen des angemeldeten Users ermitteln            
            $kostenstellen = $this->objectManager->
                    get('Pmwebdesign\\Staffm\\Domain\\Repository\\KostenstelleRepository')->
                    findByVerantwortlicher($GLOBALS['TSFE']->fe_user->user['uid']);
            $this->view->assign('kostenstellen', $kostenstellen);
        }
        
        $kostenstelle = $this->objectManager->get('Pmwebdesign\\Staffm\\Domain\\Repository\\KostenstelleRepository')->findOneByUid($kostenstelle); 

        // Suchwort?
        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
            $this->view->assign('search', $search);
        }

        // Ursprüngliche Suche?
        if ($this->request->hasArgument('standardsearch')) {
            $standardsearch = $this->request->getArgument('standardsearch');
            $this->view->assign('standardsearch', $standardsearch);
        }

        $this->view->assign('kostenstelle', $kostenstelle);
    }

    /**
     * action choose
     * Ordnet den Mitarbeiter einer Kostenstelle zu
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle
     * @return \Pmwebdesign\Staffm\Domain\Model\Kostenstelle
     */
    public function chooseAction(\Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter, \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle)
    {
        $mitarbeiter->setKostenstelle($kostenstelle);
        $this->objectManager->get('Pmwebdesign\\Staffm\\Domain\\Repository\\MitarbeiterRepository')->update($mitarbeiter);

        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
        }

        // Mitarbeiter aus Kostenstellenliste bearbeitet?
        if ($this->request->hasArgument('kst')) {
            $kst = $this->request->getArgument('kst');
            // Mitarbeiter aus Kostenstellenliste bearbeitet?
            if ($kst == "kst") {
                // TODO: Delete Caches
//                $char = strtoupper(substr($kostenstelle->getBezeichnung(), 0, 1));
//                $this->cache->remove("listKstIdentifier");
//                $this->cache->remove("listKstIdentifierAll");
//                $this->cache->remove("listKstIdentifier".$char);
//                $this->cache->remove("listKstIdentifierAdm");
//                $this->cache->remove("listKstIdentifierAllAdm");
//                $this->cache->remove("listKstIdentifier".$char."Adm");
//                $this->cache->remove("showKstIdentifier".$kostenstelle->getUid());
                
                $this->addFlashMessage('Kostenstellenverantwortlichen zugewiesen!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                $this->forward('editKst', 'Mitarbeiter', NULL, array('ma' => $mitarbeiter,
                    'kst' => $kst, 'kostenstelle' => $kostenstelle));
            } else {
                $this->addFlashMessage('Kostenstelle zugewiesen!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                $this->forward('edit', 'Mitarbeiter', NULL, array('mitarbeiter' => $mitarbeiter, 'search' => $search));
            }
        } else {
            $this->addFlashMessage('Kostenstelle zugewiesen!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
            $this->forward('edit', 'Mitarbeiter', NULL, array('mitarbeiter' => $mitarbeiter, 'search' => $search));
        }
    }

    /**
     * action deleteKst
     * Delete the cost center of an employee
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter	
     */
    public function deleteKstAction(\Pmwebdesign\Staffm\Domain\Model\Mitarbeiter $mitarbeiter)
    {
        $this->addFlashMessage('Die Kostenstelle "'.$mitarbeiter->getKostenstelle()->getNummer().' '.$mitarbeiter->getKostenstelle()->getBezeichnung().'" wurde vom Mitarbeiter '.$mitarbeiter->getFirstName().' '.$mitarbeiter->getLastName().' entfernt!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        if ($this->request->hasArgument('search')) {
            $search = $this->request->getArgument('search');
        }

        $mitarbeiter->setKostenstelle(NULL);
        $this->objectManager->get('Pmwebdesign\\Staffm\\Domain\\Repository\\MitarbeiterRepository')->update($mitarbeiter);        
        $this->redirect('edit', 'Mitarbeiter', NULL, array('mitarbeiter' => $mitarbeiter, 'search' => $search));
    }

    /**
     * action new
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $newKostenstelle
     * @ignorevalidation $newKostenstelle
     * @return void
     */
    public function newAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $newKostenstelle = NULL)
    {
        $this->view->assign('newKostenstelle', $newKostenstelle);
    }

    /**
     * action create
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $newKostenstelle
     * @return void
     */
    public function createAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $newKostenstelle)
    {        
        $this->addFlashMessage('Die Kostenstelle "'.$newKostenstelle->getNummer().' '.$newKostenstelle->getBezeichnung().'" wurde angelegt!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->kostenstelleRepository->add($newKostenstelle); 
        $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
        
        // Delete Caches
        $char = strtoupper(substr($newKostenstelle->getBezeichnung(), 0, 1));
        $this->cache->remove("0listKstIdentifier");
        $this->cache->remove("1listKstIdentifier");
        $this->cache->remove("0listKstIdentifierAll");
        $this->cache->remove("1listKstIdentifierAll");
        $this->cache->remove("0listKstIdentifier".$char);
        $this->cache->remove("1listKstIdentifier".$char);
        $this->cache->remove("0listKstIdentifierAdm");
        $this->cache->remove("1listKstIdentifierAdm");
        $this->cache->remove("0listKstIdentifierAllAdm");
        $this->cache->remove("1listKstIdentifierAllAdm");
        $this->cache->remove("0listKstIdentifier".$char."Adm");
        $this->cache->remove("1listKstIdentifier".$char."Adm");
        
        $this->redirect('edit', 'Kostenstelle', NULL, array('kostenstelle' => $newKostenstelle));
    }

    /**
     * action edit
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle
     * @ignorevalidation $kostenstelle
     * @return void
     */
    public function editAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle)
    {
        $this->view->assign('kostenstelle', $kostenstelle);
    }

    /**
     * action update
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle
     * @return void
     */
    public function updateAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle)
    {
//        $newImagePath = 'kostenstellenbilder';
//        
//        if($this->request->hasArgument('images')) {
//            $images = $this->request->getArgument('images');
//        } else {
//            $images = $this->request->getArguments();
//        }
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($images);
        // TODO: Image handling
//        if ($_FILES['tx_staffm_staffm']['name']['images'][0]) {
//            echo "Fileupload";
//
//            //be careful - you should validate the file type! This is not included here       
//            $tmpName = $_FILES['tx_staffm_staffm']['name']['images'][0];
//            $tmpFile = $_FILES['tx_staffm_staffm']['tmp_name']['images'][0]; 
//            
//            //echo $originalFilePath;
//
//            $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
//            $storage = $storageRepository->findByUid('1'); //this is the fileadmin storage
//            //build the new storage folder
//            //$targetFolder = $storage->createFolder($newImagePath); -> Error, if folder exists
//            $targetFolder = $storage->getFolder($newImagePath);
//
//            //file name, be shure that this is unique
//            $newFileName = $tmpName;
//
//            //build sys_file
//            $movedNewFile = $storage->addFile($tmpFile, $targetFolder, $newFileName);
//            $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
//
//            //now we build the file reference
//            //see private function anotiations!
//            $this->buildRelations($kostenstelle->getUid(), $movedNewFile, 'images', 'tx_staffm_domain_model_kostenstelle', $kostenstelle->getPid());
//            
//        } else {
//            
//            if(!empty($kostenstelle->getImages())){
//                $resourceFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
//                foreach ($kostenstelle->getImages() as $image) {
//                    $fileReferenceObject = $resourceFactory->getFileReferenceObject($image->getUid());
//                    $fileWasDeleted = $fileReferenceObject->getOriginalFile()->delete();
//                }                
//            }
//        }
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($kostenstelle);
        $this->addFlashMessage('Kostenstelle aktualisiert!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->kostenstelleRepository->update($kostenstelle);   
        
        // Delete Caches
        $char = strtoupper(substr($kostenstelle->getBezeichnung(), 0, 1));        
        $this->cache->remove("0listKstIdentifier");
        $this->cache->remove("1listKstIdentifier");
        $this->cache->remove("0listKstIdentifierAll");
        $this->cache->remove("1listKstIdentifierAll");
        $this->cache->remove("0listKstIdentifier".$char);
        $this->cache->remove("1listKstIdentifier".$char);
        $this->cache->remove("0listKstIdentifierAdm");
        $this->cache->remove("1listKstIdentifierAdm");
        $this->cache->remove("0listKstIdentifierAllAdm");
        $this->cache->remove("1listKstIdentifierAllAdm");
        $this->cache->remove("0listKstIdentifier".$char."Adm");
        $this->cache->remove("1listKstIdentifier".$char."Adm");
        
        $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
        //$this->redirect('edit', 'Kostenstelle', NULL, array('kostenstelle' => $kostenstelle, 'tmpfile' => $tmpFile));
        $this->redirect('edit', 'Kostenstelle', NULL, array('kostenstelle' => $kostenstelle));
    }

    /**
     * Build relations for FAL
     * 
     * @param int    $newStorageUid //The UID of the  model
     * @param array  $file //The file model of the image
     * @param string $field //the name of the relation field
     * @param string $table //the table of the model
     */
    private function buildRelations($newStorageUid, $file, $field, $table, $storagePid)
    {
        $data = array();
        $data['sys_file_reference']['NEW1234'] = array(
            'uid_local' => $file->getUid(),
            'uid_foreign' => $newStorageUid, // uid of your content record or own model
            'tablenames' => $table, //tca table name
            'fieldname' => $field, //see tca for fieldname
            'pid' => $storagePid,
            'table_local' => 'sys_file',
        );
        $data[$table][$newStorageUid] = array('image' => 'NEW1234'); //this is needed, i dont know why :( but not stored in tables

        /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
        $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
        $tce->start($data, array());
        $tce->process_datamap();
    }

    /**
     * action delete
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle
     * @return void
     */
    public function deleteAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle)
    {
        // Delete cost center of employees
        foreach ($this->mitarbeiterRepository->findKostenstellenMitarbeiter($kostenstelle) as $m) {
            $m->setKostenstelle(NULL);
            $this->mitarbeiterRepository->update($m);
            // Delete Caches from employee
            $this->cache->remove("0showMitaIdentifier".$m->getUid());
            $this->cache->remove("1showMitaIdentifier".$m->getUid());            
        }
        
        // Delete Caches
        $char = strtoupper(substr($kostenstelle->getBezeichnung(), 0, 1));
        $this->cache->remove("0listKstIdentifier");
        $this->cache->remove("1listKstIdentifier");
        $this->cache->remove("0listKstIdentifierAll");
        $this->cache->remove("1listKstIdentifierAll");
        $this->cache->remove("0listKstIdentifier".$char);
        $this->cache->remove("1listKstIdentifier".$char);
        $this->cache->remove("0listKstIdentifierAdm");
        $this->cache->remove("1listKstIdentifierAdm");
        $this->cache->remove("0listKstIdentifierAllAdm");
        $this->cache->remove("1listKstIdentifierAllAdm");
        $this->cache->remove("0listKstIdentifier".$char."Adm");
        $this->cache->remove("1listKstIdentifier".$char."Adm");

        $this->addFlashMessage('Kostenstelle gelöscht!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->kostenstelleRepository->remove($kostenstelle);
        $this->redirect('list', 'Kostenstelle', NULL, array('cache' => 'notcache'));
    }

    /**
     * action deleteKstVerantwortlicher
     * 
     * @param \Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle
     * @return void
     */
    public function deleteKstVerantwortlicherAction(\Pmwebdesign\Staffm\Domain\Model\Kostenstelle $kostenstelle)
    {
        $kostenstelle->setVerantwortlicher(NULL);
        $this->kostenstelleRepository->update($kostenstelle);
        $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
        
        // Delete Caches
        $char = strtoupper(substr($kostenstelle->getBezeichnung(), 0, 1));
        $this->cache->remove("0listKstIdentifier");
        $this->cache->remove("1listKstIdentifier");
        $this->cache->remove("0listKstIdentifierAll");
        $this->cache->remove("1listKstIdentifierAll");
        $this->cache->remove("0listKstIdentifier".$char);
        $this->cache->remove("1listKstIdentifier".$char);
        $this->cache->remove("0listKstIdentifierAdm");
        $this->cache->remove("1listKstIdentifierAdm");
        $this->cache->remove("0listKstIdentifierAllAdm");
        $this->cache->remove("1listKstIdentifierAllAdm");
        $this->cache->remove("0listKstIdentifier".$char."Adm");
        $this->cache->remove("1listKstIdentifier".$char."Adm");
        $this->cache->remove("0showKstIdentifier".$kostenstelle->getUid());
        $this->cache->remove("1showKstIdentifier".$kostenstelle->getUid());

        $this->redirect('edit', 'Kostenstelle', NULL, array('kostenstelle' => $kostenstelle));
    }

}
