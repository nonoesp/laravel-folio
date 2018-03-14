<?php

use Illuminate\Support\Facades\Config;

class ItemTest extends Orchestra\Testbench\TestCase
{
	protected $item;

	public function setUp()
	{
		parent::setUp();

		$this->item = new Nonoesp\Folio\Models\Item;
	}

	// public function testTable() {
		//$table_name = 'table_name';
		//Config::set('folio.db-prefix', 'folio_');
		//$this->assertEquals('folio_'.$table_name, $this->folio->table($table_name));
	// }

}
