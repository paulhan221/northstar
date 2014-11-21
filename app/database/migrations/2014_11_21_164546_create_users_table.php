<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($collection)
		{	
		    $collection->unique('email');
		    $collection->text('mobile');
		    $collection->text('first_name');
		    $collection->text('last_name');
		    $collection->unique('drupal_uid');
		    $collection->text('doc_uid');
		    $collection->text('campaigns');
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
