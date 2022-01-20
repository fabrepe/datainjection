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

class PluginDatainjectionInfoCollection
{

    var $infosCollection;


   function __construct() {

      $this->infosCollection = [];
   }


    //---- Getter ----//

    /**
    * Load all the mappings for a specified model
    *
    * @param model_id the model ID
   **/
   function load($models_id) {

      global $DB;

      $query = "SELECT *
                FROM `glpi_plugin_datainjection_infos`
                WHERE `models_id` = '".$models_id."'
                ORDER BY `itemtype` ASC";

      foreach ($DB->request($query) as $data) {
         $infos = new PluginDatainjectionInfo;
         $infos->fields = $data;
         $this->infosCollection[] = $infos;
      }
   }

    /**
    * Return all the mappings for this model
    *
    * @return the list of all the mappings for this model
   **/
   function getAllInfos() {

      return $this->infosCollection;
   }

}
