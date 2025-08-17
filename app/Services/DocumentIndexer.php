<?php

namespace App\Services;

use TeamTNT\TNTSearch\TNTSearch;
use Illuminate\Support\Facades\Log;

class DocumentIndexer
{
    protected $tnt;
    protected $indexName = 'documents.index';
    protected $databasePath;
    protected $storagePath = 'app/search_index/';
    protected $extractor;
    protected $config;

    public function __construct(FileContentExtractor $extractor)
    {
        $this->extractor = $extractor;
        $this->databasePath = "{$this->storagePath}{$this->indexName}";
        $this->config = [
            'driver'    => 'sqlite',
            'database'  => storage_path($this->databasePath),
            'storage'   => storage_path($this->storagePath),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ];
        $this->createIndex();
    }

    public function createIndex()
    {
        try {
            $this->tnt = new TNTSearch;
            $this->tnt->loadConfig($this->config);
            
            if (!file_exists(storage_path($this->databasePath))) {
                Log::info('Creating new TNTSearch index');
                $indexer = $this->tnt->createIndex($this->indexName);
                $indexer->setPrimaryKey('id');
            } else {
                Log::info('Loading existing TNTSearch index');
                $this->tnt->selectIndex($this->indexName);
            }
        } catch (\Throwable $th) {
            Log::error('Error creating index: ' . $th->getMessage());
        }
    }

    public function createDocumentIndex($id, $path, $location)
    {
        try {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            Log::info("Indexing document: ID=$id, Path=$path, Location=$location, Extension=$extension");
            
            if (in_array($extension, ['txt', 'pdf', 'docx', 'doc', 'xlsx', 'xls'])) {
                // Berdasarkan debug, FileContentExtractor bekerja dengan baik menggunakan original method
                // Gunakan path dan location asli dari database
                $content = $this->extractor->extractContent($path, $location);
                Log::info("Extracted content length: " . strlen($content ?? ''));
                
                if ($content) {
                    $this->addDocumentIndex($id, $content);
                    Log::info("Successfully indexed document: $id");
                } else {
                    Log::warning("No content extracted for document: $id");
                }
            } else {
                Log::info("Unsupported file extension: $extension for document: $id");
            }
        } catch (\Throwable $th) {
            Log::error("Error indexing document $id: " . $th->getMessage());
        }
    }

    public function addDocumentIndex($id, $content)
    {
        try {
            $tnt = new TNTSearch;
            $tnt->loadConfig($this->config);
            $tnt->selectIndex($this->indexName);

            $indexer = $tnt->getIndex();

            $doc = [
                'id'      => $id,
                'content' => $content,
            ];

            $indexer->insert($doc);
            Log::info("Document indexed successfully: $id");
        } catch (\Throwable $th) {
            Log::error("Error adding document to index $id: " . $th->getMessage());
        }
    }

    public function deleteDocumentIndex($id)
    {
        try {
            $tnt = new TNTSearch;
            $tnt->loadConfig($this->config);
            $tnt->selectIndex($this->indexName);

            $indexer = $tnt->getIndex();
            $indexer->delete($id);

            $this->optimizeIndex();
            Log::info("Document removed from index: $id");
        } catch (\Throwable $th) {
            Log::error("Error deleting document from index $id: " . $th->getMessage());
        }
    }

    public function search($query, $limit)
    {
        try {
            $this->tnt->selectIndex($this->indexName);
            
            // Debug: Log search query
            Log::info("Searching with query: '$query', limit: $limit");
            
            $results = $this->tnt->search($query, $limit);
            
            // Debug: Log search results
            Log::info("Search results: " . json_encode($results));
            
            return $results;
        } catch (\Throwable $th) {
            Log::error("Error searching: " . $th->getMessage());
            return [];
        }
    }

    public function optimizeIndex()
    {
        try {
            $indexPath = storage_path($this->databasePath);
            $pdo = new \PDO("sqlite:$indexPath");
            $pdo->exec('VACUUM');
            Log::info("Index optimized");
        } catch (\Throwable $th) {
            Log::error("Error optimizing index: " . $th->getMessage());
        }
    }

    // Method baru untuk debug
    public function getIndexStats()
    {
        try {
            $indexPath = storage_path($this->databasePath);
            if (!file_exists($indexPath)) {
                return ['exists' => false, 'message' => 'Index file does not exist'];
            }

            $pdo = new \PDO("sqlite:$indexPath");
            $stmt = $pdo->query('SELECT COUNT(*) as count FROM documents');
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'exists' => true,
                'document_count' => $result['count'],
                'file_size' => filesize($indexPath),
                'path' => $indexPath
            ];
        } catch (\Throwable $th) {
            return ['error' => $th->getMessage()];
        }
    }

    // Method untuk reindex semua dokumen
    public function reindexAllDocuments()
    {
        try {
            // Hapus index lama
            if (file_exists(storage_path($this->databasePath))) {
                unlink(storage_path($this->databasePath));
            }
            
            // Buat index baru
            $this->createIndex();
            
            // Re-index semua dokumen dari database
            $documents = \App\Models\Documents::where('isIndexed', true)->get();
            
            foreach ($documents as $document) {
                $this->createDocumentIndex(
                    $document->id, 
                    $document->url, 
                    $document->location
                );
            }
            
            Log::info("Reindexed " . $documents->count() . " documents");
            return true;
        } catch (\Throwable $th) {
            Log::error("Error reindexing: " . $th->getMessage());
            return false;
        }
    }
}