<?php

namespace App\Services;

use TeamTNT\TNTSearch\TNTSearch;

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
            // Check if the index already exists or create it
            if (!file_exists(storage_path($this->databasePath))) {
                $indexer = $this->tnt->createIndex($this->indexName); // Create the index file
                $indexer->setPrimaryKey('id'); // Set the primary key field (even if UUID, it will work as string)
            } else {
                $this->tnt->selectIndex($this->indexName); // Load the existing index for future operations
            }
        } catch (\Throwable $th) {
        }
    }

    public function createDocumentIndex($id, $path, $location)
    {
        try {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, ['txt', 'pdf', 'docx', 'doc', 'xlsx', 'xls'])) {
                $content = $this->extractor->extractContent($path, $location);
                if ($content) {
                    $this->addDocumentIndex($id, $content);
                }
            }
        } catch (\Throwable $th) {
            //Ignore the error
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
        } catch (\Throwable $th) {
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
        } catch (\Throwable $th) {
        }
    }

    public function search($query, $limit)
    {
        try {
            $this->tnt->selectIndex($this->indexName);

            $results = $this->tnt->search($query, $limit);
            return $results;
        } catch (\Throwable $th) {
            return [];
        }
    }

    public function optimizeIndex()
    {
        try {
            $indexPath = storage_path($this->databasePath);
            $pdo = new \PDO("sqlite:$indexPath");
            $pdo->exec('VACUUM');
        } catch (\Throwable $th) {
            //ignore the error
        }
    }
}
