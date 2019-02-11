<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    simple_rest
 * @copyright  2019 Norbert Czirjak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'enrolled_users' => array(
        'classname'   => 'local_simple_rest_external',
        'methodname'  => 'enrolled_users',
        'classpath'   => 'local/simple_rest/externallib.php',
        'description' => 'Return the actual enrolled users',
        'type'        => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Online Users CSV' => array(
        'functions' => array ('enrolled_users'),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);
