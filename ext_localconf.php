<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Pmwebdesign.' . $_EXTKEY,
	'Staffm',   // Plugin
	[ // Cacheable actions
		'Mitarbeiter' => 'list, listChoose, listChooseQuali, choose, show, new, edit, editUser, create, editKst, update, delete, deleteQuali, deleteCategories, showKst, showVeraKst, deleteImage, export',
		'Position' => 'list, show, choose, export, new, edit, create, update, delete, deletePosition',
		'Kostenstelle' => 'list, show, choose, export, new, edit, create, update, delete, deleteKst, deleteKstVerantwortlicher',
		'Firma' => 'list, show, choose, export, new, edit, create, update, delete, deleteFirma',				
		'Qualifikation' => 'list, show, choose, edit, new, create, update, delete, export',
		'Qualilog' => 'list, show, choose, edit, new, create, update, delete',
                'Category' => 'list, show, new, edit, create, update, delete'
	],	
	[ // Non-cacheable actions
		'Mitarbeiter' => 'list, listChoose, listChooseQuali, choose, show, new, edit, editUser, create, editKst, update, delete, deleteQuali, deleteCategories, deleteImage, export',
		'Position' => 'list, show, choose, export, new, edit, create, update, delete, deletePosition',
		'Kostenstelle' => 'list, show, choose, export, new, edit, create, update, delete, deleteKst, deleteKstVerantwortlicher',
		'Firma' => 'list, show, choose, export, new, edit, create, update, delete, deleteFirma',
		'Standort' => 'list, show, choose, new, edit, create, update, delete, deleteStandort',		
		'Qualifikation' => 'list, show, choose, create, update, delete',
		'Qualilog' => 'choose, create, update, delete',
                'Category' => 'list, show, new, edit, create, update, delete'
	]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Pmwebdesign.' . $_EXTKEY,
	'Staffmvorg',
	[ // Cacheable actions
		'Mitarbeiter' => 'listVgs, show, edit, editKst, update, showKst, showVeraKst, deleteImage, deleteQuali, export',
                'Position' => 'list, show, choose',
		'Kostenstelle' => 'list, show, choose, export',
		'Firma' => 'list, show, choose, export',
		'Qualifikation' => 'list, listVgs, show, choose, chooselist, edit, new, create, update, delete, export',
		'Qualilog' => 'list, show, choose, edit, new, create, update, delete',		
                'Category' => 'list, show, new, edit, create, update, delete'
	],	
	[ // Non-cacheable actions
		'Mitarbeiter' => 'listVgs, show, edit, editKst, update, showKst, showVeraKst, deleteImage, deleteQuali, export',	
                'Position' => 'list, show, choose',
		'Kostenstelle' => 'list, show, choose, export',
		'Firma' => 'list, show, choose, export',
		'Qualifikation' => 'choose, chooselist, create, update, delete',
		'Qualilog' => 'choose, create, update, delete',		
                'Category' => 'list, show, new, edit, create, update, delete'
	]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Pmwebdesign.' . $_EXTKEY,
	'Staffmcustom',
	[ // Cacheable actions
		'Mitarbeiter' => 'listCustom, showCustom',             
	],	
	[ // Non-cacheable actions
		'Mitarbeiter' => 'listCustom, showCustom',
	]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Pmwebdesign\\Staffm\\Property\\TypeConverter\\UploadedFileReferenceConverter');
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('Pmwebdesign\\Staffm\\Property\\TypeConverter\\ObjectStorageConverter');

// Show Sheduler Task
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['TYPO3\Staffm\Task\Mitarbeitersynch'] = array (
//	'extension' => $_EXTKEY,
//	'title' => 'Mitarbeiter-Synchronisation',
//	'description' => 'Synchronisiert fehlende Daten zu Mitarbeitern',
//);

/**
 * Add cache configuration
 */
if( !is_array( $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache'] ) ) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache'] = array();
}
if( !isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['frontend'] ) ) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend';
}
//if( !isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['frontend'] ) ) {
//    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\StringFrontend';
//}
// When backend active -> cache isn´t save in cf_staffm_mycache
//if( !isset($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['staffm_mycache']['backend'] ) ) {
//    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['staffm_mycache']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend';
//}
if( !isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['options'] ) ) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['options'] = array( 'defaultLifetime' => 0 );
}
if( !isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['groups'] ) ) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['staffm_mycache']['groups'] = array( 'pages' );
}