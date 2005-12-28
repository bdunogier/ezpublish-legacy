<?php
//
// Definition of Toolbar class
//
// Created on: <05-Mar-2004 12:36:14 wy>
//
// Copyright (C) 1999-2005 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file toolbar.php
*/

$http =& eZHTTPTool::instance();
$module =& $Params["Module"];
if ( $Params['SiteAccess'] )
    $currentSiteAccess = $Params['SiteAccess'];
if ( $Params['Position'] )
    $toolbarPosition =& $Params['Position'];


include_once( "kernel/common/template.php" );
include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'kernel/classes/ezcontentbrowse.php' );

$http =& eZHTTPTool::instance();

$siteini =& eZINI::instance();
if ( !in_array( $currentSiteAccess, $siteini->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) )
    return $module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );

$iniPath = "settings/siteaccess/$currentSiteAccess";
$ini =& eZINI::instance( "toolbar.ini", 'settings', null, false, null, false );

$iniAppend =& eZINI::instance( 'toolbar.ini.append', $iniPath, null, false, null, true );

$toolArray = array();
if ( $iniAppend->hasVariable( "Toolbar_" . $toolbarPosition, "Tool" ) )
{
    $toolArray =  $iniAppend->variable( "Toolbar_" . $toolbarPosition, "Tool" );
}
else if ( $ini->hasVariable( "Toolbar_" . $toolbarPosition, "Tool" ) )
{
    $toolArray =  $ini->variable( "Toolbar_" . $toolbarPosition, "Tool" );
}

$storeList = false;
$removeCache = false;
$checkCurrAction = false;   //Checks if it is needed to clear $parameterValue

$currentAction = $module->currentAction();

if ( $currentAction == 'BackToToolbars' )
{
    return $Module->redirectToView( 'toolbarlist', array() );
}
elseif ( $currentAction == 'SelectToolbarNode' )
{
    $selectedNodeIDArray = eZContentBrowse::result( 'SelectToolbarNode' );

    $nodeID = $selectedNodeIDArray[0];
    if ( is_numeric( $nodeID ) )
    {
        $toolIndex = $http->variable( 'tool_index' );
        $parameterName = $http->variable( 'parameter_name' );

        $iniAppend->setVariable( "Tool_" . $toolbarPosition . "_" . $toolArray[$toolIndex] . "_" . ( $toolIndex + 1 ), $parameterName, $nodeID );
        $succeed = $iniAppend->save(  false, false, false, false, true, true );
    }
}
elseif ( $currentAction == 'SelectToolbarNodePath' )
{
    $selectedNodeIDArray = eZContentBrowse::result( 'SelectToolbarNode' );

    $nodeID = $selectedNodeIDArray[0];
    if  ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
    {
        if ( !is_numeric( $nodeID ) )
           $nodeID = 2;
        $toolIndex = $http->variable( 'tool_index' );
        $parameterName = $http->variable( 'parameter_name' );
        $iniAppend->setVariable( "Tool_" . $toolbarPosition . "_" . $toolArray[$toolIndex] . "_" . ( $toolIndex + 1 ), $parameterName, $nodeID );
        $succeed = $iniAppend->save(  false, false, false, false, true, true );
    }
}
elseif ( $currentAction == 'NewTool' ||
         $currentAction == 'UpdatePlacement' ||
         $currentAction == 'Browse' ||
         $currentAction == 'Remove' )
{
    $deleteToolArray = array();
    if ( $currentAction == 'Remove' &&
         $http->hasPostVariable( 'deleteToolArray' ) )
    {
        $deleteToolArray = $http->postVariable( 'deleteToolArray' );
    }

    $updatedToolArray = array();
    $existingToolArray = $toolArray;
    $deleteToolKeys = array_keys( $deleteToolArray );

    $positionMap = array();
    $updatedBlockArray = array();
    for ( $originalIndex = 0; $originalIndex < count( $existingToolArray ); ++$originalIndex )
    {
        $originalPlacement = $originalIndex + 1;
        if ( in_array( $originalIndex, $deleteToolArray ) )
        {
            continue;
        }
        if ( $http->hasPostVariable( 'placement_' . $originalIndex ) )
        {
            $newIndex = $http->postVariable( 'placement_' . $originalIndex );
            if ( isset( $positionMap[$newIndex] ) )
            {
                $newPositionMap = array();
                foreach ( $positionMap as $positionIndex => $positionOriginalIndex )
                {
                    if ( $positionIndex > $newIndex )
                        ++$positionIndex;
                    $newPositionMap[$positionIndex] = $positionOriginalIndex;
                }
                $positionMap = $newPositionMap;
                ++$newIndex;
            }
            $positionMap[$newIndex] = $originalIndex;
        }
    }
    ksort( $positionMap );
    reset( $positionMap );
    $newIndex =0 ;
    foreach ( $positionMap as $originalIndex )
    {
        $originalPlacement = $originalIndex + 1;
        $newPlacement = $newIndex + 1;
        $toolName = $existingToolArray[$originalIndex];
        $updatedToolArray[$newIndex] = $toolName;
        if ( $iniAppend->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $originalPlacement ) )
        {
            $actionParameters = $iniAppend->group( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $originalPlacement );

            $updatedBlockArray[] = array( 'blockName' => "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $newPlacement,
                                          'parameters' => $actionParameters );
            $iniAppend->removeGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $originalPlacement );
        }
        ++$newIndex;
    }
    foreach ( $deleteToolArray as $deleteToolIndex )
    {
        $deleteToolPlacement = $deleteToolIndex + 1;
        $toolName = $existingToolArray[$deleteToolIndex];
        if ( $iniAppend->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $deleteToolPlacement ) )
        {
            $iniAppend->removeGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . $deleteToolPlacement );
        }
    }

    if ( $currentAction == 'NewTool' &&
         $http->hasPostVariable( 'toolName' ) )
    {
        $addedToolName = $http->postVariable( 'toolName' );
        array_push( $updatedToolArray, $addedToolName );
        $updatedToolArray = array_unique( $updatedToolArray );
    }

    foreach ( $updatedBlockArray as $updatedBlock )
    {
        $blockName = $updatedBlock['blockName'];
        $parameters = $updatedBlock['parameters'];
        foreach ( array_keys( $parameters ) as $key )
        {
            $parameterValue = $parameters[$key];
            $iniAppend->setVariable( $blockName, $key, $parameterValue );
        }
    }

    $iniAppend->setVariable( "Toolbar_" . $toolbarPosition, "Tool", $updatedToolArray );
    $succeed = $iniAppend->save( false, false, false, false, true, true );

    if ( $currentAction == 'Browse' )
    {
        $browseArray = $http->postVariable( 'BrowseButton' );
        if ( preg_match( "/_node$/", key( $browseArray ) ) )
        {
            if ( preg_match( "/(.+)_parameter_(.+)/", key( $browseArray ), $res ) )
            {
                eZContentBrowse::browse( array( 'action_name' => 'SelectToolbarNode',
                                                'description_template' => false,
                                                'persistent_data' => array( 'tool_index' => $res[1], 'parameter_name' => $res[2] ),
                                                'from_page' => "/visual/toolbar/$currentSiteAccess/$toolbarPosition/" ),
                                         $module );
                removeRelatedCache( $currentSiteAccess );
                return;
            }

        }
        else if ( preg_match( "/_subtree$/", key( $browseArray ) ) )
        {
            if ( preg_match( "/(.+)_parameter_(.+)/", key( $browseArray ), $res ) )
            {
                eZContentBrowse::browse( array( 'action_name' => 'SelectToolbarNodePath',
                                                'description_template' => false,
                                                'persistent_data' => array( 'tool_index' => $res[1], 'parameter_name' => $res[2] ),
                                                'from_page' => "/visual/toolbar/$currentSiteAccess/$toolbarPosition/" ),
                                         $module );
                removeRelatedCache( $currentSiteAccess );
                return;
            }
        }
    }
    $toolArray = $updatedToolArray;
    $removeCache = true;
}
elseif ( $currentAction == 'Store' )
{
    $storeList = true;
    $removeCache = true;
    $checkCurrAction = true;
}

