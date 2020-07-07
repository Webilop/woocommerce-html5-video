<?php
/**
 * Class SettingsClassTest
 *
 * @package Woocommerce_Html5_Video
 * 
 * @coversDefaultClass \Webilop\WooHtmlVideo\Settings
 * 
 * @group unit
 * @group settings
 */

class SettingsClassTest extends WP_UnitTestCase
{
    public $obj;

    public function setUp()
    {
        $this->obj = new Webilop\WooHtmlVideo\Settings();
    }

    /**
     * @covers ::save
     * @covers ::get
     */
    public function test_save_and_get()
    {
        // test different types of data
        $s = [
            'number' => 123,
            'string' => 'mytest',
            'list' => ['a', 987, ['c']],
            'bool' => false,
            'null' => null
        ];
        foreach ($s as $k => $v) {
            $this->assertTrue($this->obj->save($k, $v), sprintf('Error saving %s with value %s', $k, json_encode($v)));
            $this->assertEquals($v, $this->obj->get($k), sprintf('Error getting value for %s', $k));
        }
    }

    /**
     * @covers ::saveAll
     * @covers ::getAll
     */
    public function test_saveAll_and_getAll()
    {
        // test different types of data
        $s = [
            'number' => 123,
            'string' => 'mytest',
            'list' => ['a', 987, ['c']],
            'bool' => false,
            'null' => null
        ];
        $this->assertTrue($this->obj->saveAll($s), sprintf('Error saving %s', json_encode($s)));
        $this->assertEquals($s, $this->obj->getAll(), sprintf('Error getting the settings', $s));
    }

    /**
     * @covers ::saveAll
     * @covers ::save
     * @covers ::get
     */
    public function test_save_update_and_get()
    {
        // save some data first
        $this->assertTrue($this->obj->saveAll([
            'number' => 123,
            'list' => ['Z', 'X'],
            'bool' => true
        ]));

        // test different types of data
        $s = [
            'number' => 123,
            'string' => 'mytest',
            'list' => ['a', 987, ['c']],
            'bool' => false,
            'null' => null
        ];
        foreach ($s as $k => $v) {
            $this->assertTrue($this->obj->save($k, $v), sprintf('Error saving %s with value %s', $k, json_encode($v)));
            $this->assertEquals($v, $this->obj->get($k), sprintf('Error getting value for %s', $k));
        }
    }
}
