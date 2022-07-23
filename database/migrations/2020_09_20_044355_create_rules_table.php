<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('author_id')->nullable();
            $table->string('registrar_pid')->nullable();
            $table->text('description')->nullable();

            // start rule type
            $table->boolean('hunting')->nullable();
            $table->boolean('detection')->nullable();
            $table->boolean('correlation')->nullable();
            $table->boolean('monitoring_statistics')->nullable();
            //end rule type

            $table->string('version')->nullable();
            $table->text('references')->nullable();
            $table->string('triggers')->nullable();
            $table->string('kill_chain_phases')->nullable();
            $table->string('malware_name')->nullable();

            //Start Security Domain
            $table->boolean('endpoint')->nullable();
            $table->boolean('network')->nullable();
            //End Security Domain

            $table->string('priority_level')->nullable();
            $table->text('detection_logic')->nullable();
          
            $table->string('status')->nullable();
            $table->text('white_black_list')->nullable();
            $table->string('confidence')->nullable();
            $table->string('false_positive')->nullable();
            $table->string('requirement')->nullable();
            $table->string('response_solutions')->nullable();
            $table->string('attachments')->nullable();

            //Start Rule Languages
            // SIEM
            $table->boolean('splunk')->nullable();
            $table->boolean('elastic')->nullable();
            $table->boolean('arcsight')->nullable();
            $table->boolean('qradar')->nullable();
            $table->boolean('python')->nullable();
            $table->boolean('eql')->nullable();
            $table->boolean('sigma')->nullable();
            // YARA
            $table->boolean('yara')->nullable();
            // IPS/IDS
            $table->boolean('suricata')->nullable();
            $table->boolean('snort')->nullable();
            //End Rule Languages

            // SIEM
            $table->longText('splunk_detection_query')->nullable();
            $table->longText('elastic_detection_query')->nullable();
            $table->longText('arcsight_detection_query')->nullable();
            $table->longText('qradar_detection_query')->nullable();
            $table->longText('python_detection_query')->nullable();
            $table->longText('eql_detection_query')->nullable();
            $table->longText('sigma_detection_query')->nullable();
            // YARA
            $table->longText('yara_detection_query')->nullable();
            // IPS/IDS
            $table->longText('suricata_detection_query')->nullable();
            $table->longText('snort_detection_query')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('rules');
    }
}