$toolList = array();
foreach ( array_keys( $toolArray ) as $toolKey )
{
    unset( $actionParameters );
    $actionParameters = array();
    $defaultActionParameters = array();
    $actionDescription = false;
    $toolName = $toolArray[$toolKey];
    if ( $ini->hasGroup( "Tool_" . $toolName ) )
    {
        $defaultActionParameters = $ini->group( "Tool_" . $toolName );
    }
    if ( $ini->hasGroup( "Tool_" . $toolName . '_description' ) )
    {
        $actionDescription = $ini->group( "Tool_" . $toolName . '_description' );
    }
    /* if ( $ini->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) )
    {
        $defaultActionParameters = array_merge( $defaultActionParameters, $ini->group( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) );
    }*/

    if ( $iniAppend->hasGroup( "Tool_" . $toolName ) )
    {
        $actionParameters = array_merge( $actionParameters, $iniAppend->group( "Tool_" . $toolName ) );
    }
    if ( $iniAppend->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) )
    {
        $actionParameters = array_merge( $actionParameters, $iniAppend->group( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) );
    }
    $actionParameters = array_merge( $defaultActionParameters, $actionParameters );
    if ( !$actionDescription )
    {
        $actionDescription = array();
        foreach ( $actionParameters as $actionParameterKey => $actionParameter )
        {
            $actionDescription[$actionParameterKey] = $actionParameterKey;
        }
    }
    $removeNewBlock = true;
    $newActionParameters = array();
    $toolParameters = array();
    $customInputList = array();
    if ( $http->hasPostVariable( 'CustomInputList' ) )
        $customInputList = $http->postVariable( 'CustomInputList' );
    foreach ( array_keys( $actionParameters ) as $key )
    {
        $oldParameterValue = false;
        if ( isset( $defaultActionParameters[$key] ) )
            $oldParameterValue = $defaultActionParameters[$key];

        $parameterValue = $actionParameters[$key];

        if ( !isset( $customInputList[$toolKey . "_parameter_" . $key] ) && $checkCurrAction )
            $parameterValue = '';


        if ( $storeList and
             $http->hasPostVariable( $toolKey . "_parameter_" . $key ) )
        {
            $parameterValue = $http->postVariable( $toolKey . "_parameter_" . $key );
            if ( $oldParameterValue != $parameterValue )
            {
                $makeNewBlock = true;
                $newActionParameters[$key] = $parameterValue;
            }
        }
        else if ( $storeList and
                  isset( $customInputList[$toolKey . "_parameter_" . $key] ) )
        {
            $parameterValue = implode( ',', $customInputList[$toolKey . "_parameter_" . $key] );
            if ( $oldParameterValue != $parameterValue )
            {
                $makeNewBlock = true;
                $newActionParameters[$key] = $parameterValue;
            }
        }

        $toolParameterArray = array();
        $toolParameterArray['name'] = $key;
        $toolParameterArray['value'] = $parameterValue;
        $toolParameterArray['description'] = $actionDescription[$key];
        $toolParameters[] = $toolParameterArray;
    }
    $toolList[] = array( 'name' => $toolName,
                         'parameters' => $toolParameters );
    if ( $storeList )
    {
        if ( count( $newActionParameters ) == 0 )
        {
            if ( $iniAppend->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) )
            {
                $iniAppend->removeGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) );
            }
        }
        else
        {
            $removedActionParameters = array_diff( $actionParameters, $newActionParameters );
            if ( count( $removedActionParameters ) > 0 )
            {
                if ( $iniAppend->hasGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) ) )
                {
                    $iniAppend->removeGroup( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ) );
                }
            }
            foreach ( array_keys( $newActionParameters ) as $newKey )
            {
                $parameterValue = $newActionParameters[$newKey];
                $iniAppend->setVariable( "Tool_" . $toolbarPosition . "_" . $toolName . "_" . ( $toolKey + 1 ), $newKey, $parameterValue );
            }
        }
    }
}
if ( $storeList )
{
    $succeed = $iniAppend->save( false, false, false, false, true, true );
}
if ( $removeCache )
{
    removeRelatedCache( $currentSiteAccess );
}

