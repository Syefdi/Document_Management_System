<?php

namespace App\Repositories\Implementation;

use App\Models\Actions;
use App\Models\DocumentShareableLink;
use App\Models\Pages;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Contracts\DocumentShareableLinkRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

//use Your Model

/**
 * Class ActionsRepository.
 */
class DocumentShareableLinkRepository extends BaseRepository implements DocumentShareableLinkRepositoryInterface
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
    public static function model()
    {
        return DocumentShareableLink::class;
    }

    public function createOrUpdateLink($request)
    {
        try {

            $sharableLink = DocumentShareableLink::where([['documentId', '=', $request->documentId]])->first();

            if ($sharableLink != null) {
                // Update the existing link
                $code = $sharableLink->linkCode;
                $sharableLink->fill($request->all());
                $sharableLink->linkCode = $code;
                if (!empty($sharableLink->password)) {
                    $plainTextBytes = $sharableLink->password;
                    $sharableLink->password = base64_encode($plainTextBytes);
                }
                if ($sharableLink->linkExpiryTime) {
                    $date = $sharableLink->linkExpiryTime;
                    $sharableLink->linkExpiryTime = (new \Carbon\Carbon($date))->startOfMinute();
                }
                $sharableLink->save();
            } else {
                // Create a new link
                $sharableLink = $this->model->newInstance($request);
                $sharableLink->fill($request->all());
                $sharableLink->linkCode = base64_encode((string) Str::uuid());

                if (!empty($sharableLink->password)) {
                    $plainTextBytes = $sharableLink->password;
                    $sharableLink->password = base64_encode($plainTextBytes);
                }
                if ($sharableLink->linkExpiryTime) {
                    $date = $sharableLink->linkExpiryTime;
                    $sharableLink->linkExpiryTime = (new \Carbon\Carbon($date))->startOfMinute();
                }

                $sharableLink->save();
                $sharableLink->id = $sharableLink->id->toString();
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'error while saving document'], 500);
        }

        if (!empty($sharableLink['password'])) {
            $sharableLink['password'] = base64_decode($sharableLink['password']);
        }

        return response()->json($sharableLink, 200);
    }

    public function deleteLink($id)
    {
        $link = DocumentShareableLink::find($id);

        if ($link != null) {
            $link->delete();
            return response()->json(['message' => 'Link deleted successfully'], 200);
        } else {
            return response()->json(['message' => 'Link not found'], 404);
        }
    }

    public function getLink($id)
    {
        $link = DocumentShareableLink::where('documentId', $id)->first();

        if ($link === null) {
            return response()->json([]);
        }

        $result = $link->toArray();

        if (!empty($result['password'])) {
            $result['password'] = base64_decode($result['password']);
        }

        return response()->json($result);
    }

    public function getByCode($code)
    {
        return DocumentShareableLink::where('linkCode', $code)->first();
    }

    public function getLinkInfoByCode($id)
    {
        $link = DocumentShareableLink::where('linkCode', $id)->first();

        if ($link === null) {
            return response()->json([
                'isLinkExpired' => true,
            ]);
        }

        $linkInfo = [
            'hasPassword' => !empty($link->password),
            'linkCode' => $link->linkCode,
        ];

        if ($link->linkExpiryTime !== null) {
            $linkInfo['isLinkExpired'] = Carbon::now()->greaterThan($link->linkExpiryTime);
        } else {
            $linkInfo['isLinkExpired'] = false;
        }

        return response()->json($linkInfo);
    }

    public function getDocumentByCode($id)
    {
        try {
            $doc = DocumentShareableLink::where('linkCode', $id)
                ->with('document')
                ->first();

            if ($doc == null || $doc->document == null) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            $result = [
                'name' => $doc->document->name,
                'isAllowDownload' => $doc->isAllowDownload,
                'url' => $doc->document->url,
            ];
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function validatePassword($code, $password)
    {
        $link = DocumentShareableLink::where('linkCode', $code)->first();

        if ($link === null) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        if (base64_decode($link->password) === $password) {
            return response()->json(['message' => 'Password is correct'], 200);
        } else {
            return response()->json(['message' => 'Password is incorrect'], 401);
        }
    }

    public function getDocumentToken($token)
    {
        $link = DocumentShareableLink::where('linkCode', $token)->first();

        if ($link === null) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        return response()->json($link->document);
    }
}
