<?php
/**
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase
 * 	These tests are run from child plugins.
 */

/**
 * WP_UnitTestCase unit tests for wpdtrt_plugin
 */
class PluginTest extends WP_UnitTestCase {

    /**
     * SetUp
     * Automatically called by PHPUnit before each test method is run
     */
    public function setUp() {
  		// Make the factory objects available.
        parent::setUp();
    }

    /**
     * TearDown
     * Automatically called by PHPUnit after each test method is run
     */
    public function tearDown() {

    	parent::tearDown();
    }

    // ########## MOCK DATA ########## //


    // ########## TEST ########## //

	/**
	 * Test
	 */
	public function test_placeholder() {
		
		$this->assertEquals(
			true,
			true
		);	
	}
}
