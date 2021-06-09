<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNFeProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfe_product', function (Blueprint $table) {
            $table->foreignId('nfe_id');
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->softDeletes();

            $table->foreign('nfe_id')->references('id')->on('nfes');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfe_product');
    }
}
