<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DailycoinTablesV1 extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('history', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->nullable(false)->comment('nombre de banco o entidad');
      $table->float('price')->nullable(false)->comment('precio de la entidad o banco');
      $table->unsignedInteger('day_week')->length(1)->nullable(false)->comment('dia de la semana al cual pertenece el grupo');
      $table->enum('statistics', ['bajo', 'neutro', 'alto'])->nullable(false)->comment('Estadística de la moneda');
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
    Schema::dropIfExists('history');
  }
}
