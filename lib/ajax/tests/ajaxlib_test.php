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
 * Unit tests for (some of) ../ajaxlib.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/ajax/ajaxlib.php');


/**
 * Unit tests for ../ajaxlib.php functions.
 *
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ajax_testcase extends advanced_testcase {

    var $user_agents = array(
        'MSIE' => array(
            '5.5' => array('Windows 2000' => 'Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)'),
            '6.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)'),
            '7.0' => array('Windows XP SP2' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; YPC 3.0.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)')
        ),
        'Firefox' => array(
            '1.0.6'   => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.10) Gecko/20050716 Firefox/1.0.6'),
            '1.5'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; nl; rv:1.8) Gecko/20051107 Firefox/1.5'),
            '1.5.0.1' => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.0.1) Gecko/20060111 Firefox/1.5.0.1'),
            '2.0'     => array('Windows XP' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1',
                'Ubuntu Linux AMD64' => 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1) Gecko/20060601 Firefox/2.0 (Ubuntu-edgy)')
        ),
        'Safari' => array(
            '312' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-us) AppleWebKit/312.1 (KHTML, like Gecko) Safari/312'),
            '2.0' => array('Mac OS X' => 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/412 (KHTML, like Gecko) Safari/412')
        ),
        'Opera' => array(
            '8.51' => array('Windows XP' => 'Opera/8.51 (Windows NT 5.1; U; en)'),
            '9.0'  => array('Windows XP' => 'Opera/9.0 (Windows NT 5.1; U; en)',
                'Debian Linux' => 'Opera/9.01 (X11; Linux i686; U; en)')
        )
    );

    /**
     * Uses the array of user agents to test ajax_lib::ajaxenabled
     */
    function test_ajaxenabled() {
        global $CFG, $USER;

        $this->resetAfterTest(true);

        $CFG->enableajax = 1;
        $USER->ajax      = 1;

        // Should be true
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['1.5']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['6.0']['Windows XP SP2'];
        $this->assertTrue(ajaxenabled());

        // Should be false
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['1.0.6']['Windows XP'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['312']['Mac OS X'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['8.51']['Windows XP'];
        $this->assertFalse(ajaxenabled());

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['5.5']['Windows 2000'];
        $this->assertFalse(ajaxenabled());

        // Test array of tested browsers
        $tested_browsers = array('MSIE' => 6.0, 'Gecko' => 20061111);
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['7.0']['Windows XP SP2'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $tested_browsers = array('Safari' => 412, 'Opera' => 9.0);
        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Firefox']['2.0']['Windows XP'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['MSIE']['7.0']['Windows XP SP2'];
        $this->assertFalse(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Safari']['2.0']['Mac OS X'];
        $this->assertTrue(ajaxenabled($tested_browsers));

        $_SERVER['HTTP_USER_AGENT'] = $this->user_agents['Opera']['9.0']['Windows XP'];
        $this->assertTrue(ajaxenabled($tested_browsers));
    }
}
