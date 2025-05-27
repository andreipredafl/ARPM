<?php

namespace Tests\Unit;

use App\Jobs\ProcessProductImage;
use App\Models\Product;
use App\Services\SpreadsheetService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpreadsheetServiceTest extends TestCase
{
    /*
        Not ssure if this workes great, I did not tested the code 
    */
    
    use RefreshDatabase;

    public function test_process_valid_spreadsheet_data()
    {
        Queue::fake();

        $importerMock = \Mockery::mock();
        $importerMock->shouldReceive('import')->andReturn([
            [
                'product_code' => 'P1',
                'quantity' => 5
            ],
        ]);

        app()->instance('importer', $importerMock);

        $service = new SpreadsheetService();
        $service->processSpreadsheet('file.xlssx');

        $this->assertDatabaseHas('products', [
            'code' => 'P1'
        ]);
        
        Queue::assertPushed(ProcessProductImage::class);
    }

    public function test_invalid_rows_are_skipped()
    {
        Queue::fake();

        $importerMock = \Mockery::mock();
        $importerMock->shouldReceive('import')->andReturn([
            [
                'product_code' => '',
                'quantity' => 0
            ], 
        ]);

        app()->instance('importer', $importerMock);

        $service = new SpreadsheetService();
        $service->processSpreadsheet('file.xlsx');

        $this->assertDatabaseCount('products', 0);
        Queue::assertNothingPushed();
    }
}
