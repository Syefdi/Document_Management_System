<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\FileRequestDocumentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Contracts\FileRequestRepositoryInterface;


class FileRequestController extends Controller
{
    private $fileRequestRepository;

    public function __construct(
        FileRequestRepositoryInterface $fileRequestRepository,
    ) {
        $this->fileRequestRepository = $fileRequestRepository;
    }


    public function getFileRequests(Request $request)
    {
        $queryString = (object) $request->all();

        $count = $this->fileRequestRepository->getFileRequestCount($queryString);
        return response()->json($this->fileRequestRepository->getFileRequest($queryString))
            ->withHeaders(['totalCount' => $count, 'pageSize' => $queryString->pageSize, 'skip' => $queryString->skip]);
    }

    public function get($id)
    {
        $fileRequest = $this->fileRequestRepository->find($id);
        $entityDto = [
            'id' => $fileRequest->id,
            'subject' => $fileRequest->subject,
            'email' => $fileRequest->email,
            'sizeInMb' => $fileRequest->sizeInMb,
            'maxDocument' => $fileRequest->maxDocument,
            'fileRequestStatus' => $fileRequest->fileRequestStatus,
            'createdBy' => $fileRequest->createdByUser ? "{$fileRequest->createdByUser->firstName} {$fileRequest->createdByUser->lastName}" : null,
            'allowExtension' => $fileRequest->allowExtension,
            'createdDate' => $fileRequest->createdDate,
            'linkExpiryTime' =>  $fileRequest->linkExpiryTime,
            'isMaxDocumentReached' => $fileRequest->isMaxDocumentReached,
            'isLinkExpired' => $fileRequest->isLinkExpired,
            'hasPassword' => !empty($fileRequest->password) ? true : false,
            'password' => !empty($fileRequest->password) ? '*****' : '',
            'fileRequestDocuments' => $fileRequest->fileRequestDocuments,
        ];
        return response($entityDto, 201);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return  $this->fileRequestRepository->createFileRequest($request->all());
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return  $this->fileRequestRepository->updateFileRequest($request->all(), $id);
    }

    public function delete($id)
    {
        return response($this->fileRequestRepository->delete($id), 204);
    }


    public function varifyPassword(Request $request, $id)
    {
        // echo $request->query('password') ."test";
        // $validator = Validator::make($request->all(), [
        //     'password' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return $validator->errors();
        // }

        return  $this->fileRequestRepository->varifyPassword($request->query('password'), $id);
    }

    public function getFileRequestData($id)
    {
        return $this->fileRequestRepository->getFileRequestData($id);
    }
}
