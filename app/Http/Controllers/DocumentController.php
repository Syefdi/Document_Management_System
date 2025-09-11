<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Models\Documents;
use App\Models\DocumentVersions;
use App\Models\FileRequestDocuments;
use App\Repositories\Contracts\ArchiveDocumentRepositoryInterface;
use App\Repositories\Contracts\DocumentMetaDataRepositoryInterface;
use App\Repositories\Contracts\DocumentShareableLinkRepositoryInterface;
use App\Repositories\Contracts\DocumentTokenRepositoryInterface;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private $documentRepository;
    private  $documentMetaDataRepository;
    private $documenTokenRepository;
    private $userNotificationRepository;
    private $archiveDocumentRepository;
    private $documentShareableLinkRepository;
    protected $queryString;

    public function __construct(
        DocumentRepositoryInterface $documentRepository,
        DocumentMetaDataRepositoryInterface $documentMetaDataRepository,
        UserNotificationRepositoryInterface $userNotificationRepository,
        DocumentTokenRepositoryInterface $documenTokenRepository,
        ArchiveDocumentRepositoryInterface $archiveDocumentRepository,
        DocumentShareableLinkRepositoryInterface $documentShareableLinkRepository
    ) {
        $this->documentRepository = $documentRepository;
        $this->documentMetaDataRepository = $documentMetaDataRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->documenTokenRepository = $documenTokenRepository;
        $this->archiveDocumentRepository = $archiveDocumentRepository;
        $this->documentShareableLinkRepository = $documentShareableLinkRepository;
    }

    public function getDocuments(Request $request)
    {
        $queryString = (object) $request->all();

        $count = $this->documentRepository->getDocumentsCount($queryString);
        return response()->json($this->documentRepository->getDocuments($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }

    public function officeviewer(Request $request, $id)
    {
        $documentToken = $this->documenTokenRepository->getDocumentPathByToken($id, $request);

        if ($documentToken == null) {
            return response()->json([
                'message' => 'Document Not Found.',
            ], 404);
        }

        $isPublic = filter_var($request->input('isPublic'), FILTER_VALIDATE_BOOLEAN);
        $isFileRequest = filter_var($request->input('isFileRequest'), FILTER_VALIDATE_BOOLEAN);

        if ($isPublic == true) {
            return $this->downloadSharedDocument($request, $id);
        } else if ($isFileRequest == true) {
            return $this->downloadFileRequestDocument($id);
        } else {
            return $this->downloadDocument($id, $request->input('isVersion'));
        }
    }

    public function downloadSharedDocument(Request $request, $id)
    {
        $password = '';

        if ($request->has('password')) {
            $password = $request->input('password');
        }
        $documentSharableLink = $this->documentShareableLinkRepository->getByCode($id);
        if ($documentSharableLink == null) {
            return response()->json(['error' => ['message' => 'Link Expired.']], 404);
        }
        if (!empty($documentSharableLink->password) && base64_decode($documentSharableLink->password) != $password) {
            return response()->json(['error' => ['message' => 'Password is incorrect']], 404);
        }
        return $this->downloadDocument($documentSharableLink->documentId, false);
    }

    public function downloadDocument($id, $isVersion)
    {
        $bool = filter_var($isVersion, FILTER_VALIDATE_BOOLEAN);
        if ($bool == true) {
            $file = DocumentVersions::withoutGlobalScope('isDeleted')->withTrashed()->findOrFail($id);
        } else {
            $file = Documents::withoutGlobalScope('isDeleted')->withTrashed()->findOrFail($id);
        }

        $fileupload = $file->url;
        $location = $file->location ?? 'local';

        try {
            if (Storage::disk($location)->exists($fileupload)) {
                $file_contents = Storage::disk($location)->get($fileupload);
                $fileType = Storage::mimeType($fileupload);

                $fileExtension = explode('.', $file->url);

                return response($file_contents)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $fileType)
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'attachment; filename=' . $file->name . '.' . $fileExtension[1])
                    ->header('Content-Transfer-Encoding', 'binary');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function readSharedTextDocument(Request $request, $id)
    {
        $documentSharableLink = $this->documentShareableLinkRepository->getByCode($id);
        if ($documentSharableLink == null) {
            return response()->json(['error' => ['message' => 'Link Expired.']], 404);
        }
        if (!empty($documentSharableLink->password) && base64_decode($documentSharableLink->password) != $request['password']) {
            return response()->json(['error' => ['message' => 'Password is incorrect']], 404);
        }
        return $this->readTextDocument($documentSharableLink->documentId, false);
    }

    public function readTextDocument($id, $isVersion)
    {
        $bool = filter_var($isVersion, FILTER_VALIDATE_BOOLEAN);
        if ($bool == true) {
            $file = DocumentVersions::withoutGlobalScope('isDeleted')->withTrashed()->findOrFail($id);
        } else {
            $file = Documents::withoutGlobalScope('isDeleted')->withTrashed()->findOrFail($id);
        }

        $fileupload = $file->url;
        $location = $file->location ?? 'local';

        if (Storage::disk($location)->exists($fileupload)) {
            $file_contents = Storage::disk($location)->get($fileupload);
            $response = ["result" => [$file_contents]];
            return response($response);
        }
    }

    public function saveAIDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'html_content' => ['required'],  // new required html content
            'categoryId' => ['required'],   // assuming needed
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        // Check duplicate
        $model = Documents::where([
            ['name', '=', $request->name],
            ['categoryId', '=', $request->categoryId]
        ])->first();

        if (!is_null($model)) {
            return response()->json([
                'message' => 'Document already exist with same name with same category.',
            ], 409);
        }

        $location = $request->location ?? 'local';

        // Generate PDF from HTML content
        $pdf = Pdf::loadHTML($request->html_content)->setPaper('a4');

        // Generate unique filename
        $fileName = Str::uuid() . '.pdf';

        try {
            // Save PDF file to disk storage (local or s3)
            if ($location == 's3') {
                $s3Key = config('filesystems.disks.s3.key');
                $s3Secret = config('filesystems.disks.s3.secret');
                $s3Region = config('filesystems.disks.s3.region');
                $s3Bucket = config('filesystems.disks.s3.bucket');

                if (empty($s3Key) || empty($s3Secret) || empty($s3Region) || empty($s3Bucket)) {
                    return response()->json([
                        'message' => 'Error: S3 configuration is missing',
                    ], 409);
                }

                $pdfContent = $pdf->output();

                // Use Storage facade to put file on S3
                Storage::disk('s3')->put('documents/' . $fileName, $pdfContent);

                $path = 'documents/' . $fileName;
                $fileSize = strlen($pdfContent);
            } else {
                // Local storage
                $pdf->save(storage_path('app/documents/' . $fileName));
                $path = 'documents/' . $fileName;
                $fileSize = filesize(storage_path('app/' . $path));
            }
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error in storing document in ' . $location,
            ], 409);
        }
        return $this->documentRepository->saveDocument($request, $path, $fileSize);
    }

    public function saveDocument(Request $request)
    {
        $model = Documents::where([
            ['name', '=', $request->name],
            ['categoryId', '=', $request->categoryId]
        ])->first();

        if (!is_null($model)) {
            return response()->json([
                'message' => 'Document already exist with same name with same category.',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'name'       => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 409);
        }

        if (!$request->file('uploadFile')->isValid()) {
            return response()->json([
                'message' => 'Error: ' . $request->file('uploadFile')->getErrorMessage(),
            ], 409);
        }

        $location = $request->location ?? 'local';
        $fileSize = $request->file('uploadFile')->getSize();

        try {

            if ($location == 's3') {
                $s3Key = config('filesystems.disks.s3.key');
                $s3Secret = config('filesystems.disks.s3.secret');
                $s3Region = config('filesystems.disks.s3.region');
                $s3Bucket = config('filesystems.disks.s3.bucket');

                if (empty($s3Key) || empty($s3Secret) || empty($s3Region) || empty($s3Bucket)) {
                    return response()->json([
                        'message' => 'Error: S3 configuration is missing',
                    ], 409);
                }
            }

            $path = $request->file('uploadFile')->storeAs(
                'documents',
                Uuid::uuid4() . '.' . $request->file('uploadFile')->getClientOriginalExtension(),
                $location
            );
            if ($path == null || $path == '') {
                return response()->json([
                    'message' => 'Error in storing document in ' . $location,
                ], 409);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error in storing document in ' . $location,
            ], 409);
        }
        return $this->documentRepository->saveDocument($request->all(), $path, $fileSize);
    }

    public function updateDocument(Request $request, $id)
    {
        $model = Documents::where([['name', '=', $request->name], ['categoryId', '=', $request->categoryId], ['id', '<>', $id]])->first();

        if (!is_null($model)) {
            return response()->json([
                'message' => 'Document already exist.',
            ], 409);
        }
        return  response()->json($this->documentRepository->updateDocument($request, $id), 200);
    }

    public function archiveDocument($id)
    {
        return $this->documentRepository->archiveDocument($id);
    }

    public function deleteDocument($id)
    {
        return $this->archiveDocumentRepository->deleteDocument($id);
    }

    public function getDocumentMetatags($id)
    {
        return  response($this->documentMetaDataRepository->getDocumentMetadatas($id), 200);
    }

    public function assignedDocuments(Request $request)
    {
        $queryString = (object) $request->all();
        // dd($queryString);

        $count = $this->documentRepository->assignedDocumentsCount($queryString);
        return response()->json($this->documentRepository->assignedDocuments($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }

    public function getDocumentsByCategoryQuery()
    {
        return response()->json($this->documentRepository->getDocumentByCategory());
    }

    public function getDocumentbyId($id)
    {
        return response()->json($this->documentRepository->getDocumentbyId($id));
    }

    public function getDeepSearchDocuments(Request $request)
    {
        $queryString = (object) $request->all();
        return response()->json($this->documentRepository->getDeepSearchDocuments($queryString));
    }

    public function addDOocumentToDeepSearch($id)
    {
        return response()->json($this->documentRepository->addDOocumentToDeepSearch($id));
    }

    public function removeDocumentFromDeepSearch($id)
    {
        return response()->json($this->documentRepository->removeDocumentFromDeepSearch($id));
    }

    // Tambahkan method ini di controller atau buat controller terpisah untuk debugging

public function debugIndexContent()
{
    try {
        $indexer = app(\App\Services\DocumentIndexer::class);
        
        // 1. Cek stats index
        $stats = $indexer->getIndexStats();
        
        // 2. Cek dokumen di database yang seharusnya diindeks
        $documentsInDb = \App\Models\Documents::where('isIndexed', true)->count();
        
        // 3. Cek konten index secara langsung dari SQLite
        $indexPath = storage_path('app/search_index/documents.index');
        $indexContent = [];
        
        if (file_exists($indexPath)) {
            $pdo = new \PDO("sqlite:$indexPath");
            
            // Cek tabel yang ada
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();
            
            // Cek isi tabel documents jika ada
            try {
                $documents = $pdo->query("SELECT id, substr(content, 1, 100) as content_preview FROM documents LIMIT 5")->fetchAll();
                $indexContent['sample_documents'] = $documents;
            } catch (\Exception $e) {
                $indexContent['error'] = $e->getMessage();
            }
            
            // Cek total dokumen di index
            try {
                $totalDocs = $pdo->query("SELECT COUNT(*) as count FROM documents")->fetch();
                $indexContent['total_documents'] = $totalDocs['count'];
            } catch (\Exception $e) {
                $indexContent['count_error'] = $e->getMessage();
            }
            
            $indexContent['tables'] = $tables;
        }
        
        // 4. Test ekstraksi konten dari satu dokumen
        $sampleDocument = \App\Models\Documents::where('isIndexed', true)->first();
        $extractionTest = null;
        
        if ($sampleDocument) {
            $extractor = app(\App\Services\FileContentExtractor::class);
            $extractedContent = $extractor->extractContent($sampleDocument->url, $sampleDocument->location);
            $extractionTest = [
                'document_id' => $sampleDocument->id,
                'document_name' => $sampleDocument->name,
                'document_url' => $sampleDocument->url,
                'document_location' => $sampleDocument->location,
                'content_length' => strlen($extractedContent ?? ''),
                'content_preview' => substr($extractedContent ?? '', 0, 200),
                'file_exists' => file_exists($sampleDocument->location . '/' . $sampleDocument->url)
            ];
        }
        
        return response()->json([
            'index_stats' => $stats,
            'documents_in_db' => $documentsInDb,
            'index_content' => $indexContent,
            'extraction_test' => $extractionTest
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
public function debugFileExtractor($documentId)
{
    try {
        $document = \App\Models\Documents::find($documentId);
        if (!$document) {
            return response()->json(['error' => 'Document not found']);
        }
        
        $extractor = app(\App\Services\FileContentExtractor::class);
        
        // Path yang sudah kita ketahui
        $filePath = "C:\\1project\\Document_Management_System\\storage\\app\\documents\\373c98ea-2828-47a4-8703-a523c4bca6c5.pdf";
        
        $result = [
            'document' => [
                'id' => $document->id,
                'name' => $document->name,
                'url' => $document->url,
                'location' => $document->location
            ],
            'file_info' => [
                'path' => $filePath,
                'exists' => file_exists($filePath),
                'size' => file_exists($filePath) ? filesize($filePath) : 0,
                'readable' => file_exists($filePath) ? is_readable($filePath) : false,
                'mime_type' => file_exists($filePath) ? mime_content_type($filePath) : null,
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION)
            ],
            'extraction_tests' => []
        ];
        
        if (file_exists($filePath)) {
            // Test 1: Direct path extraction
            try {
                $content1 = $extractor->extractContent($document->url, $document->location);
                $result['extraction_tests']['original_method'] = [
                    'success' => !empty($content1),
                    'content_length' => strlen($content1 ?? ''),
                    'preview' => substr($content1 ?? '', 0, 200),
                    'error' => null
                ];
            } catch (\Exception $e) {
                $result['extraction_tests']['original_method'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            // Test 2: Corrected path extraction
            try {
                $pathInfo = pathinfo($filePath);
                $content2 = $extractor->extractContent($pathInfo['basename'], $pathInfo['dirname']);
                $result['extraction_tests']['corrected_path'] = [
                    'success' => !empty($content2),
                    'content_length' => strlen($content2 ?? ''),
                    'preview' => substr($content2 ?? '', 0, 200),
                    'used_filename' => $pathInfo['basename'],
                    'used_directory' => $pathInfo['dirname'],
                    'error' => null
                ];
            } catch (\Exception $e) {
                $result['extraction_tests']['corrected_path'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            // Test 3: Direct file path (full path)
            try {
                // Jika FileContentExtractor punya method untuk full path
                $content3 = $extractor->extractContent($filePath, '');
                $result['extraction_tests']['full_path'] = [
                    'success' => !empty($content3),
                    'content_length' => strlen($content3 ?? ''),
                    'preview' => substr($content3 ?? '', 0, 200),
                    'error' => null
                ];
            } catch (\Exception $e) {
                $result['extraction_tests']['full_path'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            // Test 4: Manual PDF extraction jika ada library
            $result['extraction_tests']['manual_pdf'] = $this->testManualPdfExtraction($filePath);
            
            // Test 5: Check dependencies
            $result['dependencies_check'] = [
                'pdf_parser_exists' => class_exists('Smalot\\PdfParser\\Parser'),
                'phpword_exists' => class_exists('PhpOffice\\PhpWord\\IOFactory'),
                'phpspreadsheet_exists' => class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory'),
                'extensions_loaded' => [
                    'zip' => extension_loaded('zip'),
                    'xml' => extension_loaded('xml'),
                    'mbstring' => extension_loaded('mbstring')
                ]
            ];
        }
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

private function testManualPdfExtraction($filePath)
{
    try {
        // Test dengan berbagai library PDF
        
        // 1. Test dengan smalot/pdfparser jika tersedia
        if (class_exists('Smalot\\PdfParser\\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
                
                return [
                    'method' => 'smalot/pdfparser',
                    'success' => !empty($text),
                    'content_length' => strlen($text),
                    'preview' => substr($text, 0, 200)
                ];
            } catch (\Exception $e) {
                return [
                    'method' => 'smalot/pdfparser',
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // 2. Test dengan exec pdftotext jika tersedia
        if (function_exists('exec')) {
            $output = [];
            $return_var = 0;
            exec("pdftotext \"$filePath\" -", $output, $return_var);
            
            if ($return_var === 0 && !empty($output)) {
                $text = implode("\n", $output);
                return [
                    'method' => 'pdftotext command',
                    'success' => !empty($text),
                    'content_length' => strlen($text),
                    'preview' => substr($text, 0, 200)
                ];
            }
        }
        
        // 3. Basic file reading (untuk file teks)
        if (pathinfo($filePath, PATHINFO_EXTENSION) === 'txt') {
            $content = file_get_contents($filePath);
            return [
                'method' => 'file_get_contents',
                'success' => !empty($content),
                'content_length' => strlen($content),
                'preview' => substr($content, 0, 200)
            ];
        }
        
        return [
            'method' => 'none available',
            'success' => false,
            'error' => 'No PDF extraction method available'
        ];
        
    } catch (\Exception $e) {
        return [
            'method' => 'manual extraction',
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Method untuk memperbaiki path di database
public function fixDocumentPaths()
{
    try {
        $documents = \App\Models\Documents::where('isIndexed', true)->get();
        $fixed = 0;
        
        foreach ($documents as $document) {
            // Path saat ini salah: local/documents/file.pdf
            // Path yang benar: documents/file.pdf
            if ($document->location === 'local' && strpos($document->url, 'documents/') === 0) {
                // Update path di database
                $document->location = 'documents';
                $document->url = str_replace('documents/', '', $document->url);
                $document->save();
                $fixed++;
            }
        }
        
        return response()->json([
            'message' => "Fixed $fixed document paths",
            'total_documents' => $documents->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
}
// Method untuk force reindex satu dokumen dengan logging detail
public function forceReindexDocument($documentId)
{
    try {
        $document = \App\Models\Documents::find($documentId);
        if (!$document) {
            return response()->json(['error' => 'Document not found']);
        }
        
        $indexer = app(\App\Services\DocumentIndexer::class);
        $extractor = app(\App\Services\FileContentExtractor::class);
        
        // Original path
        $originalPath = $document->location . '/' . $document->url;
        
        // Semua kemungkinan lokasi file
        $possiblePaths = [
            // Original path
            $originalPath,
            
            // Storage paths
            storage_path($document->location . '/' . $document->url),
            storage_path('app/' . $document->location . '/' . $document->url),
            storage_path('app/public/' . $document->location . '/' . $document->url),
            
            // Public paths
            public_path($document->location . '/' . $document->url),
            public_path('storage/' . $document->location . '/' . $document->url),
            public_path('uploads/' . $document->url),
            
            // Base paths
            base_path($document->location . '/' . $document->url),
            base_path('storage/app/' . $document->location . '/' . $document->url),
            
            // Only filename variants
            storage_path('app/' . $document->url),
            storage_path('app/uploads/' . $document->url),
            storage_path('app/documents/' . $document->url),
            public_path($document->url),
            public_path('documents/' . $document->url),
            public_path('uploads/' . $document->url),
        ];
        
        // Cari file yang ada
        $foundPaths = [];
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $foundPaths[] = [
                    'path' => $path,
                    'size' => filesize($path),
                    'readable' => is_readable($path),
                    'modified' => date('Y-m-d H:i:s', filemtime($path))
                ];
            }
        }
        
        // Cari file dengan pattern matching
        $searchPatterns = [
            // Cari semua file PDF di direktori storage
            storage_path('app/**/*.pdf'),
            storage_path('app/**/373c98ea-2828-47a4-8703-a523c4bca6c5.pdf'),
            public_path('**/*.pdf'),
            // Cari berdasarkan nama asli
            storage_path('app/**/2mb_compressed.pdf'),
        ];
        
        $patternMatches = [];
        foreach ($searchPatterns as $pattern) {
            $matches = glob($pattern, GLOB_BRACE);
            if ($matches) {
                $patternMatches = array_merge($patternMatches, $matches);
            }
        }
        
        // Scan direktori storage untuk file yang mirip
        $storageDirectories = [
            storage_path('app'),
            storage_path('app/local'),
            storage_path('app/local/documents'),
            storage_path('app/documents'),
            storage_path('app/uploads'),
            public_path('storage'),
            public_path('documents'),
            public_path('uploads'),
        ];
        
        $foundFiles = [];
        foreach ($storageDirectories as $dir) {
            if (is_dir($dir)) {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if (strpos($file, '373c98ea-2828-47a4-8703-a523c4bca6c5') !== false 
                        || strpos($file, '2mb_compressed') !== false) {
                        $fullPath = $dir . '/' . $file;
                        if (is_file($fullPath)) {
                            $foundFiles[] = [
                                'file' => $file,
                                'path' => $fullPath,
                                'size' => filesize($fullPath),
                                'directory' => $dir
                            ];
                        }
                    }
                }
            }
        }
        
        // Test content extraction jika file ditemukan
        $content = null;
        $extractionError = null;
        $usedPath = null;
        
        if (!empty($foundPaths)) {
            $usedPath = $foundPaths[0]['path'];
            try {
                // Ekstrak dengan path yang ditemukan
                $pathInfo = pathinfo($usedPath);
                $content = $extractor->extractContent($pathInfo['basename'], $pathInfo['dirname']);
            } catch (\Exception $e) {
                $extractionError = $e->getMessage();
            }
        }
        
        // Force reindex jika berhasil ekstrak konten
        $reindexSuccess = false;
        if ($content) {
            try {
                $indexer->deleteDocumentIndex($document->id);
                $indexer->addDocumentIndex($document->id, $content);
                $reindexSuccess = true;
            } catch (\Exception $e) {
                $extractionError = ($extractionError ? $extractionError . '; ' : '') . $e->getMessage();
            }
        }
        
        // Test search after reindex
        $searchTests = [];
        if ($reindexSuccess) {
            $searchTests = [
                'search_test' => $indexer->search('test', 5),
                'search_pdf' => $indexer->search('pdf', 5),
                'search_document' => $indexer->search('document', 5),
            ];
            
            // Search dengan kata dari konten
            if ($content) {
                $words = str_word_count(substr($content, 0, 500), 1);
                if (!empty($words)) {
                    $searchTests['search_first_word'] = $indexer->search($words[0], 5);
                }
            }
        }
        
        return response()->json([
            'document' => [
                'id' => $document->id,
                'name' => $document->name,
                'url' => $document->url,
                'location' => $document->location,
                'is_indexed' => $document->isIndexed,
                'original_path' => $originalPath
            ],
            'file_search' => [
                'checked_paths' => count($possiblePaths),
                'found_paths' => $foundPaths,
                'pattern_matches' => $patternMatches,
                'similar_files' => $foundFiles,
                'used_path' => $usedPath
            ],
            'content_extraction' => [
                'success' => !empty($content),
                'content_length' => strlen($content ?? ''),
                'content_preview' => substr($content ?? '', 0, 200),
                'extraction_error' => $extractionError
            ],
            'reindex_result' => [
                'success' => $reindexSuccess,
                'search_tests' => $searchTests
            ],
            'debug_info' => [
                'storage_path' => storage_path(),
                'public_path' => public_path(),
                'base_path' => base_path(),
                'current_working_directory' => getcwd()
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

// Method untuk manually populate index dari database
public function manuallyPopulateIndex()
{
    try {
        $indexer = app(\App\Services\DocumentIndexer::class);
        $extractor = app(\App\Services\FileContentExtractor::class);
        
        // Ambil dokumen yang seharusnya diindeks
        $documents = \App\Models\Documents::where('isIndexed', true)->limit(10)->get();
        
        $results = [];
        
        foreach ($documents as $document) {
            $fullPath = $document->location . '/' . $document->url;
            $fileExists = file_exists($fullPath);
            
            $result = [
                'id' => $document->id,
                'name' => $document->name,
                'file_exists' => $fileExists,
                'indexed' => false,
                'content_length' => 0
            ];
            
            if ($fileExists) {
                $content = $extractor->extractContent($document->url, $document->location);
                if ($content) {
                    $indexer->addDocumentIndex($document->id, $content);
                    $result['indexed'] = true;
                    $result['content_length'] = strlen($content);
                }
            }
            
            $results[] = $result;
        }
        
        // Test search setelah populate
        $searchTest = $indexer->search('pdf', 10);
        
        return response()->json([
            'processed_documents' => $results,
            'search_test_after_populate' => $searchTest
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
}
public function testReindexWithCorrectPath($documentId)
{
    try {
        $document = \App\Models\Documents::find($documentId);
        if (!$document) {
            return response()->json(['error' => 'Document not found']);
        }
        
        $indexer = app(\App\Services\DocumentIndexer::class);
        
        Log::info("=== TESTING REINDEX FOR DOCUMENT $documentId ===");
        
        // 1. Hapus index lama jika ada
        $indexer->deleteDocumentIndex($document->id);
        
        // 2. Index ulang dengan path asli dari database
        $indexer->createDocumentIndex($document->id, $document->url, $document->location);
        
        // 3. Test pencarian
        $searchTests = [
            'search_pdf' => $indexer->search('pdf', 10),
            'search_sample' => $indexer->search('sample', 10),
            'search_file' => $indexer->search('file', 10),
            'search_document' => $indexer->search('document', 10),
            'search_download' => $indexer->search('download', 10)
        ];
        
        // 4. Cek stats index
        $stats = $indexer->getIndexStats();
        
        return response()->json([
            'document' => [
                'id' => $document->id,
                'name' => $document->name,
                'url' => $document->url,
                'location' => $document->location
            ],
            'reindex_completed' => true,
            'search_tests' => $searchTests,
            'index_stats' => $stats,
            'success' => true
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

// Method untuk reindex semua dokumen yang ada
public function reindexAllDocuments()
{
    try {
        $indexer = app(\App\Services\DocumentIndexer::class);
        
        // Ambil semua dokumen yang seharusnya diindeks
        $documents = \App\Models\Documents::where('isIndexed', true)->get();
        
        $results = [
            'total_documents' => $documents->count(),
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'details' => []
        ];
        
        foreach ($documents as $document) {
            $results['processed']++;
            
            try {
                // Hapus index lama
                $indexer->deleteDocumentIndex($document->id);
                
                // Index ulang
                $indexer->createDocumentIndex($document->id, $document->url, $document->location);
                
                $results['successful']++;
                $results['details'][] = [
                    'id' => $document->id,
                    'name' => $document->name,
                    'status' => 'success'
                ];
                
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'id' => $document->id,
                    'name' => $document->name,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Test search setelah reindex semua
        $searchTests = [
            'search_pdf' => $indexer->search('pdf', 10),
            'search_document' => $indexer->search('document', 10),
            'search_file' => $indexer->search('file', 10),
            'search_sample' => $indexer->search('sample', 10)
        ];
        
        $results['final_search_tests'] = $searchTests;
        $results['index_stats'] = $indexer->getIndexStats();
        
        return response()->json($results);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
}

    public function downloadFileRequestDocument($id)
    {
        $file = FileRequestDocuments::findOrFail($id);

        $fileupload = $file->url;
        $location = 'local';

        try {
            if (Storage::disk($location)->exists($fileupload)) {
                $file_contents = Storage::disk($location)->get($fileupload);
                $fileType = Storage::mimeType($fileupload);

                $fileExtension = explode('.', $file->url);

                return response($file_contents)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $fileType)
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'attachment; filename=' . $file->name . '.' . $fileExtension[1])
                    ->header('Content-Transfer-Encoding', 'binary');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
