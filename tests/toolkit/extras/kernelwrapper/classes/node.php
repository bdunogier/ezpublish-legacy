<?php
/**
 * File containing the ezpNode class
 *
 * @copyright Copyright (C) 1999-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package tests
 */

class ezpNode
{
    function __construct( $object, $parentNodeID, $isMainNode )
    {
        $this->nodeAssignment = $object->createNodeAssignment( $parentNodeID, $isMainNode );
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'node':
            {
                if ( !isset( $this->node ) )
                    $this->node = $this->nodeAssignment->fetchNode();

                if ( !$this->node instanceof eZContentObjectTreeNode )
                    throw new ezcBaseValueException( 'node', get_class( $this->node ), 'eZContentObjectTreeNode', 'member' );

                return $this->node;
            } break;
            default:
            {
                if ( !isset( $this->node ) )
                    $this->node = $this->nodeAssignment->fetchNode();

                if ( !$this->node instanceof eZContentObjectTreeNode )
                    throw new ezcBaseValueException( 'node', get_class( $this->node ), 'eZContentObjectTreeNode', 'member' );

                if ( $this->node->hasAttribute( $name ) )
                    return $this->node->attribute( $name );

                throw new ezcBasePropertyNotFoundException( '->node->attribute( ' . $name . ' )' );
            }
        }
    }

    public function __call( $name, $arguments )
    {
        return call_user_func_array( array( $this->node, $name ), $arguments );
    }

    /**
     * Moves the node to a new location
     *
     * This method can only be called after the object which this node belongs
     * to has been published. No node exists before after publishing.
     *
     * @param int $newParentNodeID
     * @return void
     */
    function move( $newParentNodeID )
    {
        return eZContentObjectTreeNodeOperations::move( $this->node_id, $newParentNodeID );
    }

    function remove()
    {
        return $this->node->removeThis();
    }
}

?>
