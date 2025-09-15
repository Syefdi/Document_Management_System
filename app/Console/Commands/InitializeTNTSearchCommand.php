<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocumentIndexer;
use App\Services\FileContentExtractor;
use App\Models\Documents;
use Illuminate\Support\Facades\Log;

class InitializeTNTSearchCommand extends Command
{
    protected $signature = 'tntsearch:init';
    protected $description = 'Initialize TNTSearch index and create required tables';

    public function handle()
    {
        $this->info('Initializing TNTSearch index...');
        
        try {
            // Ensure search_index directory exists
            $searchIndexPath = storage_path('app/search_index');
            if (!file_exists($searchIndexPath)) {
                mkdir($searchIndexPath, 0755, true);
                $this->info('Created search_index directory.');
            }
            
            // Remove existing corrupted index file
            $indexFile = storage_path('app/search_index/documents.index');
            if (file_exists($indexFile)) {
                unlink($indexFile);
                $this->info('Removed existing corrupted index file.');
            }
            
            // Create DocumentIndexer instance
            $indexer = app(DocumentIndexer::class);
            
            $this->info('DocumentIndexer created successfully.');
            
            // Check if we have any documents to index
            $documentsCount = Documents::where('isIndexed', true)->count();
            $this->info("Found {$documentsCount} documents marked for indexing.");
            
            if ($documentsCount > 0) {
                $this->info('Adding sample documents to index...');
                
                // Get first few documents to test indexing
                $documents = Documents::where('isIndexed', true)->limit(5)->get();
                
                foreach ($documents as $document) {
                    try {
                        $this->info("Processing document: {$document->name}");
                        $indexer->createDocumentIndex($document->id, $document->url, $document->location);
                        $this->info("✓ Document {$document->id} indexed successfully");
                    } catch (\Exception $e) {
                        $this->error("✗ Error indexing document {$document->id}: " . $e->getMessage());
                    }
                }
            }
            
            // Test search functionality
            $this->info('Testing search functionality...');
            $results = $indexer->search('document', 5);
            $this->info('Search results: ' . json_encode($results));
            
            $this->info('TNTSearch initialization completed!');
            
        } catch (\Exception $e) {
            $this->error('Error initializing TNTSearch: ' . $e->getMessage());
            Log::error('TNTSearch initialization error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}