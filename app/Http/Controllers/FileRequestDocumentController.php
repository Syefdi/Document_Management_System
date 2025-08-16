<?php

namespace App\Http\Controllers;

use App\Models\FileRequestDocuments;
use App\Repositories\Contracts\FileRequestDocumentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class FileRequestDocumentController extends Controller
{
    private $fileRequestDocumentRepository;

    public function __construct(
        FileRequestDocumentRepositoryInterface $fileRequestDocumentRepository
    ) {
        $this->fileRequestDocumentRepository = $fileRequestDocumentRepository;
    }

    public function saveDocument(Request $request, $id)
    {
        $request->validate([
            'files' => 'required|array'           // Ensure it's an array
        ]);

        $location = 'local';
        $storedFiles = [];
        $files = $request->file('files');
        $names = $request->input('names');

        try {
            foreach ($files as $index => $file) {

                $path = $file->storeAs(
                    'documents',
                    Uuid::uuid4() . '.' . $file->getClientOriginalExtension(),
                    $location
                );
                if ($path == null || $path == '') {
                    return response()->json([
                        'message' => 'Error in storing document in ' . $location,
                    ], 409);
                }
                $storedFiles[] = $path;
                $this->fileRequestDocumentRepository->saveDocument($request, $path, $id, $names[$index]);
            }

            return response()->json([], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error processing files.',
                'error' => $th->getMessage(),
            ], 400);  // Return a 400 Bad Request error
        }
    }

    public function getFileRequestDocumentData($id)
    {
        return $this->fileRequestDocumentRepository->getFileRequestDocumentData($id);
    }

    public function approveDocument(Request $request)
    {
        return $this->fileRequestDocumentRepository->approveDocument($request);
    }

    public function rejectDocument(Request $request)
    {
        return $this->fileRequestDocumentRepository->rejectDocument($request);
    }

    public function downloadDocument($id)
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

    public function readTextDocument($id)
    {
        $file = FileRequestDocuments::findOrFail($id);

        $fileupload = $file->url;
        $location = 'local';

        if (Storage::disk($location)->exists($fileupload)) {
            $file_contents = Storage::disk($location)->get($fileupload);
            $response = ["result" => [$file_contents]];
            return response($response);
        }
    }
}
