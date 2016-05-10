<?php

final class Test_WPCF_Utils extends WPCF_UnitTestCase {

	function test_insert_at_position() {

		$test_source = array();
		for( $i = 0; $i < 100; ++$i ) {
			$test_source[ "item_$i" ] = $i;
		}
		$test_to_insert = array( 'insert_first' => 1042, 'insert_last' => 1043 );

		// Inserting at specified index

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, 0 );
		$this->insert_at_position_check( $result, 0, 1, array( 'item_0' => 2, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, 5 );
		$this->insert_at_position_check( $result, 5, 6, array( 'item_0' => 0, 'item_4' => 4, 'item_5' => 7, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, count( $test_source ) );
		$this->insert_at_position_check( $result, 100, 101, array( 'item_0' => 0, 'item_99' => 99 ) );

		// Negative numeric index

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, -1 );
		$this->insert_at_position_check( $result, 100, 101, array( 'item_0' => 0, 'item_99' => 99 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, -1000 );
		$this->insert_at_position_check( $result, 0, 1, array( 'item_0' => 2, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, -3 );
		$this->insert_at_position_check( $result, 98, 99, array( 'item_0' => 0, 'item_97' => 97, 'item_98' => 100, 'item_99' => 101 ) );

		// Inserting by element key

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, array( 'key' => 'item_4', 'where' => 'after' ) );
		$this->insert_at_position_check( $result, 5, 6, array( 'item_0' => 0, 'item_4' => 4, 'item_5' => 7, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, array( 'key' => 'item_5', 'where' => 'before' ) );
		$this->insert_at_position_check( $result, 5, 6, array( 'item_0' => 0, 'item_4' => 4, 'item_5' => 7, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, array( 'where' => 'before' ) );
		$this->insert_at_position_check( $result, 0, 1, array( 'item_0' => 2, 'item_99' => 101 ) );

		$result = WPCF_Utils::insert_at_position( $test_source, $test_to_insert, array( 'where' => 'after' ) );
		$this->insert_at_position_check( $result, 100, 101, array( 'item_0' => 0, 'item_99' => 99 ) );

		$this->expectOutputString( '' );
	}


	private function insert_at_position_basic( $result ) {
		$this->assertTrue( is_array( $result ) );
		$this->assertCount( 102, $result );
		for( $i = 0; $i < 100; ++$i) {
			$this->assertArrayHasKey( "item_$i", $result );
		}
		$this->assertArrayHasKey( 'insert_first', $result );
		$this->assertArrayHasKey( 'insert_last', $result );
		$this->assertEquals( 1042, $result['insert_first'] );
		$this->assertEquals( 1043, $result['insert_last'] );
	}


	private function insert_at_position_check( $result, $insert_first_index, $insert_last_index, $item_indices ) {
		$this->insert_at_position_basic( $result );
		$result_keys = array_keys( $result );
		$this->assertTrue( $result_keys[ $insert_first_index ] == 'insert_first' );
		$this->assertTrue( $result_keys[ $insert_last_index ] == 'insert_last' );
		foreach( $item_indices as $item_slug => $index ) {
			$this->assertTrue( $result_keys[ $index ] == $item_slug );
		}
	}
}