$toolbarIni =& eZINI::instance( "toolbar.ini", 'settings', null, false, true, false );
$toolbarIni->prependOverrideDir( "siteaccess/$currentSiteAccess", false, 'siteaccess' );
$toolbarIni->parse();

if ( $toolbarIni->hasVariable( "Tool", "AvailableToolArray" ) )
{
    $availableToolArray = $toolbarIni->variable( "Tool", "AvailableToolArray" );
}

$tpl =& templateInit();

$tpl->setVariable( 'toolbar_position', $toolbarPosition );
$tpl->setVariable( 'tool_list', $toolList );
$tpl->setVariable( 'available_tool_list', $availableToolArray  );
$tpl->setVariable( 'current_siteaccess', $currentSiteAccess );

$Result = array();
$Result['content'] =& $tpl->fetch( "design:visual/toolbar.tpl" );
$Result['path'] = array( array( 'url' => 'visual/toolbarlist',
                                'text' => ezi18n( 'kernel/design', 'Toolbar list' ) ) );

function removeRelatedCache( $siteAccess )
{
    // Delete compiled template
    $ini =& eZINI::instance();
    $iniPath = "settings/siteaccess/$siteAccess";
    $siteINI = eZINI::instance( 'site.ini.append', $iniPath );
    if ( $siteINI->hasVariable( 'FileSettings', 'CacheDir' ) )
    {
        $cacheDir = $siteINI->variable( 'FileSettings', 'CacheDir' );
        if ( $cacheDir[0] == "/" )
        {
            $cacheDir = eZDir::path( array( $cacheDir ) );
        }
        else
        {
            if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
            {
                $varDir = $siteINI->variable( 'FileSettings', 'VarDir' );
                $cacheDir = eZDir::path( array( $varDir, $cacheDir ) );
            }
        }
    }
    else if ( $siteINI->hasVariable( 'FileSettings', 'VarDir' ) )
    {
         $varDir = $siteINI->variable( 'FileSettings', 'VarDir' );
         $cacheDir = $ini->variable( 'FileSettings', 'CacheDir' );
         $cacheDir = eZDir::path( array( $varDir, $cacheDir ) );
    }
    else
    {
        $cacheDir =  eZSys::cacheDirectory();
    }
    $compiledTemplateDir = $cacheDir . "/template/compiled";
    eZDir::unlinkWildcard( $compiledTemplateDir . "/", "*pagelayout*.*" );
    include_once( 'kernel/classes/ezcache.php' );
    eZCache::clearByTag( 'template-block' );

    // Expire content view cache
    include_once( 'kernel/classes/ezcontentcachemanager.php' );
    eZContentCacheManager::clearAllContentCache();
}

?>
