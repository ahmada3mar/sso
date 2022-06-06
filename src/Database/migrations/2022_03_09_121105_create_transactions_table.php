<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('currency');

            $table->string('authentication_entityId')->index('authentication_entityId');
            $table->foreign('authentication_entityId')->on('merchants')->references('authentication_entityId');

            $table->string('UUID')->index('UUID');
            $table->string('merchantTransactionId');
            $table->longText('notificationUrl');
            $table->longText('shopperResultUrl');
            $table->enum('status' , [1,2,3,4,5])->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
