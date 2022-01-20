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

class PluginDatainjectionContractInjection extends Contract
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

      $tab                       = Search::getOptions(get_parent_class($this));

      //Specific to location
      $tab[5]['checktype']       = 'date';

      $tab[6]['minvalue']        = 0;
      $tab[6]['maxvalue']        = 120;
      $tab[6]['step']            = 1;
      $tab[6]['checktype']       = 'integer';

      $tab[7]['minvalue']        = 0;
      $tab[7]['maxvalue']        = 120;
      $tab[7]['step']            = 1;
      $tab[7]['checktype']       = 'integer';

      $tab[11]['checktype']      = 'float';

      $tab[20]['checktype']      = 'date';

      $tab[22]['linkfield']      = 'billing';

      // Associated suppliers
      $tab[29]['linkfield']      = 'suppliers_id';
      $tab[29]['displaytype']    = 'relation';
      $tab[29]['relationclass']  = 'Contract_Supplier';
      $tab[29]['relationfield']  = $tab[29]['linkfield'];

      //Remove some options because some fields cannot be imported
      $blacklist     = PluginDatainjectionCommonInjectionLib::getBlacklistedOptions(get_parent_class($this));
      $notimportable = [12, 13, 20, 41, 42, 43, 44, 45, 72];

      $options['ignore_fields'] = array_merge($blacklist, $notimportable);

      $options['displaytype']   = ["dropdown"         => [4],
                                      "date"             => [5],
                                      "dropdown_integer" => [6, 7, 21],
                                      "bool"             => [86],
                                      "alert"            => [59],
                                      "billing"          => [22],
                                      "renewal"          => [23],
                                      "multiline_text"   => [16 ,90]];

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


    /**
    * @param $info      array
    * @param $option    array
   **/
   function showAdditionalInformation($info = [], $option = []) {

      $name = "info[".$option['linkfield']."]";

      switch ($option['displaytype']) {
         case 'alert' :
            Contract::dropdownAlert(['name' => $name]);
            break;

         case 'renewal' :
            Contract::dropdownContractRenewal($name);
            break;

         case 'billing' :
            Dropdown::showNumber(
                $name, ['value' => 0,
                                            'min'   => 12,
                                            'max'   => 60,
                                            'step'  => 12,
                                            'toadd' => [0 => Dropdown::EMPTY_VALUE,
                                                             1 => sprintf(
                                                                 _n(
                                                                     '%d month',
                                                                     '%d months', 1
                                                                 ), 1
                                                             ),
                                                             2 => sprintf(
                                                                 _n(
                                                                     '%d month',
                                                                     '%d months', 2
                                                                 ), 2
                                                             ),
                                                             3 => sprintf(
                                                                 _n(
                                                                     '%d month',
                                                                     '%d months', 3
                                                                 ), 3
                                                             ),
                                                             6 => sprintf(
                                                                 _n(
                                                                     '%d month',
                                                                     '%d months', 6
                                                                 ), 6
                                                             )]],
                ['unit' => 'month']
            );
            break;

         default:
            break;
      }
   }

}
