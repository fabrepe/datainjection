<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2011 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Remi Collet
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginDatainjectionEntityInjection extends Entity
                                         implements PluginDatainjectionInjectionInterface{

   function __construct() {
      $this->table = getTableForItemType(get_parent_class($this));
   }


   function isPrimaryType() {
      return true;
   }


   function connectedTo() {
      return array('Document');
   }


   function getOptions($primary_type = '') {

      $tab = Search::getOptions(get_parent_class($this));

      //Remove some options because some fields cannot be imported
      $options['ignore_fields'] = array(2, 19);
      $options['displaytype']   = array("multiline_text" => array(16, 17), "dropdown" => array(9));
      $tab = PluginDatainjectionCommonInjectionLib::addToSearchOptions($tab, $options, $this);
      foreach ($tab as $id => $option) {
         //If table is NOT glpi_entitites but glpi_entitydatas => remove option
         if ($option['table'] != 'glpi_entities') {
            unset($tab[$id]);
         }
      }
      return $tab;
   }

   /**
    * Standard method to add an object into glpi
    *
    * @param values fields to add into glpi
    * @param options options used during creation
    *
    * @return an array of IDs of newly created objects : for example array(Computer=>1, Networkport=>10)
   **/
   function addOrUpdateObject($values=array(), $options=array()) {

      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }

   function customimport($input = array(), $add = true) {

      if (!isset($input['completename']) || empty($input['completename'])) {
         return -1;
      }

      // Import a full tree from completename
      $names  = explode('>',$input['completename']);
      $fk     = $this->getForeignKeyField();
      $i      = count($names);
      $parent = 0;
      $entity = new Entity();
      $level  = 0;

      foreach ($names as $name) {
         $name = trim($name);
         $i--;
         $level++;
         if (empty($name)) {
            // Skip empty name (completename starting/endind with >, double >, ...)
            continue;
         }
         $tmp['name'] = $name;

         if (!$i) {
            // Other fields (comment, ...) only for last node of the tree
            foreach ($input as $key => $val) {
               if ($key != 'completename') {
                  $tmp[$key] = $val;
               }
            }
         }
         $tmp['level']       = $level;
         $tmp['entities_id'] = $parent;

         //Does the entity alread exists ?
         $results = getAllDatasFromTable('glpi_entities', 
                                         "`name`='$name' AND `entities_id`='$parent'");
         //Entity doesn't exists => create it
         if (empty($results)) {
            $parent = CommonDropdown::import($tmp);
         } else {
            //Entity already exists, use the ID as parent
            $ent    = array_pop($results);
            $parent = $ent['id'];
         }
      }
      return $parent;
   }
   
   function customDataAlreadyInDB($injectionClass, $values, $options) {
      if (!isset($values['completename'])) {
         return false;
      } else {
         $results = getAllDatasFromTable('glpi_entities', 
                                         "`completename`='".$values['completename']."'");

         if (empty($results)) {
            return false;
         } else {
            $ent    = array_pop($results);
            return $ent['id'];
         }
      }
   }

   function processAfterInsertOrUpdate($values, $add = true) {

      if (isset($values['EntityData'])) {
         $tmp = $values['EntityData'];
         $entitydata = new EntityData();
         $entities = getAllDatasFromTable("glpi_entitydatas", 
                                          "`entities_id`='".$values['Entity']['id']."'");
         if (!empty($entities)) {
            //Update entitydata
            $tmp = array_pop($entities);
            foreach ($values['EntityData'] as $key => $value) {
               $tmp[$key] = $value;
            }
            $entitydata->update($tmp);
         } else {
            $entitydata->getEmpty();
            foreach ($entitydata->fields as $key => $value) {
               if ($value != '') {
                  $tmp[$key] = $value;
               }
            }
            foreach ($values['EntityData'] as $key => $value) {
               $tmp[$key] = $value;
            }
            $tmp['entities_id'] = $values['Entity']['id'];
            $entitydata->add($tmp);
         }
      
         unset($values['EntityData']);
      }
   }
   
}

?>