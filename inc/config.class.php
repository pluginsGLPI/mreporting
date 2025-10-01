<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

use Glpi\Exception\Http\NotFoundHttpException;

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
    throw new NotFoundHttpException("Sorry. You can't access directly to this file");
}

class PluginMreportingConfig extends CommonDBTM
{
    public static $rightname = 'config';

    public static function getTypeName($nb = 0)
    {
        return __s('Configuration', 'mreporting');
    }

    public static function getIcon()
    {
        return 'ti ti-settings';
    }

    /**
     * Définition des onglets
    **/
    public function defineTabs($options = [])
    {
        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('PluginMreportingProfile', $ong, $options);

        return $ong;
    }

    public function rawSearchOptions()
    {
        $tab = [];

        $tab[] = [
            'id'   => 'common',
            'name' => self::getTypeName(),
        ];

        $tab[] = [
            'id'            => '1',
            'table'         => $this->getTable(),
            'field'         => 'name',
            'name'          => __s('Name'),
            'datatype'      => 'itemlink',
            'itemlink_type' => $this->getType(),
        ];

        $tab[] = [
            'id'       => '2',
            'table'    => $this->getTable(),
            'field'    => 'is_active',
            'name'     => __s('Active'),
            'datatype' => 'bool',
        ];

        $tab[] = [
            'id'            => '3',
            'table'         => $this->getTable(),
            'field'         => 'show_area',
            'name'          => __s('See area', 'mreporting'),
            'datatype'      => 'bool',
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => '4',
            'table'         => $this->getTable(),
            'field'         => 'spline',
            'name'          => __s('Curve lines (SVG)', 'mreporting'),
            'datatype'      => 'bool',
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => '5',
            'table'         => $this->getTable(),
            'field'         => 'show_label',
            'name'          => __s('See values', 'mreporting'),
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => '6',
            'table'         => $this->getTable(),
            'field'         => 'flip_data',
            'name'          => __s('Reverse data array', 'mreporting'),
            'datatype'      => 'bool',
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'           => '7',
            'table'        => $this->getTable(),
            'field'        => 'unit',
            'name'         => __s('Unit', 'mreporting'),
            'autocomplete' => true,
        ];

        $tab[] = [
            'id'           => '8',
            'table'        => $this->getTable(),
            'field'        => 'default_delay',
            'name'         => __s('Default delay', 'mreporting'),
            'autocomplete' => true,
        ];

        $tab[] = [
            'id'           => '9',
            'table'        => $this->getTable(),
            'field'        => 'condition',
            'name'         => __s('Additional condition for MySQL', 'mreporting'),
            'autocomplete' => true,
        ];

        $tab[] = [
            'id'            => '10',
            'table'         => $this->getTable(),
            'field'         => 'show_graph',
            'name'          => __s('See graphic', 'mreporting'),
            'datatype'      => 'bool',
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => '11',
            'table'         => $this->getTable(),
            'field'         => 'classname',
            'name'          => __s('Class', 'mreporting'),
            'massiveaction' => false,
        ];

        $tab[] = [
            'id'            => '12',
            'table'         => $this->getTable(),
            'field'         => 'graphtype',
            'name'          => __s('Default chart format', 'mreporting'),
            'searchtype'    => 'equals',
            'massiveaction' => true,
        ];

        $tab[] = [
            'id'            => '13',
            'table'         => $this->getTable(),
            'field'         => 'is_notified',
            'name'          => __s('Send this report with the notification', 'mreporting'),
            'datatype'      => 'bool',
            'massiveaction' => true,
        ];

        return $tab;
    }

    public static function getSpecificValueToDisplay($field, $values, array $options = [])
    {
        if (!is_array($values)) {
            $values = [$field => $values];
        }
        switch ($field) {
            case 'graphtype':
                return $values[$field];
            case 'show_label':
                $labels = self::getLabelTypes();

                return $labels[$values[$field]];
        }

        return parent::getSpecificValueToDisplay($field, $values, $options);
    }

