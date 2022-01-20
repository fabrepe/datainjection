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

class PluginDatainjectionCartridgeItemInjection extends CartridgeItem
                                                implements PluginDatainjectionInjectionInterface
{


   static function getTable($classname = null) {

      $parenttype = get_parent_class();
      return $parenttype::getTable();

   }


   function isPrimaryType() {

      return true;
   }


   function connectedTo() {

      return [];
   }


    /**
    * @see plugins/datainjection/inc/PluginDatainjectionInjectionInterface::getOptions()
   **/
   function getOptions($primary_type = '') {

      $tab                 = Search::getOptions(get_parent_class($this));

      //Specific to location
      $tab[3]['linkfield'] = 'locations_id';

      $tab[8]['minvalue']  = '1';
      $tab[8]['maxvalue']  = '100';
      $tab[8]['step']      = 1;
      $tab[8]['-1']        = __('Never');

      //Remove some options because some fields cannot be imported
      $blacklist = PluginDatainjectionCommonInjectionLib::getBlacklistedOptions(get_parent_class($this));
      $notimportable            = [91, 92, 93];
      $options['ignore_fields'] = array_merge($blacklist, $notimportable);

      $options['displaytype']   = ["dropdown"         => [3, 4, 23, 49],
                                      "user"             => [24],
                                      "multiline_text"   => [16, 90],
                                      "dropdown_integer" => [8]];

      $options['checktype']     = ["integer" => [8]];

      return PluginDatainjectionCommonInjectionLib::addToSearchOptions($tab, $options, $this);
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
