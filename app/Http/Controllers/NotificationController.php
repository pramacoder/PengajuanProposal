<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifications for the authenticated user
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Get user type
        $userType = $this->getUserType($user);
        
        // Get user identifier based on user type
        $userIdentifier = $this->getUserIdentifier($user, $userType);
        
        if (!$userIdentifier) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }
        
        // Get notifications from database
        $notifications = Notification::where('user_identifier', $userIdentifier)
                                   ->where('user_type', $userType)
                                   ->orderBy('created_at', 'desc')
                                   ->take(50)
                                   ->get()
                                   ->map(function ($notification) {
                                       return [
                                           'id' => $notification->id,
                                           'type' => $notification->type,
                                           'title' => $notification->title,
                                           'message' => $notification->message,
                                           'time' => $notification->time_ago,
                                           'unread' => $notification->isUnread(),
                                           'actions' => $this->getNotificationActions($notification),
                                           'data' => $notification->data ?? [],
                                           'proposal_id' => $notification->proposal_id,
                                           'created_at' => $notification->created_at->toISOString()
                                       ];
                                   });

        $unreadCount = Notification::where('user_identifier', $userIdentifier)
                                  ->where('user_type', $userType)
                                  ->whereNull('read_at')
                                  ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|integer'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userType = $this->getUserType($user);
        $userIdentifier = $this->getUserIdentifier($user, $userType);
        
        if (!$userIdentifier) {
            return response()->json(['success' => false, 'message' => 'User identifier not found'], 400);
        }

        $notification = Notification::where('id', $request->notification_id)
                                  ->where('user_identifier', $userIdentifier)
                                  ->where('user_type', $userType)
                                  ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userType = $this->getUserType($user);
        $userIdentifier = $this->getUserIdentifier($user, $userType);
        
        if (!$userIdentifier) {
            return response()->json(['success' => false, 'message' => 'User identifier not found'], 400);
        }
        
        Notification::where('user_identifier', $userIdentifier)
                   ->where('user_type', $userType)
                   ->whereNull('read_at')
                   ->update(['read_at' => Carbon::now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get user type
     */
    private function getUserType($user): string
    {
        // Check if user has role method (User model)
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('mahasiswa')) {
                return 'mahasiswa';
            } elseif ($user->hasRole('dosen')) {
                return 'dosen';
            } elseif ($user->hasRole('reviewer')) {
                return 'reviewer';
            } elseif ($user->hasRole('operator')) {
                return 'operator';
            }
        }
        
        // Check based on model class name
        $className = get_class($user);
        if (str_contains($className, 'Mahasiswa')) {
            return 'mahasiswa';
        } elseif (str_contains($className, 'Dosen')) {
            return 'dosen';
        } elseif (str_contains($className, 'Reviewer')) {
            return 'reviewer';
        } elseif (str_contains($className, 'PT')) {
            return 'operator';
        }
        
        return 'user';
    }

    /**
     * Get user identifier based on user type
     */
    private function getUserIdentifier($user, string $userType): ?string
    {
        switch ($userType) {
            case 'mahasiswa':
                return $user->nim ?? $user->id_mahasiswa ?? null;
            case 'dosen':
                return $user->nidn ?? $user->id_dosen ?? null;
            case 'reviewer':
                return $user->id_reviewer ?? null;
            case 'operator':
                return $user->id_pt ?? $user->id_operator ?? null;
            default:
                return null;
        }
    }

    /**
     * Get notification actions based on notification data
     */
    private function getNotificationActions(Notification $notification): array
    {
        $actions = [];
        
        if (isset($notification->data['action_text'])) {
            $actions[] = $notification->data['action_text'];
        }
        
        if ($notification->proposal_id) {
            $actions[] = 'Lihat Detail';
        }
        
        if (empty($actions)) {
            $actions[] = 'Lihat';
        }
        
        return $actions;
    }










}