    /**
     * @since version 0.84
     *
     * @param $field
     * @param $name            (default '')
     * @param $values          (default '')
     * @param $options   array
     * @return string|void|integer
     **/
    public static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = [])
    {
        if (!is_array($values)) {
            $values = [$field => $values];
        }
        $options['display'] = false;
        $options['value']   = $values[$field];
        switch ($field) {
            case 'graphtype':
                return Dropdown::showFromArray(
                    $name,
                    ['PNG' => 'PNG', 'SVG' => 'SVG'],
                    $options,
                );
            case 'show_label':
                return self::dropdownLabel($name, $options);
        }

        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }

    public function getFromDBByFunctionAndClassname($function, $classname)
    {
        /** @var DBmysql $DB */
        global $DB;

        return $this->getFromDBByCrit([
            'name'     => $function,
            'classname' => $classname,
        ]);
    }

    /**
     * add First config Link
     *@return void
     **/
    public static function addFirstconfigLink()
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        $buttons = [];
        $title   = '';

        if (Session::haveRight('config', READ)) {
            $buttons['config.php?new=1'] = __s('Initialize graphics configuration', 'mreporting');
        }
        Html::displayTitle(
            $CFG_GLPI['root_doc'] . '/plugins/mreporting/pics/config2.png',
            $title,
            $title,
            $buttons,
        );
    }

    /**
     * create First Config for all graphs
     *@return void
     **/
    public function createFirstConfig()
    {
        //$reports = array();
        $classConfig = false;
        $classObject = null;

        $inc_dir = Plugin::getPhpDir('mreporting') . '/inc';
        //parse inc dir to search report classes
        $classes = PluginMreportingCommon::parseAllClasses($inc_dir);

        foreach ($classes as $classname) {
            if (!class_exists($classname)) {
                $class_filedir = $inc_dir .
                             strtolower(str_replace('PluginMreporting', '', $classname)) . '.class.php';
                if (file_exists($class_filedir)) {
                    require_once $class_filedir;
                } else {
                    continue;
                }
            }

            $functions = get_class_methods($classname);

            // We check if a config function exists in class
            foreach ($functions as $funct_name) {
                if ($funct_name == 'preconfig') { // If a preconfig exists we construct the class
                    $classConfig = true;
                    if (!is_a($classname, PluginMreportingBaseclass::class, true)) {
                        return;
                    }
                    $classObject = new $classname([]);
                }
            }

            foreach ($functions as $funct_name) {
                $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $funct_name);
                if ($ex_func[0] != 'report') {
                    continue;
                }

                $input = [];

                if ($classConfig) { // If a preconfig exists in class we do it
                    /** @var null|PluginMreportingOther|PluginMreportingHelpdesk $classObject */
                    $input = $classObject->preconfig($funct_name, $classname, $this);
                } else {// Else we get the default preconfig
                    $input = $this->preconfig($funct_name, $classname);
                }

                $input['firstconfig'] = 1;
                unset($input['id']);
                $this->add($input);
            }
        }
    }

    /**
     * Preconfig datas for standard system
     * @graphname internal name of graph
     * @return array|boolean
     **/
    public function preconfig($funct_name, $classname)
    {
        if ($funct_name != -1 && $classname) {
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $funct_name);
            if ($ex_func[0] != 'report') {
                return false;
            }
            $gtype = strtolower($ex_func[1]);

            switch ($gtype) {
                case 'area':
                case 'garea':
                    $this->fields['name']          = $funct_name;
                    $this->fields['classname']     = $classname;
                    $this->fields['is_active']     = '1';
                    $this->fields['show_area']     = '1';
                    $this->fields['show_graph']    = '1';
                    $this->fields['spline']        = '1';
                    $this->fields['default_delay'] = '365';
                    $this->fields['graphtype']     = 'SVG';
                    break;
                case 'line':
                case 'gline':
                    $this->fields['name']          = $funct_name;
                    $this->fields['classname']     = $classname;
                    $this->fields['is_active']     = '1';
                    $this->fields['spline']        = '1';
                    $this->fields['show_area']     = '0';
                    $this->fields['show_graph']    = '1';
                    $this->fields['default_delay'] = '365';
                    $this->fields['graphtype']     = 'SVG';
                    break;
                case 'vstackbar':
                    $this->fields['name']          = $funct_name;
                    $this->fields['classname']     = $classname;
                    $this->fields['is_active']     = '1';
                    $this->fields['show_graph']    = '1';
                    $this->fields['default_delay'] = '365';
                    $this->fields['graphtype']     = 'SVG';
                    break;
                case 'hgbar':
                    $this->fields['name']          = $funct_name;
                    $this->fields['classname']     = $classname;
                    $this->fields['is_active']     = '1';
                    $this->fields['show_graph']    = '1';
                    $this->fields['show_label']    = 'hover';
                    $this->fields['spline']        = '0';
                    $this->fields['show_area']     = '0';
                    $this->fields['default_delay'] = '365';
                    $this->fields['graphtype']     = 'SVG';
                    break;
                default:
                    $this->fields['name']          = $funct_name;
                    $this->fields['classname']     = $classname;
                    $this->fields['is_active']     = '1';
                    $this->fields['show_label']    = 'hover';
                    $this->fields['spline']        = '0';
                    $this->fields['show_area']     = '0';
                    $this->fields['show_graph']    = '1';
                    $this->fields['default_delay'] = '30';
                    $this->fields['graphtype']     = 'SVG';
                    break;
            }
        }

        return $this->fields;
    }

    /**
     * show not used Graphs dropdown
     * @name name of dropdown
     * @options array example $value
     *@return int
     **/
    public static function dropdownGraph($name, $options = [])
    {
        $self   = new self();
        $common = new PluginMreportingCommon();
        $rand   = mt_rand();

        $select = "<select name='" . htmlspecialchars($name) . "' id='dropdown_" . htmlspecialchars($name) . htmlspecialchars(strval($rand)) . "'>";
        $select .= "<option value='-1' selected>" . Dropdown::EMPTY_VALUE . '</option>';

        $i       = 0;
        $reports = $common->getAllReports();
        foreach ($reports as $classname => $report) {
            foreach ($report['functions'] as $function) {
                if (!$self->getFromDBByFunctionAndClassname($function['function'], $classname)) {
                    $graphs[$classname][$function['category_func']][] = $function;
                }
            }

            if (isset($graphs[$classname])) {
                $select .= '<optgroup label="' . htmlspecialchars($report['title']) . '">';

                foreach ($graphs[$classname] as $cat => $graph) {
                    $select .= '<optgroup label="' . htmlspecialchars($cat) . '">';

                    foreach ($graph as $k => $v) {
                        $comment = '';
                        $desc = '';
                        if (isset($v['desc'])) {
                            $comment = $v['desc'];
                            $desc    = ' (' . $comment . ')';
                        }

                        $select .= '<option  title="' .
                            htmlspecialchars($comment) . "\"
                            value='" . $classname . ';' . $v['function'] .
                            "'" . ($options['value'] == $classname . ';' .
                            $v['function'] ? ' selected ' : '') . '>';
                        $select .= htmlspecialchars($v['title']) . htmlspecialchars($desc);
                        $select .= '</option>';

                        $i++;
                    }
                    $select .= '</optgroup>';
                }
                $select .= '</optgroup>';
            }
        }

        $select .= '</select>';

        echo $select;

        return $rand;
    }

    /**
     * show Label dropdown
     * @name name of dropdown
     * @options array example $value
     *@return integer|string
     **/
    public static function dropdownLabel($name, $options = [], $notall = false)
    {
        $params['value']     = 0;
        $params['toadd']     = [];
        $params['on_change'] = '';

        if (is_array($options) && count($options)) {
            foreach ($options as $key => $val) {
                $params[$key] = $val;
            }
        }

        $items = $params['toadd'];
        $items += self::getLabelTypes($notall);

        return Dropdown::showFromArray($name, $items, $params);
    }

    /**
     * Get label types
     *
     * @return array of types
    **/
    public static function getLabelTypes($notall = false)
    {
        $options['never'] = __s('Never');
        $options['hover'] = __s('On mouse over', 'mreporting');
        if (!$notall) {
            $options['always'] = __s('Always');
        }

        return $options;
    }

    /**
     * Get label Name
     *
     * @param $value type ID
    **/
    public static function getLabelTypeName($value)
    {
        switch ($value) {
            case 'hover':
                return __s('On mouse over', 'mreporting');
            case 'never':
                return __s('Never');
            case 'always':
                return __s('Always');
        }
    }

    /**
     * checkVisibility
     *
     * @param $show_label show_label value (hover - always - never)
     * @param $always
     * @param $hover
    **/
    public static function checkVisibility($show_label, &$always, &$hover)
    {
        switch ($show_label) {
            case 'hover':
                $always = 'false';
                $hover  = 'true';
                break;
            case 'always':
                $always = 'true';
                $hover  = 'true';
                break;
            default:
                $always = 'false';
                $hover  = 'false';
                break;
        }
    }

    public static function getColors($index = 20)
    {
        if (isset($_SESSION['mreporting']['colors'])) {
            $colors = $_SESSION['mreporting']['colors'];
        } else {
            /* if ($index <= 10) {
             $colors = array(
                "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd",
                "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"
             );
            } else {*/
            $colors = [
                '#1f77b4', '#aec7e8', '#ff7f0e', '#ffbb78', '#2ca02c',
                '#98df8a', '#d62728', '#ff9896', '#9467bd', '#c5b0d5',
                '#8c564b', '#c49c94', '#e377c2', '#f7b6d2', '#7f7f7f',
                '#c7c7c7', '#bcbd22', '#dbdb8d', '#17becf', '#9edae5',
            ];
            // }
        }
        $tmp = $colors;
        while (count($colors) < $index) {
            $colors = array_merge($tmp, $colors);
        }

        return $colors;
    }

    public function prepareInputForAdd($input)
    {
        if (isset($input['name']) && $this->getFromDBByFunctionAndClassname($input['name'], $input['classname'])) {
            if (!isset($input['firstconfig'])) {
                Session::addMessageAfterRedirect(
                    __s('Object already exists', 'mreporting'),
                    false,
                    ERROR,
                );
            }
            return [];
        }

        return $input;
    }

    public function prepareInputForUpdate($input)
    {
        if (isset($input['classname']) && is_a($input['classname'], PluginMreportingBaseclass::class, true) && method_exists(new $input['classname']([]), 'checkConfig')) {
            $object      = new $input['classname']([]);
            $checkConfig = $object->checkConfig($input);
            if (!$checkConfig['result']) {
                Session::addMessageAfterRedirect($checkConfig['message'], false, ERROR);

                return [];
            }
        }

        return $input;
    }

    public function showForm($ID, $options = [])
    {
        /** @var array $LANG */
        global $LANG;

        $this->initForm($ID, $options);

        if ($ID <= 0) {
            if (isset($_GET['name']) && isset($_GET['classname'])) {
                $this->preconfig($_GET['name'], $_GET['classname']);
                $_GET['preconfig'] = 1;
            } else {
                $_GET['name']      = -1;
                $_GET['classname'] = -1;
                $_GET['preconfig'] = -1;
            }
        }

        echo "<table class='tab_cadre_fixe'>";
        echo '<tr>';
        echo "<td class='tab_bg_2 center' colspan='2'>";
        echo __s('Preconfiguration') . '&nbsp;';
        $opt    = ['value' => $_GET['preconfig']];
        $rand   = self::dropdownGraph('graphname', $opt);
        $params = ['graphname' => '__VALUE__'];
        Ajax::updateItemOnSelectEvent(
            "dropdown_graphname$rand",
            'show_preconfig',
            '../ajax/dropdownGraphs.php',
            $params,
        );
        echo "<span id='show_preconfig'></span>";
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        $style = ($_GET['preconfig'] == -1 && $ID <= 0) ? 'display:none;' : "'display:block;'";
        echo "<div id='show_form' style='" . htmlspecialchars($style) . "'>";

        $this->showFormHeader($options);

        echo "<tr class='tab_bg_1'>";
        echo '<td>' . __s('Name') . '</td>';
        echo '<td>';
        echo htmlspecialchars($this->fields['name']);
        echo "<input type='hidden' name='name' value=\"" . htmlspecialchars($this->fields['name']) . '">';
        echo '</td>';

        echo "<td colspan='2'>";
        $gtype = '';

        $f_name = $this->fields['name'];

        $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
        if (isset($ex_func[1])) {
            $gtype = strtolower($ex_func[1]);
        }

        $short_classname = str_replace('PluginMreporting', '', $this->fields['classname']);

        if (
            !empty($short_classname)
            && !empty($f_name)
            && isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])
        ) {
            echo '&nbsp;';
            echo "<a href='graph.php?short_classname=" . htmlspecialchars($short_classname) . '&f_name=' . htmlspecialchars($f_name) . '&gtype=' . htmlspecialchars($gtype) . "'>";
            echo htmlspecialchars($LANG['plugin_mreporting'][$short_classname][$f_name]['title']);
            echo '</a>';
        } else {
            echo __s('No report is available !', 'mreporting');
        }

        echo "<input type='hidden' name='classname' value=\"" . htmlspecialchars($this->fields['classname']) . '">';
        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>' . __s('See graphic', 'mreporting') . '</td>';
        echo '<td>';
        Dropdown::showYesNo('show_graph', htmlspecialchars($this->fields['show_graph']));
        echo '</td>';

        echo '<td>' . __s('Default chart format', 'mreporting') . '</td>';
        echo '<td>';
        Dropdown::showFromArray(
            'graphtype',
            ['PNG'   => 'PNG', 'SVG' => 'SVG'],
            ['value' => $this->fields['graphtype']],
        );
        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>' . __s('Active') . '</td>';
        echo '<td>';
        Dropdown::showYesNo('is_active', $this->fields['is_active']);
        echo '</td>';

        echo '<td>';
        echo __s('See area', 'mreporting');
        echo '</td>';
        echo '<td>';
        if ($gtype == 'area' || $gtype == 'garea') {
            Dropdown::showYesNo('show_area', $this->fields['show_area']);
        } else {
            echo Dropdown::getYesNo($this->fields['show_area']);
            echo "<input type='hidden' name='show_area' value='0'>";
        }

        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>';
        echo __s('Curve lines (SVG)', 'mreporting');
        echo '</td>';
        echo '<td>';
        if ($gtype == 'area' || $gtype == 'garea' || $gtype == 'line' || $gtype == 'gline') {
            Dropdown::showYesNo('spline', $this->fields['spline']);
        } else {
            echo Dropdown::getYesNo($this->fields['spline']);
            echo "<input type='hidden' name='spline' value='0'>";
        }
        echo '</td>';

        echo '<td>';
        echo __s('See values', 'mreporting');
        echo '</td>';

        echo '<td>';
        $opt = ['value' => $this->fields['show_label']];
        if ($gtype != 'area' && $gtype != 'garea' && $gtype != 'line' && $gtype != 'gline') {
            self::dropdownLabel('show_label', $opt);
        } else {
            self::dropdownLabel('show_label', $opt, true);
        }
        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>';
        echo __s('Reverse data array', 'mreporting');
        echo '</td>';
        echo '<td>';
        if ($gtype != 'hbar' && $gtype != 'pie' && $gtype != 'area' && $gtype != 'line') {
            Dropdown::showYesNo('flip_data', $this->fields['flip_data']);
        } else {
            echo Dropdown::getYesNo($this->fields['flip_data']);
            echo "<input type='hidden' name='flip_data' value='0'>";
        }
        echo '</td>';

        echo '<td>';
        echo __s('Unit', 'mreporting');
        echo '</td>';
        echo '<td>';
        echo Html::input(
            'unit',
            [
                'value' => $this->fields['unit'],
                'size'  => 10,
            ],
        );
        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>';
        echo __s('Default delay', 'mreporting');
        echo '</td>';
        echo '<td>';
        echo Html::input(
            'default_delay',
            [
                'value' => $this->fields['default_delay'],
                'size'  => 10,
            ],
        );
        echo '</td>';

        echo '<td>';
        echo __s('Additional condition for MySQL', 'mreporting');
        echo '</td>';
        echo '<td>';
        echo Html::input(
            'condition',
            [
                'value' => $this->fields['condition'],
            ],
        );
        echo '</td>';
        echo '</tr>';

        echo "<tr class='tab_bg_1'>";
        echo '<td>';
        echo __s('Send this report with the notification', 'mreporting');
        echo '</td>';
        echo '<td>';
        Dropdown::showYesNo('is_notified', $this->fields['is_notified']);
        echo '</td>';
        echo '<td>&nbsp;</td>';
        echo '<td>&nbsp;</td>';
        echo '</tr>';

        $this->showFormButtons($options);

        echo '</div>';

        return true;
    }

    /**
     * initialize config for graph display options
     *
     * @param $name of graph
     * @param $classname of graph
     *
     * @return array{
     *     area: bool,
     *     spline: bool,
     *     flip_data: bool,
     *     unit: string,
     *     show_label: string,
     *     delay: string,
     *     condition: string,
     *     show_graph: bool,
     *     randname: int,
     *     graphtype: string
     * }
     **/
    public static function initConfigParams($name, $classname)
    {
        $crit = ['area'  => false,
            'spline'     => false,
            'flip_data'  => false,
            'unit'       => '',
            'show_label' => 'never',
            'delay'      => '30',
            'condition'  => '',
            'show_graph' => false,
            'randname'   => mt_rand(),
            'graphtype'  => 'SVG',
        ];

        $self = new self();
        if ($self->getFromDBByFunctionAndClassname($name, $classname)) {
            $crit['area']       = $self->fields['show_area'];
            $crit['spline']     = $self->fields['spline'];
            $crit['show_label'] = $self->fields['show_label'];
            $crit['flip_data']  = $self->fields['flip_data'];
            $crit['unit']       = $self->fields['unit'];
            $crit['delay']      = $self->fields['default_delay'];
            $crit['condition']  = $self->fields['condition'];
            $crit['show_graph'] = $self->fields['show_graph'];
            $crit['graphtype']  = $self->fields['graphtype'];
            $crit['randname']   = $classname . $name;
        }

        // DEBUG_MREPORTING is constant. It is true if debug mode is enabled, false otherwise. For PHPStan, this constant is always true or false.
        /* @phpstan-ignore-next-line */
        if (DEBUG_MREPORTING) {
            $crit['show_graph'] = true;
        }

        return $crit;
    }

    /**
     * test for value of show_graph field
     *
     * @param $name of graph
     * @param $classname of graph
    **/
    public static function showGraphConfigValue($name, $classname)
    {
        // DEBUG_MREPORTING is constant. It is true if debug mode is enabled, false otherwise. For PHPStan, this constant is always true or false.
        /* @phpstan-ignore-next-line */
        if (DEBUG_MREPORTING) {
            return true;
        }

        // DEBUG_MREPORTING is constant. It is true if debug mode is enabled, false otherwise. For PHPStan, this constant is always true or false.
        /* @phpstan-ignore-next-line */
        $self = new self();
        if ($self->getFromDBByFunctionAndClassname($name, $classname)) {
            return $self->fields['show_graph'];
        }

        return false;
    }
}
