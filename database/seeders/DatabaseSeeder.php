<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // Schema::connection('sqlite')->dropIfExists('ddn');
    // Schema::connection('sqlite')->create('ddn', function (Blueprint $table) {
    //   $table->id();
    //   $table->tinyText('filename')->unique();
    //   $table->tinyText('modelIdentCode'); // merujuk ke @modelIdentCode
    //   $table->tinyText('senderIdent'); // merujuk ke senderIdent code atau sudah di transform codenya, gunakan file config jika ingin transform
    //   $table->tinyText('receiverIdent'); // merujuk ke receiver code atau sudah di transform codenya, gunakan file config jika ingin transform
    //   $table->tinyText('yearOfDataIssue'); // merujuk ke @yearOfDataIssue
    //   $table->tinyText('seqNumber'); // merujuk ke @seqNumber

    //   $table->tinyText('year');
    //   $table->tinyText('month');
    //   $table->tinyText('day');
      
    //   $table->string('securityClassification');
    //   $table->bigInteger('brexDmRef'); // merujuk filename brex yang sama dengan table csdb
    //   $table->text('authorization'); //merujuk ke ddnStatus/authorization
    //   $table->text('remarks')->nullable(); //merujuk ke ddnStatus/remarks
    //   /**
    //    * merujuk ke dmlEntry. cara tulis: 
    //    * { "deliveryList: [filename1, filename2, filename3, ...], "mediaIdent" : {$label} }
    //    * jika tidak ada, isi dengan null
    //    */
    //   $table->json('ddnContent')->nullable(); 
    // });

    // Schema::connection('sqlite')->dropIfExists('dml');
    // Schema::connection('sqlite')->create('dml', function (Blueprint $table) {
    //   $table->id();
    //   $table->tinyText('filename')->unique();
    //   $table->tinyText('modelIdentCode'); // merujuk ke @modelIdentCode
    //   $table->tinyText('senderIdent'); // merujuk ke senderIdent code atau sudah di transform codenya, gunakan file config jika ingin transform
    //   $table->tinyText('dmlType'); // merujuk ke @dmlType yang sudah di transform, 'Partial DML', 'Complete DML', 'CSL'
    //   $table->tinyText('yearOfDataIssue'); // merujuk ke @yearOfDataIssue
    //   $table->tinyText('seqNumber'); // merujuk ke @seqNumber

    //   $table->tinyText('languageIsoCode');
    //   $table->tinyText('countryIsoCode');

    //   $table->tinyText('year');
    //   $table->tinyText('month');
    //   $table->tinyText('day');

    //   $table->string('securityClassification');
    //   $table->bigInteger('brexDmRef'); // merujuk filename brex yang sama dengan table csdb
    //   $table->text('dmlRef')->nullable(); //merujuk ke dmlStatus/dmlRef
    //   $table->text('remarks')->nullable();
    //   /**
    //    * merujuk ke dmlEntry. cara tulis: 
    //    * [
    //    *  { "dmlEntryType":{$dmlEntryType},"issueType":{$issueType},"ref":{$ref},"securityClassification":{$sc},"responsiblePartnerCompany":{$responsiblePartnerCompany},"answer":{$answer},"remarks":{$remarks}}
    //    *  { "dmlEntryType":{$dmlEntryType},"issueType":{$issueType},"ref":{$ref},"securityClassification":{$sc},"responsiblePartnerCompany":{$responsiblePartnerCompany},"answer":{$answer},"remarks":{$remarks}}
    //    * ]
    //    * jika tidak ada, isi dengan null
    //    */
    //   $table->json('content')->nullable();
    // });

    // Schema::connection('sqlite')->dropIfExists('ddn');
    // Schema::connection('sqlite')->create('ddn', function (Blueprint $table) {
    //   $table->id();
    //   $table->tinyText('filename')->unique();
    //   $table->tinyText('modelIdentCode'); // merujuk ke @modelIdentCode
    //   $table->tinyText('senderIdent'); // merujuk ke senderIdent code atau sudah di transform codenya, gunakan file config jika ingin transform
    //   $table->tinyText('receiverIdent'); // merujuk ke receiver code atau sudah di transform codenya, gunakan file config jika ingin transform
    //   $table->tinyText('yearOfDataIssue'); // merujuk ke @yearOfDataIssue
    //   $table->tinyText('seqNumber'); // merujuk ke @seqNumber

    //   $table->tinyText('year');
    //   $table->tinyText('month');
    //   $table->tinyText('day');

    //   $table->string('securityClassification');
    //   $table->bigInteger('brexDmRef'); // merujuk filename brex yang sama dengan table csdb
    //   $table->text('authorization'); //merujuk ke ddnStatus/authorization
    //   $table->text('remarks')->nullable(); //merujuk ke ddnStatus/remarks
    //   /**
    //    * merujuk ke dmlEntry. cara tulis: 
    //    * { "deliveryList: [filename1, filename2, filename3, ...], "mediaIdent" : {$label} }
    //    * jika tidak ada, isi dengan null
    //    */
    //   $table->json('content')->nullable();
    // });
  }
}
