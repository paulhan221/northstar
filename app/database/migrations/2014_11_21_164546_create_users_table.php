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
			/* Email address - forced to lowercase */
		    $collection->unique('email');

		    /* Mobile phone number */
		    $collection->text('mobile');

		     /* Drupal UID */
		    $collection->text('drupal_uid');

		    /* Database-generated ID */
		    $collection->text('doc_uid');

		    /* Mailing address */
		    $collection->text('addr_street1');
		    $collection->text('addr_street2');
		    $collection->text('addr_city');
		    $collection->text('addr_state');
		    $collection->text('addr_zip');

		     /* Country */
		    $collection->text('country');

		    /* Date of birth */
		    $collection->text('birthdate');

		    /* First name */
		    $collection->text('first_name');

		     /* Last name */
		    $collection->text('last_name');

		     /* List of campaign actions */
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
