<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::connection('sqlite')->create('dml', function (Blueprint $table) {
      $table->id();
      $table->tinyText('filename')->unique();
      $table->tinyText('modelIdentCode'); // merujuk ke @modelIdentCode
      $table->tinyText('senderIdent'); // merujuk ke senderIdent code atau sudah di transform codenya, gunakan file config jika ingin transform
      $table->tinyText('dmlType'); // merujuk ke @dmlType yang sudah di transform, 'Partial DML', 'Complete DML', 'CSL'
      $table->tinyText('yearOfDataIssue'); // merujuk ke @yearOfDataIssue
      $table->tinyText('seqNumber'); // merujuk ke @seqNumber
      $table->string('securityClassification');
      $table->bigInteger('brexDmRef'); // merujuk filename brex yang sama dengan table csdb
      $table->text('dmlRef')->nullable(); //merujuk ke dmlStatus/dmlRef
      $table->text('remarks')->nullable();
      /**
       * merujuk ke dmlEntry. cara tulis: 
       * [
       *  { "dmlEntryType":{$dmlEntryType},"issueType":{$issueType},"ref":{$ref},"securityClassification":{$sc},"responsiblePartnerCompany":{$responsiblePartnerCompany},"answer":{$answer},"remarks":{$remarks}}
       *  { "dmlEntryType":{$dmlEntryType},"issueType":{$issueType},"ref":{$ref},"securityClassification":{$sc},"responsiblePartnerCompany":{$responsiblePartnerCompany},"answer":{$answer},"remarks":{$remarks}}
       * ]
       * jika tidak ada, isi dengan null
       */
      $table->json('content')->nullable(); 
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('dml');
  }
};
