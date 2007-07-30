<?php
//
// Created on: <16-Apr-2002 11:00:12 amos>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.10.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

include_once( "kernel/classes/ezworkflow.php" );
include_once( "kernel/classes/eztrigger.php" );
include_once( "kernel/classes/ezworkflowgroup.php" );
include_once( "kernel/classes/ezworkflowgrouplink.php" );
include_once( "lib/ezutils/classes/ezhttppersistence.php" );

function removeSelectedGroups( &$http, &$groups, $base )
{
    if ( $http->hasPostVariable( "DeleteGroupButton" ) )
    {
        if ( eZHttpPersistence::splitSelected( $base,
                                               $groups, $http, "id",
                                               $keepers, $rejects ) )
        {
            $groups = $keepers;
            for ( $i = 0; $i < count( $rejects ); ++$i )
            {
                $reject =& $rejects[$i];
                $group_id = $reject->attribute("id");

                // Remove all workflows in current group
                $list_in_group = & eZWorkflowGroupLink::fetchWorkflowList( 0, $group_id, $asObject = true);
                $workflow_list =  eZWorkflow::fetchList( );

                $list = array();
                for ( $k=0; $k<count( $workflow_list ); $k++ )
                {
                    for ( $j=0;$j<count( $list_in_group );$j++ )
                    {
                        $id =  $workflow_list[$k]->attribute("id");
                        $workflow_id =  $list_in_group[$j]->attribute("workflow_id");
                        if ( $id === $workflow_id )
                        {
                            $list[] =& $workflow_list[$k];
                        }
                    }
                }
                foreach ( $list as $workFlow )
                {
                  eZTrigger::removeTriggerForWorkflow( $workFlow->attribute( 'id' ) );
                  $workFlow->remove();
                }

                $reject->remove( );
                eZWorkflowGroupLink::removeGroupMembers( $group_id );
            }
        }
    }
}

$Module =& $Params["Module"];

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( "EditGroupButton" ) && $http->hasPostVariable( "EditGroupID" ) )
{
    $Module->redirectTo( $Module->functionURI( "groupedit" ) . "/" . $http->postVariable( "EditGroupID" ) );
    return;
}

if ( $http->hasPostVariable( "NewGroupButton" ) )
{
    $params = array();

    $Module->redirectTo( $Module->functionURI( "groupedit" ) );
    return;
}

$sorting = null;

if ( !isset( $TemplateData ) or !is_array( $TemplateData ) )
{
    $TemplateData = array( array( "name" => "groups",
                                  "http_base" => "ContentClass",
                                  "data" => array( "command" => "group_list",
                                                   "type" => "class" ) ) );
}

$Module->setTitle( ezi18n( 'kernel/workflow', 'Workflow group list' ) );
include_once( "kernel/common/template.php" );
$tpl = templateInit();

include_once( "kernel/classes/datatypes/ezuser/ezuser.php" );
$user = eZUser::currentUser();
foreach( $TemplateData as $tpldata )
{
    $tplname = $tpldata["name"];
    $data = $tpldata["data"];
    $asObject = isset( $data["as_object"] ) ? $data["as_object"] : true;
    $base = $tpldata["http_base"];
    unset( $list );
    $list = eZWorkflowGroup::fetchList( $asObject );
    removeSelectedGroups( $http, $list, $base );
    $tpl->setVariable( $tplname, $list );
}

$tpl->setVariable( "module", $Module );

$Result = array();
$Result['content'] =& $tpl->fetch( "design:workflow/grouplist.tpl" );
$Result['path'] = array( array( 'text' => ezi18n( 'kernel/workflow', 'Workflow' ),
                                'url' => false ),
                         array( 'text' => ezi18n( 'kernel/workflow', 'Group list' ),
                                'url' => false ) );


?>
