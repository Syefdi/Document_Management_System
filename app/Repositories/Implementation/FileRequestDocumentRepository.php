<?php

namespace App\Repositories\Implementation;

use App\Models\FileRequestDocuments;
use App\Models\FileRequestDocumentStatusEnum;
use App\Models\FileRequests;
use App\Repositories\Contracts\FileRequestDocumentRepositoryInterface;
use App\Repositories\Implementation\BaseRepository;
use Carbon\Carbon;
use App\Models\FileRequestStatusEnum;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use App\Services\DocumentIndexer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileRequestDocumentRepository extends BaseRepository implements FileRequestDocumentRepositoryInterface
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */

    protected $indexer;
    protected $documentRepository;

    public function __construct(
        DocumentIndexer $indexer,
        DocumentRepositoryInterface $documentRepository
    ) {
        parent::__construct();
        $this->indexer = $indexer;
        $this->documentRepository = $documentRepository;
    }

    public static function model()
    {
        return FileRequestDocuments::class;
    }

    public function saveDocument($request, $path, $fileRequestId, $name)
    {
        try {

            $fileRequest = FileRequests::findOrFail($fileRequestId);

            if ($fileRequest == null) {
                return response()->json([
                    'message' => 'File Request does not exists.',
                ], 409);
            }

            $model = $this->model->newInstance($request);
            $model->url = $path;
            $model->name = $name;
            $model->fileRequestId = $fileRequestId;
            $model->fileRequestDocumentStatus = FileRequestDocumentStatusEnum::PENDING->value;
            $model->createdDate = Carbon::now();
            $model->save();
            $this->resetModel();
            $result = $this->parseResult($model);

            $fileRequest->fileRequestStatus = FileRequestStatusEnum::UPLOADED->value;
            $fileRequest->save();
            return response()->json((string)$result->id, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in saving data.',
            ], 409);
        }
    }

    public function getFileRequestDocumentData($id)
    {
        $query = FileRequestDocuments::where('fileRequestId', '=', $id)
        ->orderby('createdDate', 'desc')
        ->get();
        return $query;
    }

    public function approveDocument($request)
    {
        try {
            $fileRequestDocuments = FileRequestDocuments::findOrFail($request->fileRequestDocumentId);
            if ($fileRequestDocuments == null) {
                return response()->json([
                    'message' => 'Document does not exists.',
                ], 409);
            }

            $fileRequestDocuments->fileRequestDocumentStatus = FileRequestDocumentStatusEnum::APPROVED->value;

            if (!Storage::disk('local')->exists($fileRequestDocuments->url)) {
                return response()->json([
                    'message' => 'Document does not exists.',
                ], 409);
            }

            if ($request->location == 's3') {

                $s3Key = config('filesystems.disks.s3.key');
                $s3Secret = config('filesystems.disks.s3.secret');
                $s3Region = config('filesystems.disks.s3.region');
                $s3Bucket = config('filesystems.disks.s3.bucket');

                if (empty($s3Key) || empty($s3Secret) || empty($s3Region) || empty($s3Bucket)) {
                    return response()->json([
                        'message' => 'Error: S3 configuration is missing',
                    ], 409);
                }

                $localPath = Storage::disk('local')->path($fileRequestDocuments->url);
                try {
                    $file = fopen(Storage::disk('local')->path($localPath), 'r');
                    $moved = Storage::disk('s3')->put('documents', $file);
                    if (!$moved) {
                        return response()->json([
                            'message' => 'Error while moving document from local to ' . $request->location,
                        ], 409);
                    }
                } catch (\Throwable $th) {
                    return response()->json([
                        'message' => 'Error while moving document from local to ' . $request->location,
                    ], 409);
                }
            }

            // skip index for large files.
            $fileSize = Storage::disk('local')->size($fileRequestDocuments->url);
            $this->documentRepository->saveDocument($request, $fileRequestDocuments->url, $fileSize);

            $fileRequestDocuments->approvedRejectedDate = Carbon::now();
            $fileRequestDocuments->approvalOrRejectedById = Auth::parseToken()->getPayload()->get('userId');;
            $fileRequestDocuments->save();

            return response()->json([], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in saving data.' . $e->getMessage(),
            ], 409);
        }
    }

    public function rejectDocument($request)
    {
        try {
            $fileRequestDocuments = FileRequestDocuments::findOrFail($request->id);
            if ($fileRequestDocuments == null) {
                return response()->json([
                    'message' => 'Document does not exists.',
                ], 409);
            }

            $fileRequestDocuments->fileRequestDocumentStatus = FileRequestDocumentStatusEnum::REJECTED->value;
            $fileRequestDocuments->approvedRejectedDate = Carbon::now();
            $fileRequestDocuments->approvalOrRejectedById = Auth::parseToken()->getPayload()->get('userId');;
            $fileRequestDocuments->reason = $request->reason;
            $fileRequestDocuments->save();
            return response()->json([], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in saving data.' . $e->getMessage(),
            ], 409);
        }
    }
}
