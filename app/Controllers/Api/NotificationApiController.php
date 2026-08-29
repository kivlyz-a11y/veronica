<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\InternalNotificationModel;

class NotificationApiController extends BaseController
{
    protected InternalNotificationModel $notifModel;

    public function __construct()
    {
        $this->notifModel = new InternalNotificationModel();
    }

    public function getUnread()
    {
        $userId = session()->get('user_id');
        $unreadCount = $this->notifModel->countUnread($userId);
        $items = $this->notifModel->getUnreadForUser($userId, 5);

        return $this->response->setJSON([
            'success' => true,
            'count'   => $unreadCount,
            'items'   => $items,
        ]);
    }

    public function markAsRead(int $id)
    {
        $this->notifModel->update($id, ['is_read' => 1]);
        return $this->response->setJSON(['success' => true]);
    }
}
