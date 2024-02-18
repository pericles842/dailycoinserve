<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class DailycoinTablesV1 extends Migration
{


  public function up()
  {

    DB::unprepared('
    CREATE PROCEDURE create_timestamps(
        IN nombre_tabla VARCHAR(50)
    )
    BEGIN
        SET @sql = CONCAT(\'ALTER TABLE \', nombre_tabla, 
                          \' ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
                          ADD updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;\');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END
    ');

    //Tabla history
    Schema::create('history', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100)->nullable(false)->comment('nombre de banco o entidad');
      $table->float('price')->nullable(false)->comment('precio de la entidad o banco');
      $table->unsignedInteger('day_week')->nullable(false)->comment('dia de la semana al cual pertenece el grupo');
      $table->enum('statistics', ['bajo', 'neutro', 'alto'])->nullable(false)->comment('Estadística de la moneda');
    });
    DB::statement("CALL create_timestamps('history')");

    //Tabla observation
    Schema::create('observation', function (Blueprint $table) {
      $table->id();
      $table->text('note')->nullable(false)->comment('Observación');
      $table->string('email', 500)->nullable(true);
      $table->unsignedInteger('observation_type')->nullable(false);
    });
    DB::statement("CALL create_timestamps('observation')");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::unprepared('DROP PROCEDURE IF EXISTS create_timestamps');
    Schema::dropIfExists('history');
    Schema::dropIfExists('observation');
  }
}
