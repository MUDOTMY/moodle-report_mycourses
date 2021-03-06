<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Version details.
 *
 * @package    report
 * @subpackage mycourses
 * @copyright  2021 Modernlms {@link http://modernlms.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// This adds the settings link to the folder/submenu.
$ADMIN->add('report_mycourse', $settings);
// This adds a link to an external page.
$ADMIN->add('report_mycourse', new admin_externalpage('report_mycourse', 'Corse Progress',
        $CFG->wwwroot.'/report/mycourse/index.php'));

// no report settings
$settings = null;