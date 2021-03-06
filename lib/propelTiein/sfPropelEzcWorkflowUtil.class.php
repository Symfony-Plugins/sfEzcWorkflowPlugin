<?php
/**
 * File containing the sfPropelEzcWorkflowUtil class. Comes from ezcWorkflowDatabaseTiein package
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Utility methods for sfPropelEzcWorkflowDefinitionStorage.
 * @package    symfony
 * @subpackage plugin
 */
abstract class sfPropelEzcWorkflowUtil
{
    /**
     * Wrapper for serialize() that returns an empty string
     * for empty arrays and null values.
     *
     * @param  mixed $var
     * @return string
     */
    public static function serialize( $var )
    {
        $var = serialize( $var );

        if ( $var == 'a:0:{}' || $var == 'N;' )
        {
            return '';
        }

        return base64_encode( $var );
    }

    /**
     * Wrapper for unserialize().
     *
     * @param  string $serializedVar
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public static function unserialize( $serializedVar, $defaultValue = array() )
    {
        if ( !empty( $serializedVar ) )
        {
            return unserialize( base64_decode( $serializedVar ) );
        }
        else
        {
            return $defaultValue;
        }
    }
}
?>
