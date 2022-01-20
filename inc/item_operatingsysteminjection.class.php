<?php

/**
 * -------------------------------------------------------------------------
 * Datainjection plugin for GLPI
 * Copyright (C) 2009-2022 by the Datainjection plugin Development Team.
 *
 * https://github.com/pluginsGLPI/datainjection
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Datainjection plugin.
 *
 * Datainjection plugin is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Datainjection plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Datainjection plugin. If not, see <http://www.gnu.org/licenses/>.
 * --------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

class PluginDatainjectionItem_OperatingsystemInjection extends Item_OperatingSystem
                                                implements PluginDatainjectionInjectionInterface
{


   static function getTable($classname = null) {

      $parenttype = get_parent_class();
      return $parenttype::getTable();
   }

   function isPrimaryType() {

      return false;
   }

   /**
   * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::getOptions()
  **/
   function getOptions($primary_type = '') {

      $tab = Item_OperatingSystem::rawSearchOptionsToAdd(get_parent_class($this));
      $searchoptions = [];
      foreach ($tab as $option) {
         if (is_numeric($option['id'])) {
            if ($option['table'] != 'glpi_items_operatingsystems') {
               $option['linkfield'] = getForeignKeyFieldForTable($option['table']);
            } else {
               $option['linkfield'] = $option['field'];
            }
            $searchoptions[$option['id']] = $option;
         }
      }

      $options['ignore_fields'] = [];
      $options['displaytype'] = [
         "dropdown" => [
           41, 45, 46, 48, 61, 63, 64
         ],
         "text" => [43, 44]
      ];
      return PluginDatainjectionCommonInjectionLib::addToSearchOptions($searchoptions, $options, $this);
   }

   function connectedTo() {

      return ['Computer', 'NetworkEquipment'];
   }


    /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::addOrUpdateObject()
   **/
   function addOrUpdateObject($values = [], $options = []) {

      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }
}
