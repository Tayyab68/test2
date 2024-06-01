<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notificationList(Request $request)
    {
        $totalData = Notification::count();
        $rows = Notification::orderBy('id', 'DESC')->get();
        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'description',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Notification::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = Notification::Where('title', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Notification::Where('title', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
            $repeat = '<a href="#" data-title="' . $item->title . '" data-description="' . $item->description . '" class="me-3 btn btn-info px-4 text-white repeat" rel=' . $item->id . '>' . __('repeat') . '</a>';
            $edit = '<a href="#" data-title="' . $item->title . '" data-description="' . $item->description . '" rel='.$item->id.' class="btn edit btn-success me-3" >' . __('edit') . '</a>'; 
            $delete = '<a href="#" class="btn delete btn-danger text-white" rel=' . $item->id . '>' . __('delete') . '</a>';
            $action = '<span class="float-end">'. $repeat . $edit . $delete .' </span>' ;

            $data[] = [
                $item->title,
                $item->description,
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function sendNotification(Request $request)
    {
        $notification = new Notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->save();

        $title = $request->title;
        $description = $request->description;
      
        GlobalFunction::sendPushNotificationToAllUsers($title, $description);

        return response()->json([
            'status' => true,
            'message' => 'Notification Send Successfully',
        ]);

    }

    public function updateNotification(Request $request)
    {
        $notification = Notification::where('id', $request->notificationID)->first();
        
        if ($notification) {
            $notification->title = $request->title;
            $notification->description = $request->description;
            $notification->save();
 
            return response()->json([
                'status' => true,
                'message' => 'Notification Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Notification Not Found',
            ]);
        }

    }

    public function repeatNotification(Request $request)
    {
        $title = $request->title;
        $description  = $request->description;

        GlobalFunction::sendPushNotificationToAllUsers($title, $description);

        return response()->json([
            'status' => true,
            'message' => 'Notification Send Successfully',
        ]);
    }

    public function deleteNotification(Request $request)
    {
        $notification = Notification::where('id', $request->notification_id)->first();
 
        if ($notification) {
            $notification->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notification Delete Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Notification Not Found',
            ]);
        }
    }

    public function notification()
    {
        return view('notification');
    }

}
