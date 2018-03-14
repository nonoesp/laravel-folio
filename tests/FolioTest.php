<?php

use Illuminate\Support\Facades\Config;

class FolioTest extends Orchestra\Testbench\TestCase
{
	protected $folio;

	public function setUp()
	{
		parent::setUp();

		$this->folio = new Nonoesp\Folio\Folio;
	}

	// public function testUserURL() {
		//$user = new User();
		//$user->twitter = 'nonoesp';
		//$this->assertEquals('/@'.$user->twitter, $this->folio->userURL($user));
	// }

	public function testTable() {
		$table_name = 'table_name';
		Config::set('folio.db-prefix', 'folio_');
		$this->assertEquals('folio_'.$table_name, $this->folio->table($table_name));
	}

}
