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

        $this->taxonomy = $this->create_taxonomy();

        $this->plugin = $this->taxonomy->get_plugin();
    }

    /**
     * TearDown
     * Automatically called by PHPUnit after each test method is run
     */
    public function tearDown() {

    	parent::tearDown();
    }

    /**
	 * Create the 'tour' taxonomy
	 *
	 * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/45
	 */
    public function create_taxonomy() {
		$taxonomy = wpdtrt_tourdates_taxonomy_tour_init();

		return $taxonomy;
    }

    // ########## MOCK DATA ########## //


    // ########## TEST ########## //

	/**
	 * Test that we are dealing with the expected plugin options
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/25
	 */
	public function test_plugin_options() {
		
		$plugin_options_full = array(
        	'start_date' => array(
	            'type' => 'text',
	            'label' => 'Start date',
	            'admin_table' => true,
	            'admin_table_label' => 'Start',
	            'admin_table_sort' => true,
	            'tip' => 'YYYY-M-D',
        	),
			'end' => array(
	            'type' => 'text',
	            'label' => 'End date',
	            'admin_table' => true,
	            'admin_table_label' => 'End',
	            'admin_table_sort' => true,
	            'tip' => 'YYYY-M-D',
        	)
		);

		$plugin_options_empty = array();

		$this->assertEquals(
			$this->plugin->set_plugin_options( $plugin_options_full ),
			$plugin_options_full,
			'Plugin options incorrectly merged (null -> some)'
		);	

		$this->assertEquals(
			$this->plugin->set_plugin_options( $plugin_options_full ),
			$this->plugin->get_plugin_options(),
			'Plugin options incorrectly merged (null -> some)'
		);	

		$this->assertEquals(
			$this->plugin->set_plugin_options( $plugin_options_empty ),
			$plugin_options_empty,
			'Plugin options incorrectly merged (some -> none)'
		);

		$this->assertEquals(
			$this->plugin->set_plugin_options( $plugin_options_empty ),
			$this->plugin->get_plugin_options(),
			'Plugin options incorrectly merged (some -> none)'
		);
	}
}
