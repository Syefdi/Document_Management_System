<?php

namespace App\Repositories\Implementation;

use App\Models\AllowFileExtensions;
use App\Models\AllowFileTypeEnum;
use App\Models\FileRequests;
use App\Models\Users;
use App\Repositories\Contracts\EmailRepositoryInterface;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\FileRequestRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use User;

class FileRequestRepository extends BaseRepository implements FileRequestRepositoryInterface
{

    /**
     * @var Model
     */
    protected $model;
    protected $emailRepository;

    function __construct(EmailRepositoryInterface $emailRepository)
    {
        parent::__construct();
        $this->emailRepository = $emailRepository;
    }


    public static function model()
    {
        return FileRequests::class;
    }

    public function createFileRequest($attribute)
    {
        $model = $this->model->newInstance($attribute);

        if (!empty($attribute['password'])) {
            $model->password = base64_encode($attribute['password']);
        }

        $fileExtensions = '';

        foreach ($attribute['fileExtension'] as $item) {
            $fileExtension = AllowFileExtensions::where('fileType', (int)$item)->first();
            if ($fileExtension) {
                $fileExtensions .= AllowFileTypeEnum::getName($fileExtension->fileType) . ',';
            }
        }


        // Trim the trailing comma if there are any extensions
        $fileExtensions = rtrim($fileExtensions, ',');

        $model->allowExtension = $fileExtensions;

        $model->save();
        $this->resetModel();
        $result = $this->parseResult($model);

        if ($attribute['email']) {
            $this->sendEmail($attribute, (string)$result->id);
        }
        return response($result, 201);
    }

    public function updateFileRequest($attribute, $id)
    {
        $entityExist = $this->model->findOrFail($id);
        $oldPassword = $entityExist->password;

        $filledObject = $entityExist->fill($attribute);
        if (!empty($attribute['password']) && $attribute['password'] != "*****") {
            $filledObject->password = base64_encode($attribute['password']);
        } else {
            $filledObject->password = $oldPassword;
        }

        $fileExtensions = '';
        foreach ($attribute['fileExtension'] as $item) {
            $fileExtension = AllowFileExtensions::where('fileType', (int)$item)->first();
            if ($fileExtension) {
                $fileExtensions .= AllowFileTypeEnum::getName($fileExtension->fileType) . ',';
            }
        }
        $fileExtensions = rtrim($fileExtensions, ',');
        $filledObject->allowExtension = $fileExtensions;

        $filledObject->save();
        $result = $this->parseResult($filledObject);

        return response($result, 201);
    }

    public function getFileRequest($attributes)
    {
        $query = FileRequests::select([
            'fileRequests.id',
            'fileRequests.subject',
            'fileRequests.email',
            'fileRequests.maxDocument',
            'fileRequests.sizeInMb',
            'fileRequests.fileRequestStatus',
            'fileRequests.allowExtension',
            'fileRequests.linkExpiryTime',
            'fileRequests.createdDate',
            DB::raw("CONCAT(users.firstName,' ', users.lastName) as createdByName")
        ])->leftjoin('users', 'fileRequests.createdBy', '=', 'users.id');

        $orderByArray =  explode(' ', $attributes->orderBy);
        $orderBy = $orderByArray[0];
        $direction = $orderByArray[1] ?? 'asc';

        if ($orderBy == 'subject') {
            $query = $query->orderBy('subject', $direction);
        } else if ($orderBy == 'email') {
            $query = $query->orderBy('email', $direction);
        } else if ($orderBy == 'maxDocument') {
            $query = $query->orderBy('maxDocument', $direction);
        } else if ($orderBy == 'allowExtension') {
            $query = $query->orderBy('allowExtension', $direction);
        } else if ($orderBy == 'linkExpiryTime') {
            $query = $query->orderBy('linkExpiryTime', $direction);
        }

        if ($attributes->subject) {
            $query = $query->where('subject',  'like', '%' . $attributes->subject . '%');
        }

        if ($attributes->email) {
            $query = $query->where('email', 'like', '%' . $attributes->message . '%');
        }

        $results = $query->skip($attributes->skip)->take($attributes->pageSize)->get();
        return $results;
    }

    public function getFileRequestCount($attributes)
    {
        $query = FileRequests::query();

        if ($attributes->subject) {
            $query = $query->where('subject',  'like', '%' . $attributes->subject . '%');
        }

        if ($attributes->email) {
            $query = $query->where('email', 'like', '%' . $attributes->message . '%');
        }

        $count = $query->count();
        return $count;
    }

    public function varifyPassword($password, $id)
    {
        $model = $this->model->where('id', $id)->first();
        if ($model == null) {
            return response()->json([
                'message' => 'Invalid password.',
            ], 409);
        }

        if (empty($model->password)) {
            return response()->json([], 200);
        }

        if (base64_decode($model->password) == $password) {
            return response()->json([], 200);
        }

        return response()->json([
            'message' => 'Invalid password.',
        ], 409);
    }

    public function getFileRequestData($id)
    {
        $entity = FileRequests::with(['createdByUser', 'fileRequestDocuments'])
            ->findOrFail($id);

        $isMaxDocumentReached = $entity->fileRequestDocuments->count() >= $entity->maxDocument;

        $isLinkExpired = false;

        if ($entity->linkExpiryTime) {
            $isLinkExpired = Carbon::now() > $entity->linkExpiryTime;

            if ($isLinkExpired) {
                return response(['message' => "Link is expired."], 409);
            }
        }

        $fileRequestDocuments = $entity->fileRequestDocuments->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'url' => $item->url,
                'createdDate' => $item->createdDate,
                'fileRequestId' => $item->fileRequestId,
                'fileRequestDocumentStatus' => $item->fileRequestDocumentStatus,
            ];
        });

        $entityDto = [
            'id' => $entity->id,
            'subject' => $entity->subject,
            'email' => $entity->email,
            'sizeInMb' => $entity->sizeInMb,
            'maxDocument' => $entity->maxDocument,
            'fileRequestStatus' => $entity->fileRequestStatus,
            'createdByName' => $entity->createdByUser ? "{$entity->createdByUser->firstName} {$entity->createdByUser->lastName}" : null,
            'allowExtension' => $entity->allowExtension,
            'createdDate' => $entity->createdDate,
            'linkExpiryTime' =>  $entity->linkExpiryTime,
            'isMaxDocumentReached' => $isMaxDocumentReached,
            'isLinkExpired' => $isLinkExpired,
            'hasPassword' => !empty($entity->password) ? true : false,
            'fileRequestDocuments' => $fileRequestDocuments,
        ];
        return response($entityDto, 200);
    }

    private function sendEmail($request, $id)
    {
        try {
            $body = Storage::disk('public')->get('file-request-template.html');
            $userId = Auth::parseToken()->getPayload()->get('userId');
            $user = Users::find($userId);
            $fromName = $user->firstName . ' ' . $user->lastName;

            $url =  $request["baseUrl"] . $id;
            $body = str_replace("##FROM_NAME##", $fromName, $body);
            $body = str_replace("##UPLOAD_LINK##", $url, $body);
            $message = [
                'to_address' => $request['email'],
                'subject' => $fromName . ' has requested you to upload a file.',
                'message' => $body,
                'path' => null,
            ];

            $this->emailRepository->sendEmail($message);
        } catch (\Exception $e) {
        }
    }
}
