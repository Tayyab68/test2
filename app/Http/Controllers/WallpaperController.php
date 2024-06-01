<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\Wallpaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class WallpaperController extends Controller
{
    public function wallpaper()
    {
        $categories = Category::where('type', 0)->get();
        return view('wallpaper', [
            'categories' => $categories,
        ]);
    }

    public function liveWallpaper()
    {
        $liveCategories = Category::where('type', 1)->get();
        return view('liveWallpaper', [
            'liveCategories' => $liveCategories,
        ]);
    }

    public function wallpaperList(Request $request)
    {
        $totalData = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->count();
        $rows = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->orderBy('id', 'DESC')->get();
        $result = $rows;
        $columns = [
            0 => 'id',
            1 => 'image',
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)
                 ->where(function($query) use ($search){
                        $query->Where('title', 'LIKE', "%{$search}%");
                        })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)
                                        ->where(function($query) use ($search){
                                            $query->Where('title', 'LIKE', "%{$search}%");
                                        })
                                        ->count();
        }
        $data = [];
        foreach ($result as $item) {
  
            $imageUrl = GlobalFunction::createMediaUrl($item->content);

            if ($item->content != null) {
                $image = "<img src=". $imageUrl ." class='tbl_portrait_image'>";
            } else {
                $image = "<img src='./asset/img/placeholder-image.png' class='tbl_portrait_image'>";
            }
            if ($item->content == null) {
                $imageUrl = "null";
            }

            if ($item->access_type == Constants::Premium) {
                $type = '<span class="badge badge-success border-radius-5"> Premium </span>';
            } elseif ($item->access_type == Constants::Locked) {
                $type = '<span class="badge badge-primary border-radius-5"> Locked </span>';
            } else {
                $type = '<span class="badge badge-info border-radius-5"> None </span>';
            } 

            if ($item->is_featured == 1) {
                $featured = '<div class="checkbox-slider">
                <label>
                    <input type="checkbox" class="d-none Featured"  checked rel="' . $item->id . '" value="' . $item->is_featured . '" >
                    <span class="toggle_background">
                        <div class="circle-icon"></div>
                        <div class="vertical_line"></div>
                    </span>
                </label>
            </div>';
            } else {
                $featured = '<div class="checkbox-slider">
                <label>
                    <input type="checkbox" class="d-none Featured"  rel="' . $item->id . '" value="' . $item->is_featured . '" >
                    <span class="toggle_background">
                        <div class="circle-icon"></div>
                        <div class="vertical_line"></div>
                    </span>
                </label>
            </div>';
            }


            $edit = '<a href="#" data-image="'.$imageUrl.'" data-category_id="'.$item->category->id.'" data-tags="'.$item->tags.'" data-access_type="'.$item->access_type.'"   rel='.$item->id.' class="btn edit btn-success me-3">' . __('edit') . '</a>'; 
            $delete = '<a href="#" class="btn delete btn-danger" rel=' . $item->id . '>' . __('delete') . '</a>';
            $action = '<span class="float-end">'. $edit . $delete .' </span>' ;

            $data[] = [   
                $image, 
                $item->category->title,     
                $item->tags,     
                $type,
                $featured,
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

    public function addWallpaper(Request $request)
    {
        foreach($request->file('content') as $thisImage){

            $wallpaper = new Wallpaper();
            $wallpaper->wallpaper_type = $request->wallpaper_type;
            $wallpaper->category_id = $request->category_id;
            $wallpaper->access_type = $request->access_type;
            $wallpaper->tags = $request->tags;

            $path = GlobalFunction::saveFileAndGivePath($thisImage);
            $wallpaper->content = $path;
            
            $wallpaper->save();

        } 

        return response()->json([
            'status' => true,
            'message' => 'Wallpaper Added Successfully', 

        ]);
       
    }

    public function updateWallpaper(Request $request)
    {
        $wallpaper = Wallpaper::where('id', $request->wallpaper_id)->first();

        if ($wallpaper == null) {
            return response()->json([
                'status' => false,
                'message' => 'Wallpaper Not Found',
            ]);
        } else {
            $wallpaper->tags = $request->tags;
            $wallpaper->category_id = $request->category_id;
            $wallpaper->access_type = $request->access_type;
            
            if ($request->hasFile('content')) {
                $path = "./public/storage/" . $wallpaper->content;
                if (File::exists($path)) {
                    File::delete($path);
                }
                $file = $request->file('content');
                $filePath = GlobalFunction::saveFileAndGivePath($file);
                $wallpaper->content = $filePath;
            }

            $wallpaper->save();

            return response()->json([
                'status' => true,
                'message' => 'Wallpaper Updated Successfully',
            ]);
        
        }
    }

    public function deleteWallpaper(Request $request)
    {
        $wallpaper = Wallpaper::where('id', $request->wallpaper_id)->first();
        
        if ($wallpaper == null) {
            return response()->json([
                'status' => false,
                'message' => 'Wallapaper Not Found',
            ]);
        }
        GlobalFunction::deleteFile($wallpaper->content);
        GlobalFunction::deleteFile($wallpaper->thumbnail);
        $wallpaper->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category Delete Successfully',
        ]);
    }

    public function addLiveWallpaper(Request $request)
    {
        $liveWallpaper = new Wallpaper();
        $liveWallpaper->wallpaper_type = $request->wallpaper_type;
        $liveWallpaper->category_id = $request->category_id;
        $liveWallpaper->access_type = $request->access_type;
        $liveWallpaper->tags = $request->tags;

        $thumbnailPath = GlobalFunction::saveFileAndGivePath($request->file('thumbnail'));
        $liveWallpaper->thumbnail = $thumbnailPath;

        $path = GlobalFunction::saveFileAndGivePath($request->file('content'));
        $liveWallpaper->content = $path;
        
        $liveWallpaper->save();

        return response()->json([
            'status' => true,
            'message' => 'Live wallpaper added Successfully', 
            'data' => $liveWallpaper, 
        ]);
       
    }

    public function liveWallpaperList(Request $request)
    {
        $totalData = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->count();
        $rows = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->orderBy('id', 'DESC')->get();
        $result = $rows;
        $columns = [
            0 => 'id',
            1 => 'image',
        ];
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)
                // ->Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Wallpaper::Where('wallpaper_type', $request->wallpaper_type)
                                        // ->Where('title', 'LIKE', "%{$search}%")
                                        ->count();
        }
        $data = [];
        foreach ($result as $item) {

            $thumbnailUrl = GlobalFunction::createMediaUrl($item->thumbnail);
  
            if ($item->thumbnail != null) {
                $thumbnail = "<img src=". $thumbnailUrl ." class='tbl_portrait_image'>";
            } else {
                $thumbnail = "<img src='./asset/img/placeholder-image-portrait.png' class='tbl_portrait_image'>";
            }
            if ($item->thumbnail == null) {
                $thumbnailUrl = "null";
            }

            if ($item->access_type == Constants::Premium) {
                $type = '<span class="badge badge-success border-radius-5"> Premium </span>';
            } elseif ($item->access_type == Constants::Locked) {
                $type = '<span class="badge badge-primary border-radius-5"> Locked </span>';
            } else {
                $type = '<span class="badge badge-info border-radius-5"> None </span>';
            }

            if ($item->is_featured == 1) {
                $featured = '<div class="checkbox-slider">
                <label>
                    <input type="checkbox" class="d-none Featured"  checked rel="' . $item->id . '" value="' . $item->is_featured . '" >
                    <span class="toggle_background">
                        <div class="circle-icon"></div>
                        <div class="vertical_line"></div>
                    </span>
                </label>
            </div>';
            } else {
                $featured = '<div class="checkbox-slider">
                <label>
                    <input type="checkbox" class="d-none Featured"  rel="' . $item->id . '" value="' . $item->is_featured . '" >
                    <span class="toggle_background">
                        <div class="circle-icon"></div>
                        <div class="vertical_line"></div>
                    </span>
                </label>
            </div>';
            }

            $liveWallpaperUrl = GlobalFunction::createMediaUrl($item->content);
            
            $liveWallpaper = '<a href="javascript:;" rel="'. $item->id .'" data-live_wallpaper="'. $liveWallpaperUrl .'" class="me-2  btn-primary text-white liveWallpaperUrl px-3 py-3 border-radius">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1 me-1 "><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
            Preview</a>';

            $edit = '<a href="#" data-content="'.$liveWallpaperUrl.'" data-thumbnail="'.$thumbnailUrl.'" data-category_id="'.$item->category->id.'" data-tags="'.$item->tags.'" data-access_type="'.$item->access_type.'"   rel='.$item->id.' class="btn edit btn-success me-3">' . __('edit') . '</a>'; 
            $delete = '<a href="#" class="btn delete btn-danger" rel=' . $item->id . '>' . __('delete') . '</a>';
            $action = '<span class="float-end">'. $edit . $delete .' </span>' ;

            $data[] = [   
                $thumbnail,
                $liveWallpaper,
                $item->category->title,     
                $item->tags,     
                $type,
                $featured,
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

    public function updateLiveWallpaper(Request $request)
    {
        $wallpaper = Wallpaper::where('id', $request->wallpaper_id)->first();

        if ($wallpaper == null) {
            return response()->json([
                'status' => false,
                'message' => 'Wallpaper Not Found',
            ]);
        } else {
            $wallpaper->tags = $request->tags;
            $wallpaper->category_id = $request->category_id;
            $wallpaper->access_type = $request->access_type;
  
             if ($request->hasFile('thumbnail')) {
                GlobalFunction::deleteFile($wallpaper->thumbnail);
                $file = $request->file('thumbnail');
                $thumbnailPath = GlobalFunction::saveFileAndGivePath($file);
                $wallpaper->thumbnail = $thumbnailPath;
            }
            
            if ($request->hasFile('content')) {
                GlobalFunction::deleteFile($wallpaper->content);
                $file = $request->file('content');
                $filePath = GlobalFunction::saveFileAndGivePath($file);
                $wallpaper->content = $filePath;
            }

            $wallpaper->save();

            return response()->json([
                'status' => true,
                'message' => 'Live wallpaper Updated Successfully',
            ]);
        
        }
    }

    public function updateFeatured(Request $request)
    {
        $wallpaper = Wallpaper::where('id', $request->id)->first();
        if ($wallpaper == null) {
            return response()->json([
                'status' => false,
                'message' => 'Wallpaper not found',
            ]);            
        }
        
        $wallpaper->is_featured = $request->is_featured;
        $wallpaper->save();

        return response()->json([
            'status' => true,
            'message' => 'Wallpaper Updated Successfully',
        ]);
    }

}
