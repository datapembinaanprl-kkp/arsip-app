<?php
    use Illuminate\Database\Migrations\Migration; 
    use Illuminate\Database\Schema\Blueprint; 
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {    
        /**     * Run the migrations.     */    
        public function up(): void    {        
            Schema::create('programs', 
            function (Blueprint $table) 
            {            
                $table->uuid('id')->primary();            
                $table->string('name');            
                $table->text('description')->nullable();            
                $table->date('start_date')->nullable();            
                $table->date('end_date')->nullable();            
                $table->enum('status', ['active', 'inactive', 'completed']);            
                $table->uuid('created_by');            
                $table->foreign('created_by')->references('id')->on('users')>onDelete('cascade');            
                $table->timestamps();        });    }
    /**     * Reverse the migrations.     */    
        public function down(): void    {        
            Schema::dropIfExists('programs');    
            } 
            };