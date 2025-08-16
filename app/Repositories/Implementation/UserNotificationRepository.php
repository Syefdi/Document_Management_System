<?php

namespace App\Repositories\Implementation;

use App\Models\UserNotifications;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Implementation\BaseRepository;
use App\Repositories\Exceptions\RepositoryException;

//use Your Model

/**
 * Class UserRepository.
 */
class UserNotificationRepository extends BaseRepository implements UserNotificationRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor..
     *
     *
     * @param Model $model
     */


    public static function model()
    {
        return UserNotifications::class;
    }


    public function getTop10Notification()
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        // \Log::info("getTop10Notification - Current user ID: {$userId}");
        
        if ($userId == null) {
            // \Log::warning("getTop10Notification - User ID is null");
            return [];
        }

        // DEBUG: Cek total notifikasi tanpa filter dokumen
        $totalNotifications = UserNotifications::where('userId', $userId)->count();
        // \Log::info("DEBUG - Total notifications for user (no filters): {$totalNotifications}");
        
        // DEBUG: Cek notifikasi terbaru (5 terakhir) tanpa join
        $latestNotifications = UserNotifications::where('userId', $userId)
            ->orderBy('createdDate', 'DESC')
            ->take(5)
            ->get(['id', 'message', 'isRead', 'documentId', 'createdDate']);
        // \Log::info("DEBUG - Latest 5 notifications (no join): " . $latestNotifications->toJson());
        
        // DEBUG: Cek apakah notifikasi baru ada
        $newNotificationExists = UserNotifications::where('userId', $userId)
            ->where('createdDate', '>=', '2025-08-15 06:17:00')
            ->exists();
        // \Log::info("DEBUG - New notification exists (after 06:17:00): " . ($newNotificationExists ? 'YES' : 'NO'));

        $query = UserNotifications::select(['userNotifications.*', 'documents.id as documentId', 'documents.name as documentName'])
            ->where('userNotifications.userId', '=', $userId)
            ->leftJoin('documents', function ($join) {
                $join->on('userNotifications.documentId', '=', 'documents.id')
                    ->where('documents.isDeleted', '=', false)
                    ->where('documents.isPermanentDelete', '=', false);
            })
            // GUNAKAN: Unread duluan, lalu terbaru
            ->orderBy('userNotifications.isRead', 'ASC')
            ->orderBy('userNotifications.createdDate', 'DESC');

        // Debug: Log the SQL query
        // \Log::info("getTop10Notification - SQL: " . $query->toSql());
        // \Log::info("getTop10Notification - Bindings: " . json_encode($query->getBindings()));

        $results = $query->take(10)->get();
        
        // \Log::info("getTop10Notification - Found notifications: " . $results->count());
        // \Log::info("getTop10Notification - Notifications: " . $results->toJson());

        return $results;
    }

    public function getUserNotificaions($attributes)
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        // \Log::info("getUserNotificaions - Current user ID: {$userId}");
        
        if ($userId == null) {
            throw new RepositoryException('User does not exist.');
        }
        
        $query = UserNotifications::select(['userNotifications.*', 'documents.id as documentId', 'documents.name as documentName'])
            ->where('userNotifications.userId', '=', $userId)
            ->leftJoin('documents', function ($join) {
                $join->on('userNotifications.documentId', '=', 'documents.id')
                    ->where('documents.isDeleted', '=', false)
                    ->where('documents.isPermanentDelete', '=', false);
            });

        // Debug: Cek total notifications untuk user ini tanpa filter document
        $totalUserNotifications = UserNotifications::where('userId', $userId)->count();
        // \Log::info("getUserNotificaions - Total notifications for user (without document filter): {$totalUserNotifications}");

        $orderByArray = explode(' ', $attributes->orderBy);
        $orderBy = $orderByArray[0];
        $direction = $orderByArray[1] ?? 'asc';

        if ($orderBy == 'message') {
            $query = $query->orderBy('userNotifications.message', $direction);
        }

        if ($orderBy == 'createdDate') {
            $query = $query->orderBy('userNotifications.createdDate', $direction);
        }

        if ($attributes->name) {
            $query = $query->where(function ($query) use ($attributes) {
                $query->where('userNotifications.message', 'like', '%' . $attributes->name . '%')
                    ->orWhere(function ($query) use ($attributes) {
                        $query->where('documents.name', 'like', '%' . $attributes->name . '%');
                    });
            });
        }

        // Debug: Log the SQL before applying pagination
        // \Log::info("getUserNotificaions - SQL: " . $query->toSql());
        // \Log::info("getUserNotificaions - Bindings: " . json_encode($query->getBindings()));
        
        $totalBeforePagination = $query->count();
        // \Log::info("getUserNotificaions - Total before pagination: {$totalBeforePagination}");

        $results = $query->skip($attributes->skip)->take($attributes->pageSize)->get();
        // \Log::info("getUserNotificaions - Results count: " . $results->count());

        return $results;
    }
    public function getUserNotificaionCount($attributes)
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        if ($userId == null) {
            throw new RepositoryException('User does not exist.');
        }
        $query = UserNotifications::query()
            ->where('userNotifications.userId', '=', $userId)
            ->leftJoin('documents', function ($join) {
                $join->on('userNotifications.documentId', '=', 'documents.id')
                    ->where('documents.isDeleted', '=', false)
                    ->where('documents.isPermanentDelete', '=', false);
            });

        if ($attributes->name) {
            $query = $query->where(function ($query) use ($attributes) {
                $query->where('userNotifications.message', 'like', '%' . $attributes->name . '%')
                    ->orWhere(function ($query) use ($attributes) {
                        $query->where('documents.name', 'like', '%' . $attributes->name . '%');
                    });
            });
        }

        $count = $query->count();
        return $count;
    }

    public function markAsRead($request)
    {
        $model = $this->model->find($request->id);
        $model->isRead = true;
        $saved = $model->save();
        $this->resetModel();
        $result = $this->parseResult($model);

        if (!$saved) {
            throw new RepositoryException('Error in saving data.');
        }
        return $result;
    }

    public function markAllAsRead()
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        if ($userId == null) {
            throw new RepositoryException('User does not exist.');
        }

        $userNotifications = UserNotifications::where('userId', $userId)->get();

        foreach ($userNotifications as $userNotification) {
            $userNotification->isRead = true;
            $userNotification->save();
        }

        return;
    }

    public function markAsReadByDocumentId($documentId)
    {
        $userId = Auth::parseToken()->getPayload()->get('userId');
        if ($userId == null) {
            throw new RepositoryException('User does not exist.');
        }

        $userNotifications = UserNotifications::where('userId', '=', $userId)
            ->where('documentId', '=', $documentId)->get();

        foreach ($userNotifications as $userNotification) {
            $userNotification->isRead = true;
            $userNotification->save();
        }
        return;
    }
}
